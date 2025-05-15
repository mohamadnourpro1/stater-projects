<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
  use ApiResponse;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return $this->apiresponse(RoleResource::collection($roles),'Roles Fetched Successfully',200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);
        $role = Role::create(['name' => $request->name,'guard_name' => 'web']);
        if($role){
          $role->givePermissionTo($data['permissions']);
          return $this->apiresponse($role,'Role Created Successfully',200);
        }
        return $this->apiresponse(null,'Role Not Created',400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $roles = Role::with('permissions')->find($id);
        if(!$roles){
          return $this->apiresponse(null,'Role Not Found',404);
        }
        return $this->apiresponse(new RoleResource($roles),'Role Fetched Successfully',200);
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
        $roles = Role::find($id);
        if(!$roles){
          return $this->apiresponse(null,'Role Not Found',404);
        }
        if($request->permissions){
          $data = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name'
          ]);
          $roles->syncPermissions($data['permissions']);
        }
        if($request->name){
          $roles->update(['name' => $request->name,'guard_name' => 'web']);
        }
        return $this->apiresponse(new RoleResource($roles),'Role Updated Successfully',200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $roles = Role::find($id);
        if(!$roles){
          return $this->apiresponse(null,'Role Not Found',404);
        }
        $roles->delete();
        return $this->apiresponse(null,'Role Deleted Successfully',200);
    }
}
