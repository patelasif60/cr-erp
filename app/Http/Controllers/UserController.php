<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserNotificationSetting;
use App\UserRole;
use App\Client;
use App\OrderTypes;
use App\Http\Requests\UsersRequest;
use App\WareHouse;
use Hash;
use Auth;

class UserController extends Controller
{
    public function __construct()
	{
        //$this->middleware('admin_and_manager');
	}
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(moduleacess('Users') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        
        if(Auth::user()->role == 3){
            $result = User::where('id',Auth::user()->id)->select(['id','name','email','role'])->get();
        }else{
            $result = User::all();
        }
        
        return view('users.index',compact('result'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(ReadWriteAccess('AddNewUser') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        $roles = UserRole::all();
        $whs = WareHouse::all();
        $clients = Client::select('id','company_name')->get();
        return view('users.create',compact('roles', 'whs','clients'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UsersRequest $request)
    {
        if(ReadWriteAccess('AddNewUser') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}

        if ($request->role) {
            $role = UserRole::where('id', $request->role)->first();
            if (($role->role === 'WMS User' || $role->role === 'WMS Manager')
                && (!$request->wh || ((int) $request->wh) <= 0)) {
                return response()->json([
                    'error' => true,
                    'msg' => 'Warehouse is required for Role WMN User/WMS Manager'
                ]);
            }
        }

        if (isset($request->email)) {
            if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'error' => true,
                    'msg' => 'Invalid Email Address'
                ]);
            }

            $user = User::where('email', $request->email)->first();
            if (isset($user)) {
                return response()->json([
                    'error' => true,
                    'msg' => 'Email Already exists Address'
                ]);
            }

            if (strlen($request->email) > 255) {
                return response()->json([
                    'error' => true,
                    'msg' => 'Email should be of 255 characters'
                ]);
            }
        }

        $User = new User;
        $User->name = $request->name;
        $User->password = Hash::make($request->password);
        $User->role = $request->role;
        $User->email = $request->email;
        $User->wh_id = $request->wh;
        $User->client = $request->client;
        $User->username = $request->username;
        $User->save();
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('users.index')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(ReadWriteAccess('EditUser') == false && Auth::user()->id != $id){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        $row = User::find($id);
        $roles = UserRole::all();
        $whs = WareHouse::all();
        $clients = Client::select('id','company_name')->get();
        $ots = OrderTypes::get();
        $notification = UserNotificationSetting::where('user_id',$id)->whereNull('order_by_client')->first();
        return view('users.edit',compact('row','roles','whs','clients','notification','ots'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UsersRequest $request, $id)
    {
        if(ReadWriteAccess('EditUser') == false && Auth::user()->id != $id){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}    

        if ($request->role) {
            $role = UserRole::where('id', $request->role)->first();
            if (($role->role === 'WMS User' || $role->role === 'WMS Manager')
                && (!$request->wh || ((int) $request->wh) <= 0)) {
                return response()->json([
                    'error' => true,
                    'msg' => 'Warehouse is required for Role WMN User/WMS Manager'
                ]);
            }
        }

        if (isset($request->email)) {
            if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'error' => true,
                    'msg' => 'Invalid Email Address'
                ]);
            }

            if (strlen($request->email) > 255) {
                return response()->json([
                    'error' => true,
                    'msg' => 'Email should be of 255 characters'
                ]);
            }
        }

        $User = User::find($id);

        if (!isset($User)) {
            return response()->json([
                'error' => true,
                'msg' => 'Invalid User Id'
            ]);
        }

        if ($User->email !== $request->email) {

            $entry = User::where('email', $request->email)->first();
            if (isset($entry)) {
                return response()->json([
                    'error' => true,
                    'msg' => 'Email already exists'
                ]);
            }            
        }

        $User->name = $request->name;
        $User->role = $request->role;
        $User->email = $request->email;
        $User->wh_id = $request->wh;
        $User->client = $request->client;
        $User->username = $request->username;
        $User->save();
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('users.index')
        ]);
    }

    public function update_password(Request $request, $id)
    {
        $request->validate([
            'password' => ['sometimes','required', 'string', 'min:6', 'confirmed'],
        ]);

        $User = User::find($id);
        $User->password = Hash::make($request->password);
        $User->save();
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('users.index')
        ]);
    }

    public function update_notification(Request $request, $id)
    {
        

        $UN = UserNotificationSetting::where('user_id',$id)->whereNull('order_by_client')->first();
        if(!$UN){
            $UN = new UserNotificationSetting();
        }
        $UN->user_id = $id;
        $UN->notification_type = (isset($request->notification_type) ? implode(',',$request->notification_type) : NULL);
        $UN->order_by_client = (isset($request->order_by_client) ? implode(',',$request->order_by_client) : NULL);
        $UN->order_by_order_type = (isset($request->order_by_order_type) ? implode(',',$request->order_by_order_type) : NULL);
        $UN->inventory_low_stock = (isset($request->inventory_low_stock) ? 1 : 0);
        $UN->inventory_high_stock = (isset($request->inventory_high_stock) ? 1 : 0);
        $UN->inventory_out_of_stock = (isset($request->inventory_out_of_stock) ? 1 : 0);
        
        $UN->save();
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('users.edit',$id)
        ]);
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(ReadWriteAccess('DeleteUser') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        $User = User::find($id);
        $User->delete();
        return redirect()->route('users.index')->with('success','Deleted successfully');
    }
}
