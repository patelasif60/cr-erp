<?php

namespace App\Http\Controllers;

use App\ReportsUserFilter;
use App\ReportsMasterFilter;
use Illuminate\Http\Request;
use App\ProductListingFilter;
use Illuminate\Support\Facades\Auth;

class ReportSectionController extends Controller
{

    public function index() {
        return view('report_section.index');
    }

    public function getOpenOrderReport(Request $request) {

    }

    public function getShippedOrderReport(Request $request) {
        
    }

    public function getInventoryReport(Request $request) {
        
    }

    public function getOODReport(Request $request) {
        
    }

    public function getShippedItemsReport(Request $request) {
        
    }

    public function getFilters($report_type) {

        $filters = ReportsMasterFilter::where('report_type', $report_type)->get();
        $user_filter = ReportsUserFilter::where('report_type', $report_type)
                        ->where('created_by', Auth::user()->id)->first();
        
        $col_ids = [];
        if (isset($user_filter)) {
            $filter_ids = $user_filter->filter_ids;
            $col_ids = explode(',', $filter_ids);
        }

        return view('report_section.view_columns', compact('report_type', 'filters', 'col_ids'));
    }

    public function saveFilters(Request $request) {

        # Auth::user()->id
        $col_ids = explode(",", $request->all()['col_ids']);

        $selected_filters = ReportsMasterFilter::whereIn('id', $col_ids)->get();
        $table_columns = [];
        foreach ($selected_filters as $filter) {
            if (!isset($table_columns[$filter->table_name])) {
                $table_columns[$filter->table_name] = [];
            }
            array_push($table_columns[$filter->table_name], $filter->column_name);
        }

        $user_filter = ReportsUserFilter::where('created_by', Auth::user()->id)
            ->where('report_type', $request->all()['report_type'])->first();

        if (!isset($user_filter)) {
            ReportsUserFilter::create([
                'created_by' => Auth::user()->id,
                'selected_filters' => json_encode($table_columns),
                'report_type' => $request->all()['report_type'],
                'filter_ids' => implode(',', $col_ids)
            ]);
        } else {
            $user_filter->selected_filters = json_encode($table_columns);
            $user_filter->filter_ids = implode(',', $col_ids);
            $user_filter->save();
        }
        
        return response(['data' => json_decode(json_encode($table_columns)), 
                'msg' => 'Filters Added Successfully',
                'error' => 0
        ]);
    }
}