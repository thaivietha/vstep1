<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Traits\FileUploadTrait;
use App\Http\Requests\Admin\StoreStudentsRequest;
use App\Http\Requests\Admin\StoreTeachersRequest;
use App\Http\Requests\Admin\UpdateTeachersRequest;
use App\Models\Auth\User;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\DataTables;

class StudentsController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of Category.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('backend.students.index');
    }

    /**
     * Display a listing of Courses via ajax DataTable.
     *
     * @return \Illuminate\Http\Response
     */
    public function getData(Request $request)
    {
        $has_view = false;
        $has_delete = false;
        $has_edit = false;
        $teachers = "";


        if (request('show_deleted') == 1) {
            $students = User::query()->role('student')->onlyTrashed()->orderBy('created_at', 'desc');
        } else {
            $students = User::query()->role('student')->orderBy('created_at', 'desc');
        }

        if (auth()->user()->isAdmin() || auth()->user()->hasRole('manager')) {
            $has_view = true;
            $has_edit = true;
            $has_delete = true;
        }


        return DataTables::of($students)
            ->addIndexColumn()
            ->addColumn('actions', function ($q) use ($has_view, $has_edit, $has_delete, $request) {
                $view = "";
                $edit = "";
                $delete = "";
                if ($request->show_deleted == 1) {
                    return view('backend.datatable.action-trashed')->with(['route_label' => 'admin.students', 'label' => 'id', 'value' => $q->id]);
                }

//                if ($has_view) {
//                    $view = view('backend.datatable.action-view')
//                        ->with(['route' => route('admin.students.show', ['student' => $q->id])])->render();
//                }

                if ($has_edit) {
                    $edit = view('backend.datatable.action-edit')
                        ->with(['route' => route('admin.students.edit', ['student' => $q->id])])
                        ->render();
                    $view .= $edit;
                }

                if ($has_delete) {
                    $delete = view('backend.datatable.action-delete')
                        ->with(['route' => route('admin.students.destroy', ['student' => $q->id])])
                        ->render();
                    $view .= $delete;
                }

                $view .= '<a class="btn btn-warning mb-1" href="' . route('admin.orders.index', ['student_id' => $q->id]) . '">' . trans('labels.backend.courses.title') . '</a>';

                return $view;
            })
            ->addColumn('status', function ($q) {
                $html = html()->label(html()->checkbox('')->id($q->id)
                ->checked(($q->active == 1) ? true : false)->class('switch-input')->attribute('data-id', $q->id)->value(($q->active == 1) ? 1 : 0).'<span class="switch-label"></span><span class="switch-handle"></span>')->class('switch switch-lg switch-3d switch-primary');
                return $html;
                // return ($q->active == 1) ? "Enabled" : "Disabled";
            })
            ->rawColumns(['actions', 'image', 'status'])
            ->make();
    }

    /**
     * Show the form for creating new Category.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.students.create');
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param  \App\Http\Requests\StoreStudentsRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStudentsRequest $request)
    {
        $student = User::create($request->all());
        $student->confirmed = 1;
//        if ($request->image) {
//            $teacher->avatar_type = 'storage';
//            $teacher->avatar_location = $request->image->store('/avatars', 'public');
//        }
        $student->active = isset($request->active)?1:0;
        $student->save();
        $student->assignRole('student');

//        $payment_details = [
//            'bank_name'         => request()->payment_method == 'bank'?request()->bank_name:'',
//            'ifsc_code'         => request()->payment_method == 'bank'?request()->ifsc_code:'',
//            'account_number'    => request()->payment_method == 'bank'?request()->account_number:'',
//            'account_name'      => request()->payment_method == 'bank'?request()->account_name:'',
//            'paypal_email'      => request()->payment_method == 'paypal'?request()->paypal_email:'',
//        ];
//        $data = [
//            'user_id'           => $student->id,
////            'facebook_link'     => request()->facebook_link,
////            'twitter_link'      => request()->twitter_link,
////            'linkedin_link'     => request()->linkedin_link,
////            'payment_method'    => request()->payment_method,
////            'payment_details'   => json_encode($payment_details),
//            'description'       => request()->description,
//        ];
//        TeacherProfile::create($data);


        return redirect()->route('admin.students.index')->withFlashSuccess(trans('alerts.backend.general.created'));
    }


    /**
     * Show the form for editing Category.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $student = User::findOrFail($id);
        return view('backend.students.edit', compact('student'));
    }

    /**
     * Update Category in storage.
     *
     * @param  \App\Http\Requests\UpdateTeachersRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTeachersRequest $request, $id)
    {

        $teacher = User::findOrFail($id);
        $teacher->update($request->except('email'));
        if ($request->has('image')) {
            $teacher->avatar_type = 'storage';
            $teacher->avatar_location = $request->image->store('/avatars', 'public');
        }
        $teacher->active = isset($request->active)?1:0;
        $teacher->save();

        $payment_details = [
            'bank_name'         => request()->payment_method == 'bank'?request()->bank_name:'',
            'ifsc_code'         => request()->payment_method == 'bank'?request()->ifsc_code:'',
            'account_number'    => request()->payment_method == 'bank'?request()->account_number:'',
            'account_name'      => request()->payment_method == 'bank'?request()->account_name:'',
            'paypal_email'      => request()->payment_method == 'paypal'?request()->paypal_email:'',
        ];
        $data = [
            // 'user_id'           => $user->id,
            'facebook_link'     => request()->facebook_link,
            'twitter_link'      => request()->twitter_link,
            'linkedin_link'     => request()->linkedin_link,
            'payment_method'    => request()->payment_method,
            'payment_details'   => json_encode($payment_details),
            'description'       => request()->description,
        ];
        $teacher->teacherProfile->update($data);


        return redirect()->route('admin.teachers.index')->withFlashSuccess(trans('alerts.backend.general.updated'));
    }


    /**
     * Display Category.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $student = User::findOrFail($id);

        return view('backend.students.show', compact('student'));
    }


    /**
     * Remove Category from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $student = User::findOrFail($id);

        if ($student->courses->count() > 0) {
            return redirect()->route('admin.students.index')->withFlashDanger(trans('alerts.backend.general.teacher_delete_warning'));
        } else {
            $student->delete();
        }

        return redirect()->route('admin.students.index')->withFlashSuccess(trans('alerts.backend.general.deleted'));
    }

    /**
     * Delete all selected Category at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if ($request->input('ids')) {
            $entries = User::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore Category from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $student = User::onlyTrashed()->findOrFail($id);
        $student->restore();

        return redirect()->route('admin.students.index')->withFlashSuccess(trans('alerts.backend.general.restored'));
    }

    /**
     * Permanently delete Category from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        $teacher = User::onlyTrashed()->findOrFail($id);
        $teacher->teacherProfile->delete();
        $teacher->forceDelete();

        return redirect()->route('admin.teachers.index')->withFlashSuccess(trans('alerts.backend.general.deleted'));
    }


    /**
     * Update teacher status
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     **/
    public function updateStatus()
    {
        $teacher = User::find(request('id'));
        $teacher->active = $teacher->active == 1? 0 : 1;
        $teacher->save();
    }
}
