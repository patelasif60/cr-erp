<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class RolesController extends Controller
{
    public function __construct()
	{
        $this->middleware('admin_and_manager');
	}
    /**  
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles_permissions_menus = DB::table('roles_permissions')->where('type','menus')->orderBy('sorting_order')->get();
        $roles_permissions_functions = DB::table('roles_permissions')->where('type','functions')->orderBy('sorting_order')->get();
        $roles_permissions_wms = DB::table('roles_permissions')->where('type','wms')->orderBy('sorting_order')->get();
        $roles_permissions_notification = DB::table('roles_permissions')->where('type','notification')->orderBy('sorting_order')->get();
        // return view('roles.index',compact('roles'));
        return view('roles.RolePermissions',compact('roles_permissions_menus','roles_permissions_functions', 'roles_permissions_wms','roles_permissions_notification'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function RolePermissions($role_id,$parent_id){
        return view('roles.RolePermissions',compact('role_id','parent_id'));
    }

    public function display_access(Request $request){
        $data = $request->all();
        if($data['role'] == 'user'){
            DB::table('roles_permissions')->where('module_link',$data['module_link'])->update(['user'=> $data['valueck']]); 
        }
        if($data['role'] == 'manager'){
            DB::table('roles_permissions')->where('module_link',$data['module_link'])->update(['manager'=> $data['valueck']]); 
        }
        if($data['role'] == 'administrator'){
            DB::table('roles_permissions')->where('module_link',$data['module_link'])->update(['administrator'=> $data['valueck']]); 
        }
        if($data['role'] == 'wms_user'){
            DB::table('roles_permissions')->where('module_link',$data['module_link'])->update(['wms_user'=> $data['valueck']]); 
        }
        if($data['role'] == 'wms_manager'){
            DB::table('roles_permissions')->where('module_link',$data['module_link'])->update(['wms_manager'=> $data['valueck']]); 
        }

        if($request->valueck == 1){
            $returnData['msg'] = "Permission Added";
        }else{
            $returnData['msg'] = "Permission Removed";
        }
        return response()->json($returnData);
    }

    public function save_menus_order(Request $request){
        $roles_permissions_menus = DB::table('roles_permissions')->where('type','menus')->orderBy('sorting_order')->get();
        $order = explode(",",$request->order);
        if(!empty($order)){
            foreach($roles_permissions_menus as $row){
                $key = array_search($row->id,$order);
                DB::table('roles_permissions')->where('id',$row->id)->update(['sorting_order'=>$key]);
            }
        }
        return response()->json(['msg'=>"Table Sorted"]);
    }

    public function save_functions_order(Request $request){
        $roles_permissions_functions = DB::table('roles_permissions')->where('type','functions')->orderBy('sorting_order')->get();
        $order = explode(",",$request->order);
        if(!empty($order)){
            foreach($roles_permissions_functions as $row){
                $key = array_search($row->id,$order);
                DB::table('roles_permissions')->where('id',$row->id)->update(['sorting_order'=>$key]);
            }
        }
        return response()->json(['msg'=>"Table Sorted"]);
    }

    public function save_notifications_order(Request $request){
        $roles_permissions_notifications = DB::table('roles_permissions')->where('type','notification')->orderBy('sorting_order')->get();
        $order = explode(",",$request->order);
        if(!empty($order)){
            foreach($roles_permissions_notifications as $row){
                $key = array_search($row->id,$order);
                DB::table('roles_permissions')->where('id',$row->id)->update(['sorting_order'=>$key]);
            }
        }
        return response()->json(['msg'=>"Table Sorted"]);
    }

    public function save_wms_order(Request $request){
        $roles_permissions_functions = DB::table('roles_permissions')->where('type','wms')->orderBy('sorting_order')->get();
        $order = explode(",",$request->order);
        if(!empty($order)){
            foreach($roles_permissions_functions as $row){
                $key = array_search($row->id,$order);
                DB::table('roles_permissions')->where('id',$row->id)->update(['sorting_order'=>$key]);
            }
        }
        return response()->json(['msg'=>"Table Sorted"]);
    }
}