<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class ModulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modules = DB::table('modules')->orderBy('sort')->get();
        return view('modules.index',compact('modules'));
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
        $input = $request->all();
       
        if($input['id'] != ''){
            $update_data = [
                'menu_title' => $input['menu_title'],
                'module_slug' => $input['module_slug'],
                'module_link' => $input['module_link'],
                'module_icon' => $input['module_icon'],
                'is_module' => $input['is_module'],
            ];
            DB::table('modules')->where('id',$input['id'])->update($update_data);
            $arr['type']  = 'edit';
            $arr['menu_title'] = $input['menu_title'];
            $arr['module_slug']  = $input['module_slug'];
            $arr['module_link']  = $input['module_link'];
            $arr['module_icon']  = $input['module_icon'];
            $arr['is_module']  = $input['is_module'];
            $arr['id']    = $input['id'];
        } else {
            $data_insert = [
                'menu_title' => $input['menu_title'],
                'module_slug' => $input['module_slug'],
                'module_link' => $input['module_link'],
                'module_icon' => $input['module_icon'],
                'is_module' => $input['is_module'],
            ];
            $insert_id = DB::table('modules')->insertGetId($data_insert);
            $arr['menu'] = '<li class="dd-item dd3-item" data-id="'.$insert_id.'" >
                                <div class="dd-handle dd3-handle">&nbsp;</div>
                                <div class="dd3-content">
                                    <span id="label_show'.$insert_id.'">'.$input['menu_title'].'</span>
                                    <span class="span-right">
                                        <a class="edit-button btn btn-primary btn-sm btn-flat dlbtn'.$insert_id.'" id="'.$insert_id.'" is_module="'.$input['is_module'].'" module_icon="'.$input['module_icon'].'" module_link="'.$input['module_link'].'" menu_title="'.$input['menu_title'].'" module_slug="'.$input['module_slug'].'" style="padding: 1px 7px;"><i class="fas fa-pencil-alt"></i></a>  <a class="del-button btn btn-danger btn-sm btn-flat" id="'.$insert_id.'" style="padding: 1px 7px;"><i class="fa fa-trash"></i></a>
                                    </span> 
                                </div>';
            $arr['type'] = 'add';
        }
        print json_encode($arr);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function menu_save (Request $request)
    {
        $input = $request->all();
        $data = json_decode($input['data']);
        function parseJsonArray($jsonArray, $parentID = 0) {
          $return = array();
          foreach ($jsonArray as $subArray) {
            $returnSubSubArray = array();
            if (isset($subArray->children)) {
                $returnSubSubArray = parseJsonArray($subArray->children, $subArray->id);
            }
        
            $return[] = array('id' => $subArray->id, 'parentID' => $parentID);
            $return = array_merge($return, $returnSubSubArray);
          }
          return $return;
        }
        
        $readbleArray = parseJsonArray($data);
        $i=0;
        foreach($readbleArray as $row){
          $i++;
          $data_update = [
           'parent_menu_id' => $row['parentID'], 
           'sort' => $i, 
          ];
          DB::table('modules')->where('id',$row['id'])->update($data_update);
        }
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

    public function menu_delete(Request $request){
        $input = $request->all();
        function recursiveDelete($id) {
            $result = DB::table('modules')->select('id')->where('parent_menu_id',$id)->get();
            if(!empty($result)){
               foreach($result as $current) {
                    recursiveDelete($current->menu_id);
               }
            }
            DB::table('modules')->where('id',$id)->delete();
        }
        recursiveDelete($input['id']);
    }
}
