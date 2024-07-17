<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MasterProductReport;
use App\ProductVariance;
use App\Feedback;
use DB;
use DataTables;
use App\RequestProductSelection;
use Excel;
use App\Exports\MarkoutExport;

class ReportsController extends Controller
{
    public function master_product_daily_report(){
        if(moduleacess('MasterProductDailyReport') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        $product_reports = MasterProductReport::orderBy('created_at', 'desc')->get();
        return view('cranium.reports.master_product_daily_report', ['product_reports' => $product_reports]);
    } 

    public function feedbacks(){
        if(moduleacess('Feedback') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        $result = Feedback::leftJoin('users',function($join){
            $join->on('users.id','=','feedback.user_id');
        })->select('feedback.*','users.name')->get();
        return view('cranium.reports.feedbacks',compact('result'));
    }

    public function new_requests(){
        if(moduleacess('NewRequestTypes') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        $result = RequestProductSelection::all();
        return view('cranium.reports.new_requests',compact('result'));
    }

    public function markout_report(){
        return view('cranium.reports.markout_products');
    }

    public function markout_datatables(Request $request){
        $data = ProductVariance::with('product')->orderBy('id','desc')->get();
        
        return Datatables::of($data)
            
            ->make(true);
    }

    public function markout_export(){
        $data = ProductVariance::with('product')->orderBy('id','desc')->get();
        return  Excel::download(new MarkoutExport($data), 'Cranium_markout_'.date('Ymd').'.xlsx', null, [\Maatwebsite\Excel\Excel::XLSX]);
    }

}
