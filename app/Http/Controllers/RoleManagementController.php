<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleManagementController extends Controller
{

    public function index()
    {
        if (!auth()->user()->can('manage roles')) {
            abort(403);
        }

        return view('admin.roles.index');
    }

    public function userRoles()
    {
//        if (!auth()->user()->can('manage users')) {
//            abort(403);
//        }

        return view('admin.users.roles');
    }
}
