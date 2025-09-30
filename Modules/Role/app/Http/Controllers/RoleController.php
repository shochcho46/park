<?php

namespace Modules\Role\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function roleIndex(Request $request)
    {
        $limit = $request->limit ?? 20;
        $datas = Role::orderBy('id','desc')->where('name','!=','SuperAdmin')
                    ->when($request->search, function($query) use ($request){
                        $query->where('name','like','%'.$request->search.'%');
                    })
                    ->paginate($limit);
        return view('role::role.index',compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function roleCreate()
    {
        return view('role::role.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function roleStore(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        Role::create($validatedData);

        $toaster = [
            'message' => 'Role created successfully!',
            'alert-type' => 'success'
        ];

        return redirect()->route('admin.roleIndex')->with($toaster);

    }


    public function roleEdit(Role $role)
    {
        $data = $role;
        return view('role::role.edit',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function roleUpdate(Request $request, Role $role): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id, // Exclude current role from unique check
        ]);


        // Update the role with validated data
        $role->update($validatedData);

        $toaster = [
            'message' => 'Role updated successfully!',
            'alert-type' => 'success'
        ];

        return redirect()->route('admin.roleIndex')->with($toaster);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function roleDestroy(Role $role)
    {

        $role->delete();

        $toaster = [
            'message' => 'Role deleted successfully!',
            'alert-type' => 'success'
        ];

        return redirect()->route('admin.roleIndex')->with($toaster);
    }

    public function roleWithPermission(Role $role)
    {
        $role = $role;
        $permissions = Permission::all();
        $assignedPermissions = $role->permissions->pluck('id')->toArray();
        return view('role::role.assing',compact('role', 'permissions', 'assignedPermissions'));
    }

    public function roleWithPermissionStore(Request $request, Role $role)
    {
        $request->validate([
            'permission_list' => 'required|array',
        ]);
        $permissionName = Permission::whereIn('id',$request->permission_list)->pluck('name');
        $role = Role::find($role->id);

        if (!empty($role) &&  $role != null) {
            $role->syncPermissions($permissionName);
            $toaster = [
                'message' => 'Permission assigned successfully!',
                'alert-type' => 'success'
            ];
        }
        else
        {
            $toaster = [
                'message' => 'Error in Permission assigned',
                'alert-type' => 'error'
            ];
        }


        return redirect()->route('admin.roleIndex')->with($toaster);
    }


    public function permissionIndex(Request $request)
    {
        $limit = $request->limit ?? 20;
        $datas = Permission::orderBy('id','desc')
                    ->when($request->search, function($query) use ($request){
                        $query->where('name','like','%'.$request->search.'%');
                    })
                    ->paginate($limit);
        return view('role::permission.index',compact('datas'));
    }

    public function permissionCreate()
    {
        return view('role::permission.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function permissionStore(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        Permission::create($validatedData);

        $toaster = [
            'message' => 'Permission created successfully!',
            'alert-type' => 'success'
        ];

        return redirect()->route('admin.permissionIndex')->with($toaster);

    }


    public function permissionEdit(Permission $permission)
    {
        $data = $permission;
        return view('role::permission.edit',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function permissionUpdate(Request $request, Permission $permission): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:roles,name,' . $permission->id, // Exclude current role from unique check
        ]);


        // Update the role with validated data
        $permission->update($validatedData);

        $toaster = [
            'message' => 'Permission updated successfully!',
            'alert-type' => 'success'
        ];

        return redirect()->route('admin.permissionIndex')->with($toaster);
    }


    public function permissionDestroy(Permission $permission)
    {

        $permission->delete();

        $toaster = [
            'message' => 'Permission deleted successfully!',
            'alert-type' => 'success'
        ];

        return redirect()->route('admin.permissionIndex')->with($toaster);
    }
}
