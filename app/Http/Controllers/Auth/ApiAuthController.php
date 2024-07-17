<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Resources\Auth\AuthResource;
use Auth;
use DB;

class ApiAuthController extends Controller
{
    public function login (Request $request) {
        error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:255',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $email = $request->email;
        $user = User::where(function($q) use($email){
            $q->where('email', $email);
            $q->Orwhere('username', $email);
        })->with('user_role')->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $roles_permissions_wms = DB::table('roles_permissions')->where('type','wms')->orderBy('sorting_order')->get();
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
                $response = ["error" => false,"message" => "Login successfully","data" => $response];
                return response($response, 200);
            } else {
                $response = ["error" => true,"message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["error" => true,"message" =>'User does not exist'];
            return response($response, 422);
        }
    }
    public function logout (Request $request) {
        // $token = Auth::user()->accessTokens();
        // $token->delete();
        $response = ["error" => false,'message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
    public function changePassword(Request $request)
    {
        
        try 
        {
            $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'id' => 'required',
            'new_password' => 'required|confirmed',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                $error = $validator->errors()->all(':message');
                $response = [
                    'error' => true,
                    'message' => $error[0],
                    'data' => $errors
                ];
                return response()->json($response,400);  
            }
            $user = User::find($id);
            $hashedPassword = $request->old_password;
            if (Hash::check($hashedPassword , $user->password)) {
                $request->user()->fill([
                    'password' => Hash::make($request->new_password)
                ])->save();
            }
            else{
                return response()->json([
                    'error' => true,
                    'message' => 'Old password does not matched',
                ], 400);
            }
            return response()->json([
                'error' => false,
                'message' => 'Password Changed successfully',
            ], 200);

        }
        catch (Exception $error) 
        {
            return response()->json([
                'error' => true,
                'message' => 'Error in changing password',
                'error_log' => $error,
            ]);
        
        }
    }
}
