<?php

namespace App\Http\Controllers;

use App\HotRoute;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

class HotRouteController extends Controller
{
    public function get_hot_route_list(Request $request) {

        $dataget = HotRoute::leftjoin('carriers', 'carriers.id', '=', 'hot_routes.carrier_id')
            ->leftjoin('warehouses', 'warehouses.id', '=', 'hot_routes.wh_id')
            ->select('hot_routes.*', 'warehouses.warehouses', 'carriers.company_name');        

        if(isset($request->search['value'])){                        
            $search_text = $request->search['value'];
            if($search_text != ''){
                $dataget->where(function($query) use($search_text){
                    $query->Orwhere('zip','LIKE','%'.$search_text.'%');
                    $query->Orwhere('transit_days','LIKE','%'.$search_text.'%');  
                    $query->Orwhere('warehouses','LIKE','%'.$search_text.'%');              
                    $query->Orwhere('company_name','LIKE','%'.$search_text.'%');              
                    $query->Orwhere('cut_off_time','LIKE','%'.$search_text.'%');              
                });                
            }
        }

        $order_by = 'id';
        $order = 'ASC';

        if(isset($request->order[0]['column'])){
            $order_by = $request->order[0]['column'] == 0
                ? 'id' : $request->columns[$request->order[0]['column']]['data'];
            $order = $request->order[0]['dir'];
        }
        
        $dataget->orderBy($order_by, $order);

        $offset = $request->get('start');
        $limit = $request->get('length');
        $total = count($dataget->get()->toArray());        

        if($limit >= 0){
            $hot_routes = $dataget->skip($offset)->take($limit);
        } else {
            $hot_routes = $dataget->get();     
        }           

        return Datatables::of($hot_routes)
            ->filter(function ($query) {
                                
            })
            ->addColumn('action', function($row){
                $btn = '<button class="btn btn-warning mr-2" onclick="editRoute('.$row->id.')">Edit Route</button>';
                $btn .= '<button class="btn btn-danger" onclick="deleteRoute('.$row->id.')">Delete Route</button>';
                return $btn;
            })
            // ->editColumn('wh_id', function($row) {
            //     return $row->warehouse->warehouses;
            // })
            // ->editColumn('carrier_id', function($row) {
            //     return $row->carrier->company_name;
            // })
            ->rawColumns(['action'])
            ->setTotalRecords($total)
			->setFilteredRecords($total)
            ->make(true);
    }

    public function delete_route($id) {
        if ($id == 'all') {
            HotRoute::truncate();
            return response(['error' => 0, 'msg' => 'All Data Deleted Successfully.']);
        } else if (str_contains($id, ',')) {
            $ids = explode(',', $id);
            HotRoute::whereIn('id', $ids)->delete();    
            return response(['error' => 0, 'msg' => 'Selected Data Deleted Successfully.']);
        }
        HotRoute::where('id', $id)->delete();
        return response(['error' => 0, 'msg' => 'Deleted Successfully.']);
    }

    public function save_route(Request $request) {
        
        $wh = $request->wh_td;
        $carrier = $request->carrier_type;
        $zip_codes = $request->zip_codes;
        $transit_days = $request->transit_days;
        $cut_off_time = $request->cut_off_time;

        $zip_codes = explode(',', $zip_codes);

        foreach($zip_codes as $zip_code) {
            
            $hr = HotRoute::where('wh_id', $wh)->where('carrier_id', $carrier)->where('zip', $zip_code)->first();

            if (isset($hr)) {
                $hr->transit_days = $transit_days;
                $hr->cut_off_time = isset($cut_off_time) ? $cut_off_time : NULL;
                $hr->save();
            } else {
                HotRoute::create([
                    'wh_id' => $wh,
                    'carrier_id' => $carrier,
                    'zip' => $zip_code,
                    'transit_days' => $transit_days,
                    'cut_off_time' => isset($cut_off_time) ? $cut_off_time : NULL
                ]);
            }
        }

        return response(['error' => 0, 'msg' => isset($hr) ? 'Houte Route Edited Successfully.' : 'Houte Route Added Successfully.']);
    }

    public function get_route_by_id($id) {

        $hr = HotRoute::where('id', $id)->first();

        if (isset($hr)) {
            return response(['error' => 0, 'msg' => 'Houte Route Added Successfully.', 'data' => $hr]);
        }
        return response(['error' => 1, 'msg' => 'Houte Route not found.']);
    }
}
