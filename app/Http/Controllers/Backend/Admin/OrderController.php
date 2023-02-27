<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helpers\General\EarningHelper;
use App\Http\Requests\Admin\StoreCategoriesRequest;
use App\Http\Requests\Admin\StoreOrdersRequest;
use App\Mail\OfflineOrderMail;
use App\Models\Auth\User;
use App\Models\Bundle;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use mysql_xdevapi\Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Response;


class OrderController extends Controller
{

    /**
     * Display a listing of Orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (!Gate::allows('order_access')) {
            return abort(401);
        }
        $orders = Order::get();
        return view('backend.orders.index', compact('orders'));
    }

    /**
     * Display a listing of Orders via ajax DataTable.
     *
     * @return \Illuminate\Http\Response
     */
    public function getData(Request $request)
    {
        $has_view = false;
        $has_delete = false;
        $has_edit = false;
        $orders = "";

        if (request('offline_requests') == 1) {

            $orders = Order::query()->where('payment_type', '=', 3)->orderBy('updated_at', 'desc');
        } else {
            $orders = Order::query()->orderBy('updated_at', 'desc');
        }

        if (auth()->user()->can('order_view')) {
            $has_view = true;
        }
        if (auth()->user()->can('order_edit')) {
            $has_edit = true;
        }
        if (auth()->user()->can('order_delete')) {
            $has_delete = true;
        }

        return DataTables::of($orders)
            ->addIndexColumn()
            ->addColumn('actions', function ($q) use ($request) {
                $view = "";
                $edit = "";
                $delete = "";
                $allow_delete = false;

                $view = view('backend.datatable.action-view')
                    ->with(['route' => route('admin.orders.show', ['order' => $q->id])])->render();

                if ($q->status == 0) {
                    $complete_order = view('backend.datatable.action-complete-order')
                        ->with(['route' => route('admin.orders.complete', ['order' => $q->id])])
                        ->render();
                    $view .= $complete_order;
                }

                if ($q->status == 0) {
                    $delete = view('backend.datatable.action-delete')
                    ->with(['route' => route('admin.orders.destroy', ['order' => $q->id])])
                    ->render();

                    $view .= $delete;
                }

                return $view;

            })
            ->addColumn('items', function ($q) {
                $items = "";
                foreach ($q->items as $key => $item) {
                    if($item->item != null){
                        $key++;
                        $items .= $key . '. ' . $item->item->title . "<br>";
                    }

                }
                return $items;
            })
            ->addColumn('user_email', function ($q) {
                return $q->user->email;
            })
            ->addColumn('date', function ($q) {
                return $q->updated_at->diffforhumans();
            })
            ->addColumn('payment', function ($q) {
                if ($q->status == 0) {
                    $payment_status = trans('labels.backend.orders.fields.payment_status.pending');
                } elseif ($q->status == 1) {
                    $payment_status = trans('labels.backend.orders.fields.payment_status.completed');
                } else {
                    $payment_status = trans('labels.backend.orders.fields.payment_status.failed');
                }
                return $payment_status;
            })
            ->editColumn('price', function ($q) {
                return '$' . floatval($q->price);
            })
            ->editColumn('amount', function ($q) {
                return  number_format($q->amount);
            })
            ->rawColumns(['items', 'actions'])
            ->make();
    }

