<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUsersRequest;
use App\Http\Requests\Admin\UpdateUsersRequest;

class UsersController extends Controller
{
    /**
     * Display a listing of User.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Gate::allows('user_access')) {
            return abort(401);
        }

        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating new User.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Gate::allows('user_create')) {
            return abort(401);
        }
        $roles = Role::get()->pluck('title', 'id');

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  \App\Http\Requests\StoreUsersRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUsersRequest $request)
    {
        if (!Gate::allows('user_create')) {
            return abort(401);
        }
        $user = User::create($request->all());
        $user->role()->sync(array_filter((array)$request->input('role')));


        return redirect()->route('admin.users.index');
    }


    /**
     * Show the form for editing User.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Gate::allows('user_edit')) {
            return abort(401);
        }
        $roles = Role::get()->pluck('title', 'id');

        $user = User::findOrFail($id);

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update User in storage.
     *
     * @param  \App\Http\Requests\UpdateUsersRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUsersRequest $request, $id)
    {
        if (!Gate::allows('user_edit')) {
            return abort(401);
        }
        $user = User::findOrFail($id);
        $user->update($request->all());
        $user->role()->sync(array_filter((array)$request->input('role')));


        return redirect()->route('admin.users.index');
    }


    /**
     * Display User.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Gate::allows('user_view')) {
            return abort(401);
        }
        $roles = Role::get()->pluck('title', 'id');
        $courses = \App\Models\Course::whereHas('teachers',
            function ($query) use ($id) {
                $query->where('id', $id);
            })->get();

        $user = User::findOrFail($id);

        return view('admin.users.show', compact('user', 'courses'));
    }


    /**
     * Remove User from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Gate::allows('user_delete')) {
            return abort(401);
        }
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index');
    }

    /**
     * Delete all selected User at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (!Gate::allows('user_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = User::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Search User.
     *
     * @param Request $request
     */
    public function search(Request $request)
    {
        $searchTerm = $request->input('q');
        $page = $request->input('page', 1);
        $perPage = 10; // Số người dùng trên mỗi trang

        $users = \App\Models\Auth\User::select('id', 'username', 'email')
            ->whereHas('roles', function ($q) {
                $q->whereNotIn('role_id', [1, 2, 5]);
            })
            ->where(function ($query) use ($searchTerm) {
                $query->where('username', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
            })
            ->orderBy('username', 'asc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $formattedUsers = $users->map(function ($user) {
            return ['id' => $user->id, 'text' => $user->username . ' - (' . $user->email . ')'];
        });

        $moreResults = count($formattedUsers) === $perPage;

        return response()->json(['results' => $formattedUsers, 'pagination' => ['more' => $moreResults]]);
    }

}
