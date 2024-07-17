<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\Services\BillingNoteService;

class BillingNoteController extends Controller
{
	public function __construct(BillingNoteService $service)
	{
        $this->service = $service;
	}
	public function index(){
        if(moduleacess('AllSubMenusSelectionfunctions') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        return view('cranium.billingNote.index');
    }
    public function store(Request $request){
        $input = $request->all();
        $data = [
            'option' => ProperInput($input['option']),
        ];
     	$billingNotes = $this->service->create($data);
     	if(!$billingNotes)
        {
           $data_info = [
                'msg' => 'option already exists',
                'error' => 1
            ];
        }
        else{
        	$data_info = [
                'msg' => 'Success',
                'error' => 0
            ];
        }
        return response()->json($data_info);
    }

    public function update(Request $request){  
        $billingNotes = $this->service->update($request->all());
        if(!$billingNotes)
        {
           $data_info = [
                'msg' => 'option already exists',
                'error' => 1
            ];
        }
        else{
        	$data_info = [
                'msg' => 'Success',
                'error' => 0
            ];
        }
        return response()->json($data_info);
    }
    public function billingnotesList(Request $request){
        $billingNotes = $this->service->getAll();
        return Datatables::of($billingNotes)->addColumn('action', function($billingNote)
        {
            $btn = '';
            $btn .= '<a href="javascript:void(0);" onClick="openEditModal(\''.$billingNote->id.'\',\''.$billingNote->option.'\')" class="btn btn-primary"><i class="nav-icon i-Pen-2 "></i></a><a href="javascript:void(0)" onclick="deleteBillingNote(\''.$billingNote->id.'\')" class="btn btn-danger ml-1"><i class="nav-icon i-Close-Window"></i> </a>
            ';
            return $btn;
            
        })->rawColumns(['action'])->make(true);
    }
    public function destroy(Request $request){
        return $this->service->destroy($request->id);   
    }

}