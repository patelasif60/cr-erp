<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PropIngredients;
use App\Http\Requests\PropIngredientsRequest;
use DataTables;

class PropIngredientsController extends Controller
{
    public function __construct(PropIngredients $PropIngredients){
        $this->PropIngredients = $PropIngredients;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(moduleacess('AllSubMenusSelectionfunctions') == false){
            return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('prop_ingredients.index');
    }

    public function PropIngredientsList(Request $request){
        $pro = $this->PropIngredients->orderBy('prop_ingredients','ASC')->get();
        
        return DataTables::of($pro)
        ->addColumn('command',function($pro){
            $command = '';
                $command.='<div class="row"><div class="col-xl-2"><a onClick="GetModel(\''.route('prop_ingredients.edit',$pro['id']).'\')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a></div> ';
                $command.='</div>';
            
            return $command;
        })
        ->rawColumns(['command'])
        ->make(true);
    }

    public function create(){
        if(ReadWriteAccess('AllSubMenusSelectionfunctions') == false){
            return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('prop_ingredients.create');
    }

    public function store(PropIngredientsRequest $request){
        $input = $request->all();
        $data = [
            'prop_ingredients' => ProperInput($input['prop_ingredients']),
        ];
        $result = PropIngredients::create($data);
        if($result){
            $data_info = [
                'msg' => 'Success',
                'error' => 0
            ];
        }else{
            $data_info = [
                'msg' => 'Something wend wrong',
                'error' => 1
            ];
        }
        return response()->json($data_info);
    }

    public function edit($id){
        $row = PropIngredients::where('id',$id)->first()->toArray();
        return view('prop_ingredients.edit',compact('row'));
    }

    public function update(PropIngredientsRequest $request,$id){
        $input = $request->all();
        $data = [
            'prop_ingredients' =>  ProperInput($input['prop_ingredients']),
        ];
        $result =  PropIngredients::where('id',$id)->update($data);
        if($result){
            $data_info = [
                'msg' => 'Success',
                'error' => 0
            ];
        }else{
            $data_info = [
                'msg' => 'Something wend wrong',
                'error' => 1
            ];
        }
        return response()->json($data_info);
    }

    public function destroy($id)
    {
        $result = PropIngredients::where('id',$id)->delete();
        if($result){
            return redirect()->back()->with(['success'=>'Successfully deleted']);
        }else{
            return redirect()->back()->with(['error'=>'Something went wrong']);
        }
    }
}
