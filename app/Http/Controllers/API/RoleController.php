<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;
use DB;
use Auth;
class RoleController extends BaseController
{
    // function __construct()
    // {
    //     //  $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
    //     //  $this->middleware('permission:role-create', ['only' => ['create','store']]);
    //     //  $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
    //     //  $this->middleware('role:admin', ['only' => ['index']]);
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::where('name', 'NOT LIKE', '%super-admin%')->orderBy('id','ASC')->get();
        // $roles = Role::orderBy('id','DESC')->paginate(5);
        return $this->sendResponse($roles, '');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required', //must be an array
            ]);
            $roles = Role::create(['name' => $request->input('name')]);
            $roles->syncPermissions($request->input('permission'));

            return $this->sendResponse($roles, 'Role created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::findOrFail($id);
        $role->permissions;
        return $this->sendResponse($role, '');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required']);

            $role = Role::find($id);
            $role->name = $request->input('name');
            
            $role->save();
            $role->syncPermissions($request->input('permission'));
            return $this->sendResponse($role, 'Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(DB::table("roles")->where('id',$id)->delete()){
            return $this->sendResponse($id, 'Role deleted successfully');
        }
        return $this->sendError('A problem occurred, contact Administrator');
    }
}
