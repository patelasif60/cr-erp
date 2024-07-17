<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use Auth;
use DB;

class UserApiController extends Controller
{
    public function getUser(Request $request)
    {
        $header = $request->header('Authorization');
        $user_id = str_replace('Bearer ', '', $header);
        $user = User::find($request->user_id);
        $roles_permissions_wms = DB::table('roles_permissions')->where('type','wms')->orderBy('sorting_order')->get();
        if ($user) {
          $response = [
                 'token' => base64_encode($user->id),
                 'user_id' => $user->id,
                 'user_name' => $user->name,
                 'user_email' => $user->email,
                 'user_role_id' => $user->role,
                 'user_role' => $user->user_role['role'],
                 'permissions' => $roles_permissions_wms,
                 'warehouse_id' => $user->wh_id,
                 'warehouse_name' => isset($user->warehouse->id) ? $user->warehouse->warehouses : null
           ];
         $response = ["error" => false,"message" => "Success","data" => $response];
         return response($response, 200);
        }
        else{
          return response(["error" => true,"message" => "Data not Found"], 400);
        }
    }

    public function updateWarehouse(Request $request){
      $user_id = $request->user_id;
      $warehouse_id = $request->warehouse_id;

      $user = User::find($user_id);
      $user->wh_id = $warehouse_id;
      $user->save();
      $response = ["error" => false,"message" => "Success"];
      return response($response, 200);
    }
}