    /**
     * Complete Order manually once payment received.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function create(){
        if (!Gate::allows('order_create')) {
            return abort(401);
        }
        $courses = Course::has('category')->ofTeacher()->get()->pluck('title', 'id');
//        $users = User::get()->pluck('username', 'id')->prepend('Please select', '');
        $users = \App\Models\Auth\User::whereHas('roles', function ($q) {
            $q->where('role_id','<>', 1)
                ->where('role_id', '<>',2);
        })->get()->pluck('name', 'id');

        return view('backend.orders.create', compact('courses', 'users'));
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param  \App\Http\Requests\StoreOrdersRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrdersRequest $request)
    {
        if (!Gate::allows('order_create')) {
            return abort(401);
        }

        $courses = Course::whereIN('id',$request->course_id)->get();
        if ($this->checkDuplicate($request->user_id,$courses)) {
            return $this->checkDuplicate($request->user_id,$courses);
        }

        $order = new Order();
        $order->user_id = $request->user_id;
        $order->reference_no = str_random(8);
        $order->coupon_id =  0 ;
        $order->save();

        $content = [];
        $items = [];
        $amount = 0;
        $counter = 0;

        //Getting and Adding items
        foreach ($courses as $item) {
            $counter++;
            $type = Course::class;
            $amount += $item->price;
            $order->items()->create([
                'item_id' => $item->id,
                'item_type' => $type,
                'price' => $item->price
            ]);

            array_push($items, ['number' => $counter, 'name' => $item->name, 'price' => $item->price]);
        }
        $content['items'] = $items;
        $content['total'] =  number_format($amount);
        $content['reference_no'] = $order->reference_no;

        try {
            $user = User::find($request->user_id);
            \Mail::to($user->email)->send(new OfflineOrderMail($content));
            $this->adminOrderMail($order);
        } catch (\Exception $e) {
            \Log::info($e->getMessage() . ' for order ' . $order->id);
        }

        $order->payment_type = 3;
        $order->amount = $amount;
        $order->status = 1;
        $order->save();

        try{
            (new EarningHelper)->insert($order);

            //Generating Invoice
            generateInvoice($order);

            foreach ($order->items as $orderItem) {
                //Bundle Entries
                if($orderItem->item_type == Bundle::class){
                    foreach ($orderItem->item->courses as $course){
                        $course->students()->attach($order->user_id);
                    }
                }
                $orderItem->item->students()->attach($order->user_id);
            }
        }catch (\Exception $exception){
            \Log::error($exception->getMessage());
        }


        return redirect()->route('admin.orders.index')->withFlashSuccess(trans('alerts.backend.general.created'));
    }

    public function complete(Request $request)
    {
//        dd('check');
        $order = Order::findOrfail($request->order);
        $order->status = 1;
        $order->save();

        try{
            (new EarningHelper)->insert($order);

            //Generating Invoice
            generateInvoice($order);

            foreach ($order->items as $orderItem) {
                //Bundle Entries
                if($orderItem->item_type == Bundle::class){
                    foreach ($orderItem->item->courses as $course){
                        $course->students()->attach($order->user_id);
                    }
                }
                $orderItem->item->students()->attach($order->user_id);
            }
        }catch (\Exception $exception){
            \Log::error($exception->getMessage());
        }

        return back()->withFlashSuccess(trans('alerts.backend.general.updated'));
    }

    /**
     * Show Order from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return view('backend.orders.show', compact('order'));
    }

    /**
     * Remove Order from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $order = Order::findOrFail($id);
        $order->items()->delete();
        $order->delete();
        return redirect()->route('admin.orders.index')->withFlashSuccess(trans('alerts.backend.general.deleted'));
    }

    /**
     * Delete all selected Orders at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (!Gate::allows('course_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Order::whereIn('id', $request->input('ids'))->get();
            foreach ($entries as $entry) {
                if ($entry->status = 1) {
                    foreach ($entry->items as $item) {
                        $item->course->students()->detach($entry->user_id);
                    }
                    $entry->items()->delete();
                    $entry->delete();
                }
            }
        }
    }

    private function checkDuplicate($user_id,$courses)
    {
        $is_duplicate = false;
        $message = '';
        $orders = Order::where('user_id', '=', $user_id)->pluck('id');
        $order_items = OrderItem::whereIn('order_id', $orders)->get(['item_id', 'item_type']);
        foreach ($courses as $cartItem) {
            foreach ($order_items->where('item_type', 'App\Models\Course') as $item) {
                if ($item->item_id == $cartItem->id) {
                    $is_duplicate = true;
                    $message .= $cartItem->title . ' ' . __('alerts.frontend.duplicate_course') . '\n';
                }
            }
        }
        if ($is_duplicate) {
            return redirect()->back()->with('flash_danger', $message);
        }
        return false;
    }
}
