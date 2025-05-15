<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserRolesResource;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
  use ApiResponse;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('roles')->get();
        return $this->apiresponse(UserRolesResource::collection($users),'Users Fetched Successfully',200);
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
          'user'=> 'required|exists:users,name',
          'role'=> 'required|exists:roles,name',
        ]);
        $user = User::where('name',$data['user'])->first();
        $role = Role::where('name',$data['role'])->first();
        if($user && $role){
          $user->assignRole($role);
          return $this->apiresponse($user,'Role Assigned Successfully',200);
        }
        return $this->apiresponse(null,'Role Not Assigned',400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with('roles')->find($id);
        if(!$user){
          return $this->apiresponse(null,'user Not Found',404);
        }
        return $this->apiresponse(new UserRolesResource($user),'Role Fetched Successfully',200);
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
        $user = User::find($id);
        if(!$user){
          return $this->apiresponse(null,'user Not Found',404);
        }
        $data = $request->validate([
          'role'=> 'required|exists:roles,name',
        ]);
        $role = Role::where('name',$data['role'])->first();
        if($user && $role){
          $user->assignRole($role);
          return $this->apiresponse($user,'Role Updated Successfully',200);
        }
        return $this->apiresponse(null,'Role Not Updated',400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if(!$user){
          return $this->apiresponse(null,'user Not Found',404);
        }
        $user->syncRoles([]);
        return $this->apiresponse($user,'Roles Removed Successfully',200);
    }
}
