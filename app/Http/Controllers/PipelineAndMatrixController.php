<?php

namespace App\Http\Controllers;

use App\User;
use App\Client;
use App\WareHouse;
use Carbon\Carbon;
use App\OrderDetail;
use App\OrderSummary;
use App\Exports\TotalOrderShipped;
use Carbon\CarbonPeriod;
use App\Exports\TotalOrder;
use App\ShippingServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PipelineAndMatrixController extends Controller
{
    public function index() {        
        return view('report_section.pipeline_and_metrix');
    }

    public function GetTotalOrderChart(Request $request){  
        $input = $request->all();
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $client_id = $request->client_id;
        $order_status = $request->order_status;
        $wh_id = '';
        if (isset($request->to_warehouse)) {
            $wh_id = $request->to_warehouse;
        } else if (isset(Auth::user()->wh_id) && Auth::user()->role != 1) {
            $wh_id = Auth::user()->wh_id;
        }
        $warehouse = $wh_id > 0 ? WareHouse::find($wh_id) : null;
        $period = CarbonPeriod::create($start_date, $end_date);

        // Iterate over the period
        $str = '';
        foreach ($period as $date) {
            $day = $date->format('m-d-Y');
            $startDate = Carbon::parse($date)->startOfDay();
            $endDate = Carbon::parse($date)->endOfDay();
            $order_obj = OrderSummary::leftJoin('order_details',function($join){
                $join->on('order_details.order_number','=','order_summary.etailer_order_number');
            })->whereBetween('order_summary.created_at',[$startDate,$endDate]);
            if(isset($client_id))
            {
                $order_obj->whereIn('order_summary.client_id',$client_id);
            }
            if(isset($order_status)){
                $order_obj->whereIn('order_summary.order_status',$order_status);
            }
            if($warehouse != ''){
                $order_obj->where('order_details.warehouse',$warehouse->warehouses);
            }
            $order_obj->groupBy('order_summary.id');

            $total_price = count($order_obj->get());
            $str.="['".$day."', ".$total_price."],";
        }
        $chart_data = substr($str,0,-1);
        return view('report_section.parts.order_chart_total_orders',compact('chart_data','input'));
    }

    public function GetTotalOrderModel(Request $request){
        $date = $request->selected_date; 
        $newdate = Carbon::createFromFormat('m-d-Y', $date)->format('Y-m-d');
        $startDate = Carbon::parse($newdate)->startOfDay();
        $endDate = Carbon::parse($newdate)->endOfDay();
        
        $order_obj = OrderSummary::leftjoin('clients', 'clients.id', '=', 'order_summary.client_id')
        ->leftjoin('order_summary_status', 'order_summary_status.id', '=', 'order_summary.order_status')
        ->leftJoin('order_details',function($join){
            $join->on('order_details.order_number','=','order_summary.etailer_order_number');
        })
        ->select('order_summary.*','order_summary_status.order_status_name', 'clients.company_name as client_name')
        ->whereBetween('order_summary.created_at',[$startDate,$endDate]);
        $client_id = json_decode($request->client_id);
        $order_status = json_decode($request->order_status);

        $warehouse = '';
        if (isset($request->to_warehouse)) {
            $warehouse = $request->to_warehouse;
        } else if (isset($request->wh_warehouse)) {
            $warehouse = $request->wh_warehouse;
        }

        $warehouse = !isset($warehouse) && Auth::user()->role != 1 && isset(Auth::user()->wh_id) 
                        ? Auth::user()->wh_id : '';
        
        if(isset($client_id))
        {
            $order_obj->whereIn('order_summary.client_id',$client_id);
        }
        if(isset($order_status)){
            $order_obj->whereIn('order_summary.order_status',$order_status);
        }
        if($warehouse != ''){
            $wh = WareHouse::find($warehouse);
            $order_obj->where('order_details.warehouse', $wh->warehouses);
        }
        $order_obj->groupBy('order_summary.id');
        $result = $order_obj->get();

        return view('report_section.parts.model_totalorder_chart',compact('result'));

    }

    public function GetUserOrderModel(Request $request){
        $start_date = str_replace('"', '', $request->start_date);
        $end_date = str_replace('"', '', $request->end_date);
        $startDate = Carbon::parse($start_date)->startOfDay();
        $endDate = Carbon::parse($end_date)->endOfDay();
        
        $result = OrderSummary::leftjoin('clients', 'clients.id', '=', 'order_summary.client_id')
        ->leftjoin('order_summary_status', 'order_summary_status.id', '=', 'order_summary.order_status')
        ->leftJoin('order_details',function($join){
            $join->on('order_details.order_number','=','order_summary.etailer_order_number');
        })
        ->select('order_summary.*','order_summary_status.order_status_name', 'clients.company_name as client_name')
        ->whereBetween('order_summary.created_at',[$startDate, $endDate])
        //->where('order_details.warehouse', $wh)->groupBy('order_summary.id')
        ->get();

        return view('report_section.parts.model_totalorder_chart',compact('result'));
    }

    public function GetTotalOrderChartByUser(Request $request){
        $input = $request->all();
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $startDate = Carbon::parse($start_date)->startOfDay();
        $endDate = Carbon::parse($end_date)->endOfDay();
        $selected_users = $request->user;
        $users = isset($selected_users) ? User::whereIn('id', $selected_users)->get() : User::all();
        $str = '';
        if($users){
            foreach($users as $row_users){
                $order_obj_picked = OrderDetail::leftJoin('order_summary',function($join){
                    $join->on('order_details.order_number','=','order_summary.etailer_order_number');
                })->whereBetween('order_summary.created_at',[$startDate,$endDate])->where('order_details.picker_id',$row_users->id);
                $order_obj_picked->groupBy('order_details.sub_order_number');
                $order_obj_picked->whereIn('order_details.status',[3,11]);
                $total_picked = count($order_obj_picked->get());

                $order_obj_packed = OrderDetail::leftJoin('order_summary',function($join){
                    $join->on('order_details.order_number','=','order_summary.etailer_order_number');
                })->whereBetween('order_summary.created_at',[$startDate,$endDate])->where('order_details.picker_id',$row_users->id);
                $order_obj_packed->groupBy('order_details.sub_order_number');
                $order_obj_packed->whereIn('order_details.status',[4,12]);
                $total_packed = count($order_obj_packed->get());

                $order_obj_shipped = OrderDetail::leftJoin('order_summary',function($join){
                    $join->on('order_details.order_number','=','order_summary.etailer_order_number');
                })->whereBetween('order_summary.created_at',[$startDate,$endDate])->where('order_details.picker_id',$row_users->id);
                $order_obj_shipped->groupBy('order_details.sub_order_number');
                $order_obj_shipped->whereIn('order_details.status',[6,13]);
                $total_shipped = count($order_obj_shipped->get());

                if($total_picked > 0 || $total_packed > 0 || $total_shipped > 0){
                    $str.="['".$row_users->name."', ".$total_picked.", ".$total_packed.", ".$total_shipped."],";
                }
                
            }
        }

        $chart_data = substr($str,0,-1);
        
        return view('report_section.parts.order_chart_total_orders_by_user',compact('chart_data','input'));
    }

    public function GetWarehouseOrderModel(Request $request){
        $start_date = str_replace('"', '', $request->start_date);
        $end_date = str_replace('"', '', $request->end_date);
        $startDate = Carbon::parse($start_date)->startOfDay();
        $endDate = Carbon::parse($end_date)->endOfDay();

        $wh = $request->selected_wh;
        
        $result = OrderSummary::leftjoin('clients', 'clients.id', '=', 'order_summary.client_id')
        ->leftjoin('order_summary_status', 'order_summary_status.id', '=', 'order_summary.order_status')
        ->leftJoin('order_details',function($join){
            $join->on('order_details.order_number','=','order_summary.etailer_order_number');
        })
        ->select('order_summary.*','order_summary_status.order_status_name', 'clients.company_name as client_name')
        ->whereBetween('order_summary.created_at',[$startDate, $endDate])
        ->where('order_details.warehouse', $wh)->groupBy('order_summary.id')->get();

        return view('report_section.parts.model_totalorder_chart',compact('result'));
    }

    public function GetTotalOrderChartByWarehouse(Request $request){
        $input = $request->all();
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $startDate = Carbon::parse($start_date)->startOfDay();
        $endDate = Carbon::parse($end_date)->endOfDay();
        
        $warehouses = NULL;
        if (isset($request->wh_warehouse)) {
            $warehouses = WareHouse::where('id', $request->wh_warehouse)->get();            
        }
        $warehouses = Auth::user()->role != 1 && isset(Auth::user()->wh_id) && !isset($warehouses)
                ? WareHouse::where('id', Auth::user()->wh_id)->get() 
                : $warehouses;
        
        $warehouses = Auth::user()->role == 1 && !isset($warehouses) ? WareHouse::all() : $warehouses;

        $str = '';
        if(isset($warehouses) && count($warehouses) > 0){
            foreach($warehouses as $row_warehouse){
                $order_obj_picked = OrderDetail::leftJoin('order_summary',function($join){
                    $join->on('order_details.order_number','=','order_summary.etailer_order_number');
                })->whereBetween('order_summary.created_at',[$startDate,$endDate])->where('order_details.warehouse',$row_warehouse->warehouses);
                $order_obj_picked->groupBy('order_details.sub_order_number');
                $order_obj_picked->whereIn('order_details.status',[3,11]);
                $total_picked = count($order_obj_picked->get());

                $order_obj_packed = OrderDetail::leftJoin('order_summary',function($join){
                    $join->on('order_details.order_number','=','order_summary.etailer_order_number');
                })->whereBetween('order_summary.created_at',[$startDate,$endDate])->where('order_details.warehouse',$row_warehouse->warehouses);
                $order_obj_packed->groupBy('order_details.sub_order_number');
                $order_obj_packed->whereIn('order_details.status',[4,12]);
                $total_packed = count($order_obj_packed->get());

                $order_obj_shipped = OrderDetail::leftJoin('order_summary',function($join){
                    $join->on('order_details.order_number','=','order_summary.etailer_order_number');
                })->whereBetween('order_summary.created_at',[$startDate,$endDate])->where('order_details.warehouse',$row_warehouse->warehouses);
                $order_obj_shipped->groupBy('order_details.sub_order_number');
                $order_obj_shipped->whereIn('order_details.status',[6,13]);
                $total_shipped = count($order_obj_shipped->get());

                if($total_picked > 0 || $total_packed > 0 || $total_shipped > 0){
                    $str.="['".$row_warehouse->warehouses."', ".$total_picked.", ".$total_packed.", ".$total_shipped."],";
                }                
                
            }
        }

        $chart_data = substr($str,0,-1);    
        
        return view('report_section.parts.order_chart_total_orders_by_warehouse',compact('chart_data','input'));
    }
    
    public function GetClientOrdersByTransitDays(Request $request){
        $input = $request->all();
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $client_id = $request->client_id_transit;
        $whs = $request->td_gr_warehouse;
        $wh_selected = '';
        if (isset($whs) && count($whs) > 0) {
            foreach($whs as $wh) {
                $wh_selected = $wh_selected . '\'' . $wh . '\', ';
            }
            $wh_selected = substr($wh_selected, 0, -2);
        }
        
        $sql = 'SELECT c.company_name, od.sub_order_number, od.transit_days, sum(od.customer_paid_price) as total_money, 
                case
                    when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                    else "LINE PACK"
                end as pack_type
                FROM order_details od
                INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
                inner join clients c on os.client_id = c.id
                where od.transit_days is not null 
                and od.service_type_id not in (20, 22, 23) 
                and od.status not in (6, 13, 14)
                and CONVERT(od.created_at, DATE) between \''.$start_date.'\' and \''.$end_date.'\' ';

        if (isset($client_id)) {
            $sql = $sql . 'and os.client_id in ('. implode(',', $client_id) .')';            
        }

        if ($wh_selected !== '') {
            $sql = $sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $sql = $sql . 'group by c.company_name, od.sub_order_number, od.transit_days';
        
        $all_items = [];
        $items = DB::select($sql);

        if (isset($items)) { foreach ($items as $item) array_push($all_items, $item); }

        $sql = 'SELECT c.company_name, od.sub_order_number, od.fulfilled_by, 
            case
                when od.service_type_id = 20 then \'-1\'
                when od.service_type_id = 22 or od.service_type_id = 23 then \'0\'
            end as transit_days, 
            case
                when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                else "LINE PACK"
            end as pack_type
            FROM order_details od
            INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
            inner join clients c on os.client_id = c.id
            where od.service_type_id in (20, 22, 23) and od.status not in (6, 13, 14)
            and CONVERT(od.created_at, DATE) between \''.$start_date.'\' and \''.$end_date.'\' ';

        if (isset($client_id)) {
            $sql = $sql . 'and os.client_id in ('. implode(',', $client_id) .') ';            
        }

        if ($wh_selected !== '') {
            $sql = $sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $sql = $sql . 'group by c.company_name, od.sub_order_number, od.fulfilled_by, od.service_type_id';

        $items = DB::select($sql);

        if (isset($items)) { foreach ($items as $item) array_push($all_items, $item); }

        $client_count_array = [];
        $pack_type_order_number = [];
        if (isset($all_items)) {
            foreach ($all_items as $item) {                
                $pack_type = str_replace(' ', '_', $item->pack_type);
                $transit_day = $item->transit_days;
                $order_number = $item->sub_order_number;
                if (isset($client_count_array[$pack_type][$transit_day])) {
                    $prev_count = $client_count_array[$pack_type][$transit_day];
                    $client_count_array[$pack_type][$transit_day] = $prev_count += 1;
                } else {
                    $client_count_array[$pack_type][$transit_day] = 1;
                }
                if (isset($pack_type_order_number[$pack_type])) {
                    $old_order_number = $pack_type_order_number[$pack_type];
                    $pack_type_order_number[$pack_type] = $old_order_number . ',' . $order_number;
                } else {
                    $pack_type_order_number[$pack_type] = $order_number;
                }
            }
        }

        $ip_001d = isset($client_count_array['INDIVIDUAL_PACK']['-1']) ? $client_count_array['INDIVIDUAL_PACK']['-1'] : '0';
        $ip_01d = isset($client_count_array['INDIVIDUAL_PACK']['0']) ? $client_count_array['INDIVIDUAL_PACK']['0'] : '0';
        $ip_1d = isset($client_count_array['INDIVIDUAL_PACK']['1']) ? $client_count_array['INDIVIDUAL_PACK']['1'] : '0';
        $ip_2d = isset($client_count_array['INDIVIDUAL_PACK']['2']) ? $client_count_array['INDIVIDUAL_PACK']['2'] : '0';
        $ip_3d = isset($client_count_array['INDIVIDUAL_PACK']['3']) ? $client_count_array['INDIVIDUAL_PACK']['3'] : '0';
        $ip_4d = isset($client_count_array['INDIVIDUAL_PACK']['4']) ? $client_count_array['INDIVIDUAL_PACK']['4'] : '0';
        $ip_5d = isset($client_count_array['INDIVIDUAL_PACK']['5']) ? $client_count_array['INDIVIDUAL_PACK']['5'] : '0';
        $lp_001d = isset($client_count_array['LINE_PACK']['-1']) ? $client_count_array['LINE_PACK']['-1'] : '0';
        $lp_01d = isset($client_count_array['LINE_PACK']['0']) ? $client_count_array['LINE_PACK']['0'] : '0';
        $lp_1d = isset($client_count_array['LINE_PACK']['1']) ? $client_count_array['LINE_PACK']['1'] : '0';
        $lp_2d = isset($client_count_array['LINE_PACK']['2']) ? $client_count_array['LINE_PACK']['2'] : '0';
        $lp_3d = isset($client_count_array['LINE_PACK']['3']) ? $client_count_array['LINE_PACK']['3'] : '0';
        $lp_4d = isset($client_count_array['LINE_PACK']['4']) ? $client_count_array['LINE_PACK']['4'] : '0';
        $lp_5d = isset($client_count_array['LINE_PACK']['5']) ? $client_count_array['LINE_PACK']['5'] : '0';

        $chart_data = "['INDIVIDUAL_PACK', ".$ip_001d.", ".$ip_01d.", ".$ip_1d.", ".$ip_2d.", ".$ip_3d.", ".$ip_4d.", ".$ip_5d."], ['LINE_PACK', ".$lp_001d.", ".$lp_01d.", ".$lp_1d.", ".$lp_2d.", ".$lp_3d.", ".$lp_4d.", ".$lp_5d."]";
        return view('report_section.parts.order_chart_transit',compact('chart_data','input', 'pack_type_order_number'));
    }

    public function GetTransitDayModal(Request $request){
        $start_date = str_replace('"', '', $request->start_date);
        $end_date = str_replace('"', '', $request->end_date);
        $client_id = $request->client_id_transit;
        $pack_type = $request->pack_type;
        $o_nums = explode(',', $request->$pack_type);
        $str_onums = '';
        foreach($o_nums as $o_num) {
            $str_onums = $str_onums . '\'' . $o_num . '\', ';
        }
        $str_onums = substr($str_onums, 0, -2);
        $whs = $request->td_gr_warehouse;
        $wh_selected = str_replace('[', '', $whs);
        $wh_selected = str_replace(']', '', $wh_selected);

        $sql = 'SELECT c.company_name, od.sub_order_number, od.fulfilled_by , od.transit_days 
            FROM order_details od
            INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
            inner join clients c on os.client_id = c.id
            where od.transit_days is not null 
            and od.service_type_id not in (20, 22, 23) 
            and od.status not in (6, 13, 14)
            and od.sub_order_number in ('. $str_onums .') 
            and CONVERT(od.created_at, DATE) between \''.$start_date.'\' and \''.$end_date.'\' ';

        if (isset($client_id)) {
            $sql = $sql . 'and os.client_id in ('. implode(',', $client_id) .')';            
        }

        if ($wh_selected !== '') {
            $sql = $sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $sql = $sql . ' group by c.company_name, od.sub_order_number, od.fulfilled_by , od.transit_days'; 
        
        $items = DB::select($sql);

        $all_items = [];

        if (isset($items)) { foreach ($items as $item) array_push($all_items, $item); }

        $sql = 'SELECT c.company_name, od.sub_order_number, od.fulfilled_by, 
            case
                when od.service_type_id = 20 then \'-1\'
                when od.service_type_id = 22 or od.service_type_id = 23 then \'0\'
            end as transit_days, 
            case
                when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                else "LINE PACK"
            end as pack_type
            FROM order_details od
            INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
            inner join clients c on os.client_id = c.id
            where od.service_type_id in (20, 22, 23) and od.status not in (6, 13, 14) and od.sub_order_number in ('. $str_onums .') 
            and CONVERT(od.created_at, DATE) between \''.$start_date.'\' and \''.$end_date.'\' ';

        if (isset($client_id)) {
            $sql = $sql . 'and os.client_id in ('. implode(',', $client_id) .') ';            
        }

        if ($wh_selected !== '') {
            $sql = $sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $sql = $sql . 'group by c.company_name, od.sub_order_number, od.fulfilled_by, od.service_type_id';

        $items = DB::select($sql);

        if (isset($items)) { foreach ($items as $item) array_push($all_items, $item); }

        $type_transit_count = [];

        if (isset($all_items)) {
            foreach($all_items as $item) {
                $temp = get_temp($item->fulfilled_by, $item->sub_order_number);
                if (isset($temp) && $temp !== '') {
                    $comp = $item->company_name;
                    $transit_day = $item->transit_days;
                    $value = 1;
                    $sub_order = $item->sub_order_number;
                    if (isset($type_transit_count[$temp][$comp][$transit_day])) {
                        $value = $type_transit_count[$temp][$comp][$transit_day][0] + 1;
                        $sub_order = $type_transit_count[$temp][$comp][$transit_day][1] . ',' . $item->sub_order_number;
                    }
                    $type_transit_count[$temp][$comp][$transit_day][0] = $value;                    
                    $type_transit_count[$temp][$comp][$transit_day][1] = $sub_order;                    
                }
            }
        }

        $temp_count = [];
        foreach($type_transit_count as $key => $values) {
            foreach($values as $value_key => $value) {
                $temp = $key;
                foreach (range(-1, 5) as $day) {
                    $day_order = isset($values[$value_key][$day][1]) ? $values[$value_key][$day][1] : '';
                    if (isset($temp_count[$temp][$day]) && $temp_count[$temp][$day] !== '') {
                        $day_order = $day_order !== ''
                                    ? $day_order . ',' . $temp_count[$temp][$day]
                                    : $temp_count[$temp][$day];
                    }
                    $temp_count[$temp][$day] = $day_order;
                }                
            }
        }
        
        return view('report_section.parts.model_total_td_chart',compact('type_transit_count', 'temp_count'));
    }

    public function GetTransitDayOrderModal(Request $request) {

        $so_nums = isset($request->sub_orders) ? explode(',', $request->sub_orders) : NULL;
        $o_nums = isset($request->orders) ? explode(',', $request->orders) : NULL;

        if ($o_nums != NULL) {
            $os = OrderSummary::join('order_details as od', 'od.order_number' , '=', 'order_summary.etailer_order_number')
            ->leftjoin('users as u', 'u.id', '=', 'od.picker_id')
            ->join('order_summary_status as oss', 'order_summary.order_status', '=', 'oss.id')
            ->whereIn('od.order_number', $o_nums)->distinct()->get(['order_summary.*', 'oss.order_status_name', 'u.name as picker_name']);
        } else if ($so_nums != NULL) {
            $os = OrderSummary::join('order_details as od', 'od.order_number' , '=', 'order_summary.etailer_order_number')
            ->leftjoin('users as u', 'u.id', '=', 'od.picker_id')
            ->join('order_summary_status as oss', 'order_summary.order_status', '=', 'oss.id')
            ->whereIn('od.sub_order_number', $so_nums)->distinct()->get(['order_summary.*', 'oss.order_status_name', 'u.name as picker_name']);
        }        

        return response()->json([                
            'error' => 0,
            'data' => $os
        ]);
    }

    public function GetTransitDayTable(Request $request) {
        $input = $request->all();
        $start_date = $request->tb_start_date;
        $end_date = $request->tb_end_date;
        $client_id = $request->tb_client_id_transit;
        
        $whs = $request->td_warehouse;
        $wh_selected = '';
        if (isset($whs) && count($whs) > 0) {
            foreach($whs as $wh) {
                $wh_selected = $wh_selected . '\'' . $wh . '\', ';
            }
            $wh_selected = substr($wh_selected, 0, -2);
        }
        
        $sql = 'SELECT c.company_name, od.sub_order_number, od.transit_days, od.fulfilled_by, 
            case
                when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                else "LINE PACK"
            end as pack_type
            FROM order_details od
            INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
            inner join clients c on os.client_id = c.id
            where od.transit_days is not null 
            and od.service_type_id not in (20, 22, 23) 
            and od.status not in (6, 13, 14)
            and CONVERT(od.created_at, DATE) between \''.$start_date.'\' and \''.$end_date.'\' ';

        if (isset($client_id)) {
            $sql = $sql . 'and os.client_id in ('. implode(',', $client_id) .') ';            
        }

        if ($wh_selected !== '') {
            $sql = $sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $sql = $sql . 'group by c.company_name, od.sub_order_number, od.fulfilled_by , od.transit_days';

        $all_items = [];
        $items = DB::select($sql);
        
        if (isset($items)) { foreach ($items as $item) array_push($all_items, $item); }
        
        $sql = 'SELECT c.company_name, od.sub_order_number, od.fulfilled_by, 
            case
                when od.service_type_id = 20 then \'-1\'
                when od.service_type_id = 22 or od.service_type_id = 23 then \'0\'
            end as transit_days, 
            case
                when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                else "LINE PACK"
            end as pack_type
            FROM order_details od
            INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
            inner join clients c on os.client_id = c.id
            where od.service_type_id in (20, 22, 23) and od.status not in (6, 13, 14)
            and CONVERT(od.created_at, DATE) between \''.$start_date.'\' and \''.$end_date.'\' ';

        if (isset($client_id)) {
            $sql = $sql . 'and os.client_id in ('. implode(',', $client_id) .') ';            
        }

        if ($wh_selected !== '') {
            $sql = $sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $sql = $sql . 'group by c.company_name, od.sub_order_number, od.fulfilled_by, od.service_type_id';

        $items = DB::select($sql);

        if (isset($items)) { foreach ($items as $item) array_push($all_items, $item); }

        $client_count_array = [];
        $pack_type_order_number = [];
        $type_transit_count = [];
        $temp_count = [];

        if (isset($all_items)) {
            
            foreach ($all_items as $item) {                
                $pack_type = str_replace(' ', '_', $item->pack_type);
                $transit_day = $item->transit_days;
                $order_number = $item->sub_order_number;
                if (isset($client_count_array[$pack_type][$transit_day])) {
                    $old_order_number = $client_count_array[$pack_type][$transit_day];                    
                    $client_count_array[$pack_type][$transit_day] = $old_order_number . ',' . $order_number;
                } else {
                    $client_count_array[$pack_type][$transit_day] = $order_number;
                }
                if (isset($pack_type_order_number[$pack_type])) {
                    $old_order_number = $pack_type_order_number[$pack_type];
                    $pack_type_order_number[$pack_type] = $old_order_number . ',' . $order_number;
                } else {
                    $pack_type_order_number[$pack_type] = $order_number;
                }
            }

            foreach($all_items as $item) {
                $temp = get_temp($item->fulfilled_by, $item->sub_order_number);
                if (isset($temp) && $temp !== '') {
                    $comp = $item->company_name;
                    $transit_day = $item->transit_days;
                    $value = 1;
                    $sub_order = $item->sub_order_number;
                    $type = str_replace(' ', '_', $item->pack_type);
                    if (isset($type_transit_count[$type][$temp][$comp][$transit_day])) {
                        $value = $type_transit_count[$type][$temp][$comp][$transit_day][0] + 1;
                        $sub_order = $type_transit_count[$type][$temp][$comp][$transit_day][1] . ',' . $item->sub_order_number;
                    }
                    $type_transit_count[$type][$temp][$comp][$transit_day][0] = $value;                    
                    $type_transit_count[$type][$temp][$comp][$transit_day][1] = $sub_order;                    
                }
            }

            foreach($type_transit_count as $key => $values) {
                $pack = $key;            
                foreach($values as $value_key => $value) {
                    $temp = $value_key;
                    foreach($value as $v_key => $v) {
                        foreach (range(-1, 5) as $day) {
                            $day_order = isset($value[$v_key][$day][1]) ? $value[$v_key][$day][1] : '';
                            if (isset($temp_count[$pack][$temp][$day]) && $temp_count[$pack][$temp][$day] !== '') {
                                $day_order = $day_order !== ''
                                            ? $day_order . ',' . $temp_count[$pack][$temp][$day]
                                            : $temp_count[$pack][$temp][$day];
                            }
                            $temp_count[$pack][$temp][$day] = $day_order;
                        }
                    }
                }
            }
        }
        
        $count = [];
        $count['LINE_PACK'] = isset($pack_type_order_number['LINE_PACK'])
            ? $count['LINE_PACK'] = $pack_type_order_number['LINE_PACK']
            : '';
        $count['INDIVIDUAL_PACK'] = isset($pack_type_order_number['INDIVIDUAL_PACK'])
            ? $count['INDIVIDUAL_PACK'] = $pack_type_order_number['INDIVIDUAL_PACK']
            : '';

        return view('report_section.parts.order_table_transit',
            compact('client_count_array', 'input', 'count', 'type_transit_count', 'temp_count'));
    }

    public function GetClientOrdersByOrderStatus(Request $request){

        $input = $request->all();
        $start_date = $request->os_start_date;
        $end_date = $request->os_end_date;
        $client_id = $request->client_id_os;
        $all_items = array();
        $whs = $request->os_gr_warehouse;
        $wh_selected = '';
        if (isset($whs) && count($whs) > 0) {
            foreach($whs as $wh) {
                $wh_selected = $wh_selected . '\'' . $wh . '\', ';
            }
            $wh_selected = substr($wh_selected, 0, -2);
        }

        $oo_sql = 'SELECT c.company_name, os.etailer_order_number, \'Open Orders\' as order_status_name,
                case
                    when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                    else "LINE PACK"
                end as pack_type
                FROM order_details od
                INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
                inner join clients c on os.client_id = c.id
                inner join order_summary_status oss on os.order_status = oss.id
                where (os.order_status in (1, 2, 19, 23) 
                or od.status in (1, 2, 3, 4, 9, 10, 11, 12))
                and os.order_status not in (7, 8, 9) ';

        if (isset($client_id)) {
            $oo_sql = $oo_sql . 'and os.client_id in ('. implode(',', $client_id) .') ';            
        }

        if ($wh_selected !== '') {
            $oo_sql = $oo_sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $oo_sql = $oo_sql . 'group by c.company_name, os.etailer_order_number, oss.order_status_name';
        
        $items = DB::select($oo_sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);
        }
        
        $sql = 'SELECT c.company_name, os.etailer_order_number,
                case 
                    when oss.order_status_name = \'Hold Operations\' then \'Hold\'
                    when oss.order_status_name = \'Hold Scheduled\' then \'Hold\'
                    when oss.order_status_name = \'Hold Severe Weather\' then \'Hold\'
                    when oss.order_status_name = \'Hold-Payment\' then \'Hold\'
                end as order_status_name,
                case
                    when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                    else "LINE PACK"
                end as pack_type
                FROM order_details od
                INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
                inner join clients c on os.client_id = c.id
                inner join order_summary_status oss on os.order_status = oss.id
                where os.order_status in (10, 11, 12, 13)
                and CONVERT(od.created_at, DATE)  <= \''.$end_date.'\' ';

        if (isset($client_id)) {
            $sql = $sql . 'and os.client_id in ('. implode(',', $client_id) .') ';            
        }

        if ($wh_selected !== '') {
            $sql = $sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $sql = $sql . 'group by c.company_name, os.etailer_order_number, oss.order_status_name';

        $items = DB::select($sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);
        }

        $oos_sp_sql = 'SELECT c.company_name, os.etailer_order_number,
                case 
                    when oss.order_status_name = \'Shipped\' then \'Processed\'
                    when oss.order_status_name = \'Error: OOS\' then \'OOS\'
                end as order_status_name,
                case
                    when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                    else "LINE PACK"
                end as pack_type
                FROM order_details od
                INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
                inner join clients c on os.client_id = c.id
                inner join order_summary_status oss on os.order_status = oss.id
                where os.order_status in (26, 17)
                and CONVERT(od.created_at, DATE) between \''.$start_date.'\' and \''.$end_date.'\' ';

        if (isset($client_id)) {
            $oos_sp_sql = $oos_sp_sql . 'and os.client_id in ('. implode(',', $client_id) .') ';            
        }

        if ($wh_selected !== '') {
            $oos_sp_sql = $oos_sp_sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $oos_sp_sql = $oos_sp_sql . 'group by c.company_name, os.etailer_order_number, oss.order_status_name';

        $items = DB::select($oos_sp_sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);
        }

        $ats_sql = 'SELECT c.company_name, os.etailer_order_number,
                \'Available to Ship\' as order_status_name,
                case
                    when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                    else "LINE PACK"
                end as pack_type
                FROM order_details od
                INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
                inner join clients c on os.client_id = c.id
                inner join order_summary_status oss on os.order_status = oss.id
                where os.order_status in (1, 2, 19, 23) and od.transit_days is not null
                and CONVERT(od.created_at, DATE)  <= \''.$end_date.'\' ';

        if (isset($client_id)) {
            $ats_sql = $ats_sql . 'and os.client_id in ('. implode(',', $client_id) .') ';            
        }

        if ($wh_selected !== '') {
            $ats_sql = $ats_sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $ats_sql = $ats_sql . 'group by c.company_name, os.etailer_order_number, oss.order_status_name';

        $items = DB::select($ats_sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);
        }
        
        $client_count_array = [];
        $pack_type_order_number = [];        

        $mst_sql = 'SELECT c.company_name, os.etailer_order_number,
            \'Must Ship Today\' as order_status_name,
            case
                when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                else "LINE PACK"
            end as pack_type
            FROM order_details od
            INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
            inner join clients c on os.client_id = c.id
            inner join order_summary_status oss on os.order_status = oss.id
            where os.order_status in (1, 2, 19, 23)  and os.must_ship_today = 1 
            and CONVERT(od.created_at, DATE)  <= \''.$end_date.'\' ';

        if (isset($client_id)) {
            $mst_sql = $mst_sql . 'and os.client_id in ('. implode(',', $client_id) .') ';            
        }

        if ($wh_selected !== '') {
            $mst_sql = $mst_sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $mst_sql = $mst_sql . 'group by c.company_name, os.etailer_order_number, oss.order_status_name';

        $items = DB::select($mst_sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);
        }

        $pp_sql = 'SELECT c.company_name, os.etailer_order_number,
            case
                when od.status = 3 then "Picked"
                when od.status = 4 then "Packed"
            end as order_status_name,
            case
                when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                else "LINE PACK"
            end as pack_type
            FROM order_details od
            INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
            inner join clients c on os.client_id = c.id
            where od.status in (3, 4) 
            and CONVERT(od.created_at, DATE) between \''.$start_date.'\' and \''.$end_date.'\' ';

        if (isset($client_id)) {
            $pp_sql = $pp_sql . 'and os.client_id in ('. implode(',', $client_id) .') ';            
        }

        if ($wh_selected !== '') {
            $pp_sql = $pp_sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $pp_sql = $pp_sql . 'group by c.company_name, os.etailer_order_number, od.status';

        $items = DB::select($pp_sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);
        }

        if (isset($all_items)) {
            foreach ($all_items as $item) {                
                $pack_type = str_replace(' ', '_', $item->pack_type);
                $order_status = $item->order_status_name;
                $order_number = $item->etailer_order_number;
                if (isset($client_count_array[$pack_type][$order_status])) {
                    $prev_count = $client_count_array[$pack_type][$order_status];
                    $client_count_array[$pack_type][$order_status] = $prev_count += 1;
                } else {
                    $client_count_array[$pack_type][$order_status] = 1;
                }
                if (isset($pack_type_order_number['os_' . $pack_type])) {
                    $old_order_number = $pack_type_order_number['os_'. $pack_type];
                    $pack_type_order_number['os_' . $pack_type] = $old_order_number . ',' . $order_number;
                } else {
                    $pack_type_order_number['os_' . $pack_type] = $order_number;
                }
            }
        }
        
        $ip_oo = isset($client_count_array['INDIVIDUAL_PACK']['Open Orders']) 
                ? $client_count_array['INDIVIDUAL_PACK']['Open Orders'] : '0';
        $ip_ats = isset($client_count_array['INDIVIDUAL_PACK']['Available to Ship']) 
                ? $client_count_array['INDIVIDUAL_PACK']['Available to Ship'] : '0';
        $ip_oh = isset($client_count_array['INDIVIDUAL_PACK']['Hold']) 
                ? $client_count_array['INDIVIDUAL_PACK']['Hold'] : '0';
        $ip_pd = isset($client_count_array['INDIVIDUAL_PACK']['Processed']) 
                ? $client_count_array['INDIVIDUAL_PACK']['Processed'] : '0';
        $ip_mst = isset($client_count_array['INDIVIDUAL_PACK']['Must Ship Today']) 
                ? $client_count_array['INDIVIDUAL_PACK']['Must Ship Today'] : '0';
        $ip_picked = isset($client_count_array['INDIVIDUAL_PACK']['Picked']) 
                ? $client_count_array['INDIVIDUAL_PACK']['Picked'] : '0';
        $ip_packed = isset($client_count_array['INDIVIDUAL_PACK']['Packed']) 
                ? $client_count_array['INDIVIDUAL_PACK']['Packed'] : '0';
        $ip_oos = '0';

        $lp_oo = isset($client_count_array['LINE_PACK']['Open Orders']) 
                ? $client_count_array['LINE_PACK']['Open Orders'] : '0';
        $lp_ats = isset($client_count_array['LINE_PACK']['Available to Ship']) 
                ? $client_count_array['LINE_PACK']['Available to Ship'] : '0';
        $lp_oh = isset($client_count_array['LINE_PACK']['Hold']) 
                ? $client_count_array['LINE_PACK']['Hold'] : '0';
        $lp_pd = isset($client_count_array['LINE_PACK']['Processed']) 
                ? $client_count_array['LINE_PACK']['Processed'] : '0';
        $lp_mst = isset($client_count_array['LINE_PACK']['Must Ship Today']) 
                ? $client_count_array['LINE_PACK']['Must Ship Today'] : '0';
        $lp_picked = isset($client_count_array['LINE_PACK']['Picked']) 
                ? $client_count_array['LINE_PACK']['Picked'] : '0';
        $lp_packed = isset($client_count_array['LINE_PACK']['Packed']) 
                ? $client_count_array['LINE_PACK']['Packed'] : '0';
        $lp_oos = '0';

        $chart_data = "['INDIVIDUAL_PACK', ".$ip_oo.", ".$ip_mst.", ".$ip_ats.", ".$ip_oh.", ".$ip_oos.", ".$ip_pd.", ".$ip_picked.", ".$ip_packed."], 
                ['LINE_PACK', ".$lp_oo.", ".$lp_mst.", ".$lp_ats.", ".$lp_oh.", ".$lp_oos.", ".$lp_pd.", ".$lp_picked.", ".$lp_packed."]";
        return view('report_section.parts.order_chart_order_status',compact('chart_data','input', 'pack_type_order_number'));        
    }

    public function GetClientOrdersByOrderStatusModal(Request $request) {
        
        $input = $request->all();
        $start_date = str_replace('"', '', $request->os_start_date);
        $end_date = str_replace('"', '', $request->os_end_date);
        $client_id = $request->client_id_os;
        $all_items = array();

        $pack_type = 'os_' . $request->pack_type;
        $o_nums = explode(',', $request->$pack_type);
        $str_onums = '';
        foreach($o_nums as $o_num) {
            $str_onums = $str_onums . '\'' . $o_num . '\', ';
        }
        $str_onums = substr($str_onums, 0, -2);

        $whs = $request->os_gr_warehouse;
        $wh_selected = str_replace('[', '', $whs);
        $wh_selected = str_replace(']', '', $wh_selected);

        $oo_sql = 'SELECT c.company_name, os.etailer_order_number, \'Open Orders\' as order_status_name,
                case
                    when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                    else "LINE PACK"
                end as pack_type
                FROM order_details od
                INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
                inner join clients c on os.client_id = c.id
                inner join order_summary_status oss on os.order_status = oss.id
                where (os.order_status in (1, 2, 19, 23) 
                or od.status in (1, 2, 3, 4, 9, 10, 11, 12))
                and os.order_status not in (7, 8, 9)
                and os.etailer_order_number in (' . $str_onums .') ';

        if (isset($client_id)) {
            $oo_sql = $oo_sql . 'and os.client_id in ('. implode(',', $client_id) .') ';            
        }

        if ($wh_selected !== '') {
            $oo_sql = $oo_sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $oo_sql = $oo_sql . 'group by c.company_name, os.etailer_order_number, oss.order_status_name';
        
        $items = DB::select($oo_sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);
        }

        $mst_sql = 'SELECT c.company_name, os.etailer_order_number, od.sub_order_number,
                    \'Must Ship Today\' as order_status_name, od.fulfilled_by 
                    FROM order_details od
                    INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
                    inner join clients c on os.client_id = c.id
                    inner join order_summary_status oss on os.order_status = oss.id
                    where os.order_status in (1, 2, 19, 23)  and os.must_ship_today = 1
                    and os.etailer_order_number in (' . $str_onums .') 
                    and CONVERT(od.created_at, DATE)  <= \''.$end_date.'\' ';
        
        if (isset($client_id)) {
            $mst_sql = $mst_sql . 'and os.client_id in ('. implode(',', $client_id) .') ';  
        }

        if ($wh_selected !== '') {
            $mst_sql = $mst_sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $mst_sql = $mst_sql . ' group by c.company_name, os.etailer_order_number, od.sub_order_number, od.fulfilled_by';

        $items = DB::select($mst_sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);
        }

        $sql = 'SELECT c.company_name, os.etailer_order_number,
                    case 
                        when oss.order_status_name = \'Hold Operations\' then \'Hold\'
                        when oss.order_status_name = \'Hold Scheduled\' then \'Hold\'
                        when oss.order_status_name = \'Hold Severe Weather\' then \'Hold\'
                        when oss.order_status_name = \'Hold-Payment\' then \'Hold\'
                    end as order_status_name, od.fulfilled_by
                    case
                        when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                        else "LINE PACK"
                    end as pack_type
                    FROM order_details od
                    INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
                    inner join clients c on os.client_id = c.id
                    inner join order_summary_status oss on os.order_status = oss.id
                    where os.order_status in (10, 11, 12, 13)
                    and os.etailer_order_number in (' . $str_onums .') 
                    and CONVERT(od.created_at, DATE)  <= \''.$end_date.'\' ';
        
        if (isset($client_id)) {
            $sql = $sql . 'and os.client_id in ('. implode(',', $client_id) .') ';  
        }

        if ($wh_selected !== '') {
            $sql = $sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $sql = $sql . ' group by c.company_name, os.etailer_order_number, od.sub_order_number, od.fulfilled_by, oss.order_status_name';

        $items = DB::select($sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);
        }

        $oos_sp_sql = 'SELECT c.company_name, os.etailer_order_number,
                    case 
                        when oss.order_status_name = \'Shipped\' then \'Processed\'
                        when oss.order_status_name = \'Error: OOS\' then \'OOS\'
                    end as order_status_name, od.fulfilled_by
                    case
                        when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                        else "LINE PACK"
                    end as pack_type
                    FROM order_details od
                    INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
                    inner join clients c on os.client_id = c.id
                    inner join order_summary_status oss on os.order_status = oss.id
                    where os.order_status in (26, 17)
                    and os.etailer_order_number in (' . $str_onums .') 
                    and CONVERT(od.created_at, DATE) between \''.$start_date.'\' and \''.$end_date.'\' ';
        
        if (isset($client_id)) {
            $oos_sp_sql = $oos_sp_sql . 'and os.client_id in ('. implode(',', $client_id) .') ';  
        }

        if ($wh_selected !== '') {
            $oos_sp_sql = $oos_sp_sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $oos_sp_sql = $oos_sp_sql . ' group by c.company_name, os.etailer_order_number, od.sub_order_number, od.fulfilled_by, oss.order_status_name';

        $items = DB::select($oos_sp_sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);
        }        

        $ast_sql = 'SELECT c.company_name, os.etailer_order_number, od.sub_order_number,
                    \'Available to Ship\' as order_status_name, od.fulfilled_by 
                    FROM order_details od
                    INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
                    inner join clients c on os.client_id = c.id
                    inner join order_summary_status oss on os.order_status = oss.id
                    where where os.order_status in (1, 2, 19, 23) and od.transit_days is not null
                    and os.etailer_order_number in (' . $str_onums .') 
                    and CONVERT(od.created_at, DATE) <= \''.$end_date.'\' ';
        
        if (isset($client_id)) {
            $ast_sql = $ast_sql . 'and os.client_id in ('. implode(',', $client_id) .') ';  
        }

        if ($wh_selected !== '') {
            $ast_sql = $ast_sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $ast_sql = $ast_sql . ' group by c.company_name, os.etailer_order_number, od.sub_order_number, od.fulfilled_by';

        $items = DB::select($ast_sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);
        }

        $pp_sql = 'SELECT c.company_name, os.etailer_order_number, od.sub_order_number,
                    case
                        when od.status = 3 then "Picked"
                        when od.status = 4 then "Packed"
                    end as order_status_name, od.fulfilled_by 
                    FROM order_details od
                    INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
                    inner join clients c on os.client_id = c.id
                    where od.status in (3, 4)
                    and os.etailer_order_number in (' . $str_onums .') 
                    and CONVERT(od.created_at, DATE) between \''.$start_date.'\' and \''.$end_date.'\' ';
        
        if (isset($client_id)) {
            $pp_sql = $pp_sql . 'and os.client_id in ('. implode(',', $client_id) .') ';  
        }

        if ($wh_selected !== '') {
            $pp_sql = $pp_sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $pp_sql = $pp_sql . ' group by c.company_name, os.etailer_order_number, od.sub_order_number, od.fulfilled_by, od.status';

        $items = DB::select($pp_sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);
        }

        $type_status_count = [];
        $temp_count = [];

        if (isset($all_items)) {
            foreach($all_items as $item) {
                $temp = get_temp($item->fulfilled_by, $item->sub_order_number);
                if (isset($temp) && $temp !== '') {
                    $comp = $item->company_name;
                    $status = $item->order_status_name;
                    $sub_order = $item->sub_order_number;
                    $on = $item->etailer_order_number;
                    if (isset($type_status_count[$temp][$comp][$status])) {
                        $on = $type_status_count[$temp][$comp][$status][0] . ',' . $item->etailer_order_number;
                        $sub_order = $type_status_count[$temp][$comp][$status][1] . ',' . $item->sub_order_number;
                    }
                    $type_status_count[$temp][$comp][$status][0] = $on;                    
                    $type_status_count[$temp][$comp][$status][1] = $sub_order;                    
                }
            }

            foreach($type_status_count as $key => $values) {
                foreach($values as $value_key => $value) {
                    $temp = $key;
                    $oo = isset($values[$value_key]['Open Orders'][0]) ? $values[$value_key]['Open Orders'][0] : '';
                    $mst = isset($values[$value_key]['Must Ship Today'][0]) ? $values[$value_key]['Must Ship Today'][0] : '';
                    $ats = isset($values[$value_key]['Available to Ship'][0]) ? $values[$value_key]['Available to Ship'][0] : '';
                    $oh = isset($values[$value_key]['Hold'][0]) ? $values[$value_key]['Hold'][0] : '';
                    $oos = isset($values[$value_key]['OOS'][0]) ? $values[$value_key]['OOS'][0] : '';
                    $pd = isset($values[$value_key]['Processed'][0]) ? $values[$value_key]['Processed'][0] : '';
                    $pi = isset($values[$value_key]['Picked'][0]) ? $values[$value_key]['Picked'][0] : '';
                    $pa = isset($values[$value_key]['Packed'][0]) ? $values[$value_key]['Packed'][0] : '';
                    if (isset($temp_count[$temp]['OOS'])) {
                        $oos = $oos !== '' 
                        ? $oos . ',' . $temp_count[$temp]['OOS'] 
                        : $temp_count[$temp]['OOS'];
                    }
                    if (isset($temp_count[$temp]['Hold'])) {
                        $oh = $oh !== '' 
                            ? $oh . ',' . $temp_count[$temp]['Hold'] 
                            : $temp_count[$temp]['Hold'];
                    }
                    if (isset($temp_count[$temp]['Open Orders'])) {
                        $oo = $oo !== '' 
                            ? $oo . ',' . $temp_count[$temp]['Open Orders']
                            : $temp_count[$temp]['Open Orders'];
                    }
                    if (isset($temp_count[$temp]['Must Ship Today'])) {
                        $mst = $mst !== '' 
                            ? $mst . ',' . $temp_count[$temp]['Must Ship Today'] 
                            : $temp_count[$temp]['Must Ship Today'] ;
                    }
                    if (isset($temp_count[$temp]['Available to Ship'])) {
                        $ats = $ats !== '' 
                            ? $ats . ',' . $temp_count[$temp]['Available to Ship']
                            : $temp_count[$temp]['Available to Ship'];
                    }
                    if (isset($temp_count[$temp]['Processed'])) {
                        $pd = $pd !== '' 
                        ? $pd . ',' . $temp_count[$temp]['Processed'] 
                        : $temp_count[$temp]['Processed'];
                    }
                    if (isset($temp_count[$temp]['Picked'])) {
                        $pi = $pi !== '' 
                        ? $pi . ',' . $temp_count[$temp]['Picked'] 
                        : $temp_count[$temp]['Picked'];
                    }
                    if (isset($temp_count[$temp]['Packed'])) {
                        $pa = $pa !== '' 
                        ? $pa . ',' . $temp_count[$temp]['Packed'] 
                        : $temp_count[$temp]['Packed'];
                    }
                    $temp_count[$temp]['Must Ship Today'] = $mst;
                    $temp_count[$temp]['Available to Ship'] = $ats;
                    $temp_count[$temp]['Open Orders'] = $oo;
                    $temp_count[$temp]['Hold'] = $oh;
                    $temp_count[$temp]['OOS'] = $oos;
                    $temp_count[$temp]['Processed'] = $pd;
                    $temp_count[$temp]['Picked'] = $pi;
                    $temp_count[$temp]['Packed'] = $pa;
                }
            }  
        }
        return view('report_section.parts.model_total_os_chart',compact('type_status_count', 'temp_count'));
    }

    public function GetOrderStatusTable(Request $request){
        
        $input = $request->all();
        $start_date = $request->os_tb_start_date;
        $end_date = $request->os_tb_end_date;
        $client_id = $request->os_tb_client_id;
        $whs = $request->os_warehouse;
        $wh_selected = '';
        if (isset($whs) && count($whs) > 0) {
            foreach($whs as $wh) {
                $wh_selected = $wh_selected . '\'' . $wh . '\', ';
            }
            $wh_selected = substr($wh_selected, 0, -2);
        }

        $all_items = array();
        
        $oo_sql = 'SELECT c.company_name, os.etailer_order_number, \'Open Orders\' as order_status_name,
                case
                    when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                    else "LINE PACK"
                end as pack_type, od.fulfilled_by, od.sub_order_number
                FROM order_details od
                INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
                inner join clients c on os.client_id = c.id
                inner join order_summary_status oss on os.order_status = oss.id
                where (os.order_status in (1, 2, 19, 23) 
                or od.status in (1, 2, 3, 4, 9, 10, 11, 12))
                and os.order_status not in (7, 8, 9) ';

        if (isset($client_id)) {
            $oo_sql = $oo_sql . 'and os.client_id in ('. implode(',', $client_id) .') ';            
        }

        if ($wh_selected !== '') {
            $oo_sql = $oo_sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $oo_sql = $oo_sql . 'group by c.company_name, os.etailer_order_number, oss.order_status_name';
        
        $items = DB::select($oo_sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);
        }

        $sql = 'SELECT c.company_name, os.etailer_order_number, 
                case 
                    when oss.order_status_name = \'Hold Operations\' then \'Hold\'
                    when oss.order_status_name = \'Hold Scheduled\' then \'Hold\'
                    when oss.order_status_name = \'Hold Severe Weather\' then \'Hold\'
                    when oss.order_status_name = \'Hold-Payment\' then \'Hold\'
                end as order_status_name, od.fulfilled_by, od.sub_order_number,
                case
                    when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                    else "LINE PACK"
                end as pack_type
                FROM order_details od
                INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
                inner join clients c on os.client_id = c.id
                inner join order_summary_status oss on os.order_status = oss.id
                where CONVERT(od.created_at, DATE)  <= \''.$end_date.'\'
                and os.order_status in (10, 11, 12, 13) ';

        if (isset($client_id)) {
            $sql = $sql . 'and os.client_id in ('. implode(',', $client_id) .') ';            
        }

        if ($wh_selected !== '') {
            $sql = $sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }        

        $sql = $sql . ' group by c.company_name, os.etailer_order_number, od.sub_order_number, od.fulfilled_by, oss.order_status_name';

        $items = DB::select($sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);
        }

        $oos_sp_sql = 'SELECT c.company_name, os.etailer_order_number, 
                case 
                    when oss.order_status_name = \'Shipped\' then \'Processed\'
                    when oss.order_status_name = \'Error: OOS\' then \'OOS\'
                end as order_status_name, od.fulfilled_by, od.sub_order_number,
                case
                    when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                    else "LINE PACK"
                end as pack_type
                FROM order_details od
                INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
                inner join clients c on os.client_id = c.id
                inner join order_summary_status oss on os.order_status = oss.id
                where CONVERT(od.created_at, DATE) between \''.$start_date.'\' and \''.$end_date.'\'
                and os.order_status in (26, 17) ';

        if (isset($client_id)) {
            $oos_sp_sql = $oos_sp_sql . 'and os.client_id in ('. implode(',', $client_id) .') ';            
        }

        if ($wh_selected !== '') {
            $oos_sp_sql = $oos_sp_sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }        

        $oos_sp_sql = $oos_sp_sql . ' group by c.company_name, os.etailer_order_number, od.sub_order_number, od.fulfilled_by, oss.order_status_name';

        $items = DB::select($oos_sp_sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);
        }
        
        $mst_sql = 'SELECT c.company_name, os.etailer_order_number, od.sub_order_number, od.fulfilled_by,
            \'Must Ship Today\' as order_status_name,
            case
                when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                else "LINE PACK"
            end as pack_type
            FROM order_details od
            INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
            inner join clients c on os.client_id = c.id
            inner join order_summary_status oss on os.order_status = oss.id
            where os.order_status in (1, 2, 19, 23)  and os.must_ship_today = 1
            and CONVERT(od.created_at, DATE)  <= \''.$end_date.'\' ';

        if (isset($client_id)) {
            $mst_sql = $mst_sql . 'and os.client_id in ('. implode(',', $client_id) .')';            
        }

        if ($wh_selected !== '') {
            $mst_sql = $mst_sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $mst_sql = $mst_sql . ' group by c.company_name, os.etailer_order_number, od.sub_order_number, od.fulfilled_by, oss.order_status_name';

        $items = DB::select($mst_sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);            
        }

        $ats_sql = 'SELECT c.company_name, os.etailer_order_number, od.sub_order_number, od.fulfilled_by,
            \'Available to Ship\' as order_status_name,
            case
                when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                else "LINE PACK"
            end as pack_type
            FROM order_details od
            INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
            inner join clients c on os.client_id = c.id
            inner join order_summary_status oss on os.order_status = oss.id
            where os.order_status in (1, 2, 19, 23) and od.transit_days is not null
            and CONVERT(od.created_at, DATE) <= \''.$end_date.'\' ';

        if (isset($client_id)) {
            $ats_sql = $ats_sql . 'and os.client_id in ('. implode(',', $client_id) .')';            
        }

        if ($wh_selected !== '') {
            $ats_sql = $ats_sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $ats_sql = $ats_sql . ' group by c.company_name, os.etailer_order_number, od.sub_order_number, od.fulfilled_by, oss.order_status_name';    
        $items = DB::select($ats_sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);
        }

        $ats_sql = 'SELECT c.company_name, os.etailer_order_number, od.sub_order_number, od.fulfilled_by,
            case
                when od.status = 3 then "Picked"
                when od.status = 4 then "Packed"
            end as order_status_name,
            case
                when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                else "LINE PACK"
            end as pack_type
            FROM order_details od
            INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
            inner join clients c on os.client_id = c.id
            where od.status in (3, 4)
            and CONVERT(od.created_at, DATE)  <= \''.$end_date.'\' ';

        if (isset($client_id)) {
            $ats_sql = $ats_sql . 'and os.client_id in ('. implode(',', $client_id) .')';            
        }

        if ($wh_selected !== '') {
            $ats_sql = $ats_sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $ats_sql = $ats_sql . ' group by c.company_name, os.etailer_order_number, od.sub_order_number, od.fulfilled_by, od.status';    
        $items = DB::select($ats_sql);
        if (isset($items)) {
            foreach($items as $item) array_push($all_items, $item);
        }

        $client_count_array = [];
        $pack_type_order_number = [];
        $type_status_count = [];
        $temp_count = [];

        if (isset($all_items)) {
            
            foreach ($all_items as $item) {                
                $pack_type = str_replace(' ', '_', $item->pack_type);
                $order_status = $item->order_status_name;
                $order_number = $item->etailer_order_number;
                if (isset($client_count_array[$pack_type][$order_status])) {
                    $old_order_number = $client_count_array[$pack_type][$order_status];
                    $client_count_array[$pack_type][$order_status] = $old_order_number . ',' . $order_number;
                } else {
                    $client_count_array[$pack_type][$order_status] = $order_number;
                }
                if (isset($pack_type_order_number[$pack_type])) {
                    $old_order_number = $pack_type_order_number[$pack_type];
                    $pack_type_order_number[$pack_type] = $old_order_number . ',' . $order_number;
                } else {
                    $pack_type_order_number[$pack_type] = $order_number;
                }
            }

            foreach($all_items as $item) {
                $temp = get_temp($item->fulfilled_by, $item->sub_order_number);
                $o_type = $item->pack_type;
                if (isset($temp) && $temp !== '') {
                    $comp = $item->company_name;
                    $status = $item->order_status_name;
                    $value = 1;
                    $sub_order = $item->sub_order_number;
                    $on = $item->etailer_order_number;
                    if (isset($type_status_count[$o_type][$temp][$comp][$status])) {
                        $on = $type_status_count[$o_type][$temp][$comp][$status][0] . ',' . $item->etailer_order_number;
                        $sub_order = $type_status_count[$o_type][$temp][$comp][$status][1] . ',' . $item->sub_order_number;
                    }
                    $type_status_count[$o_type][$temp][$comp][$status][0] = $on;                    
                    $type_status_count[$o_type][$temp][$comp][$status][1] = $sub_order;                    
                }
            }

            foreach($type_status_count as $key => $values) {
                $pack = $key;            
                foreach($values as $value_key => $value) {
                    $temp = $value_key;
                    foreach($value as $v_key => $v) {
                        $oo = isset($value[$v_key]['Open Orders'][0]) ? $value[$v_key]['Open Orders'][0] : '';
                        $mst = isset($value[$v_key]['Must Ship Today'][0]) ? $value[$v_key]['Must Ship Today'][0] : '';
                        $ats = isset($value[$v_key]['Available to Ship'][0]) ? $value[$v_key]['Available to Ship'][0] : '';
                        $oh = isset($value[$v_key]['Hold'][0]) ? $value[$v_key]['Hold'][0] : '';
                        $oos = isset($value[$v_key]['OOS'][0]) ? $value[$v_key]['OOS'][0] : '';
                        $pd = isset($value[$v_key]['Processed'][0]) ? $value[$v_key]['Processed'][0] : '';
                        $pi = isset($value[$v_key]['Picked'][0]) ? $value[$v_key]['Picked'][0] : '';
                        $pa = isset($value[$v_key]['Packed'][0]) ? $value[$v_key]['Packed'][0] : '';
                        if (isset($temp_count[$pack][$temp]['Processed']) && $temp_count[$pack][$temp]['Processed'] !== '') {
                            $pd = $pd !== '' 
                                ? $temp_count[$pack][$temp]['Processed'] . ',' . $pd
                                : $temp_count[$pack][$temp]['Processed'];
                        }
                        if (isset($temp_count[$pack][$temp]['OOS']) && $temp_count[$pack][$temp]['OOS'] !== '') {
                            $oos = $oos !== '' 
                            ? $temp_count[$pack][$temp]['OOS'] . ',' . $oos
                            : $temp_count[$pack][$temp]['OOS'];
                        }
                        if (isset($temp_count[$pack][$temp]['Hold']) && $temp_count[$pack][$temp]['Hold'] !== '') {
                            $oh = $oh !== '' 
                                ? $temp_count[$pack][$temp]['Hold'] . ',' . $oh
                                : $temp_count[$pack][$temp]['Hold'];
                        }
                        if (isset($temp_count[$pack][$temp]['Open Orders']) && $temp_count[$pack][$temp]['Open Orders'] !== '') {
                            $oo = $oo !== '' 
                                ? $temp_count[$pack][$temp]['Open Orders'] . ',' . $oo
                                : $temp_count[$pack][$temp]['Open Orders'];
                        }
                        if (isset($temp_count[$pack][$temp]['Must Ship Today']) && $temp_count[$pack][$temp]['Must Ship Today'] !== '') {
                            $mst = $mst !== '' 
                                ? $temp_count[$pack][$temp]['Must Ship Today'] . ',' . $mst
                                : $temp_count[$pack][$temp]['Must Ship Today'] ;
                        }
                        if (isset($temp_count[$pack][$temp]['Available to Ship']) && $temp_count[$pack][$temp]['Available to Ship'] !== '') {
                            $ats = $ats !== '' 
                                ? $temp_count[$pack][$temp]['Available to Ship'] . ',' . $ats
                                : $temp_count[$pack][$temp]['Available to Ship'];
                        }
                        if (isset($temp_count[$pack][$temp]['Picked']) && $temp_count[$pack][$temp]['Picked'] !== '') {
                            $pi = $pi !== '' 
                                ? $temp_count[$pack][$temp]['Picked'] . ',' . $pi
                                : $temp_count[$pack][$temp]['Picked'];
                        }
                        if (isset($temp_count[$pack][$temp]['Packed']) && $temp_count[$pack][$temp]['Packed'] !== '') {
                            $pa = $pa !== '' 
                                ? $temp_count[$pack][$temp]['Packed'] . ',' . $pa
                                : $temp_count[$pack][$temp]['Packed'];
                        }
                        $temp_count[$pack][$temp]['Must Ship Today'] = $mst;
                        $temp_count[$pack][$temp]['Available to Ship'] = $ats;
                        $temp_count[$pack][$temp]['Open Orders'] = $oo;
                        $temp_count[$pack][$temp]['Hold'] = $oh;
                        $temp_count[$pack][$temp]['OOS'] = $oos;
                        $temp_count[$pack][$temp]['Processed'] = $pd;
                        $temp_count[$pack][$temp]['Picked'] = $pi;
                        $temp_count[$pack][$temp]['Packed'] = $pa;                        
                    }                    
                }
            }
        }

        $count = [];
        $count['LINE_PACK'] = isset($pack_type_order_number['LINE_PACK'])
            ? $count['LINE_PACK'] = $pack_type_order_number['LINE_PACK']
            : '';
        $count['INDIVIDUAL_PACK'] = isset($pack_type_order_number['INDIVIDUAL_PACK'])
            ? $count['INDIVIDUAL_PACK'] = $pack_type_order_number['INDIVIDUAL_PACK']
            : '';

        return view('report_section.parts.order_table_order_status',
            compact('client_count_array', 'input', 'count', 'type_status_count', 'temp_count'));
    }

    public function GetOrderModal(Request $request) {

        $so_nums = isset($request->sub_orders) ? explode(',', $request->sub_orders) : NULL;
        $o_nums = isset($request->orders) ? explode(',', $request->orders) : NULL;
        $os = $request->os;

        $result = OrderSummary::join('order_details as od', 'od.order_number' , '=', 'order_summary.etailer_order_number')
            ->leftjoin('users as u', 'u.id', '=', 'od.picker_id')
            ->leftJoin('carriers', 'carriers.id', '=', 'od.carrier_id')
            ->join('order_summary_status as oss', 'order_summary.order_status', '=', 'oss.id')
            ->whereIn($so_nums != NULL ? 'od.sub_order_number' : 'od.order_number', $so_nums != NULL ? $so_nums : $o_nums)
            ->distinct()
            ->get(['order_summary.*', 'oss.order_status_name', 'u.name as picker_name', 'carriers.company_name as carrier_name', 'od.service_type_id']);        

        $ups_st = ShippingServiceType::whereIn('id', [1, 2, 3, 7, 8, 11])->where('service_type', 'ups')->get(['id', 'service_name']);
		$fedex_st = ShippingServiceType::where('service_type', 'fedex')->get(['id', 'service_name']);

        return view('report_section.parts.model_totalorder_chart', compact('result','so_nums', 'o_nums', 'os', 'ups_st', 'fedex_st'));
    }

    public function GetClientOrdersTableByShipDay(Request $request){

        $input = $request->all();
        $start_date = $request->sd_tb_start_date;
        $end_date = $request->sd_tb_end_date;
        $client_id = $request->sd_tb_client_id;
        $whs = $request->sd_warehouse;
        $wh_selected = '';
        if (isset($whs) && count($whs) > 0) {
            foreach($whs as $wh) {
                $wh_selected = $wh_selected . '\'' . $wh . '\', ';
            }
            $wh_selected = substr($wh_selected, 0, -2);
        }
        
        $sql = 'SELECT c.company_name, od.sub_order_number, op.ship_day, od.fulfilled_by, 
            case
                when sum(od.customer_paid_price) > 150 then "INDIVIDUAL PACK"
                else "LINE PACK"
            end as pack_type
            FROM order_details od
            INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
            inner join order_packages op on op.order_id = od.sub_order_number and op.Order_ETIN  = od.ETIN
            inner join clients c on os.client_id = c.id
            where od.status = 6 and op.ship_day is not null
            and CONVERT(op.ship_date, DATE) between \''.$start_date.'\' and \''.$end_date.'\' ';
        
        if (isset($client_id)) {
            $sql = $sql . 'and os.client_id in ('. implode(',', $client_id) .') ';            
        }

        if ($wh_selected !== '') {
            $sql = $sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }
        
        $sql = $sql . 'group by c.company_name, od.sub_order_number, od.fulfilled_by , op.ship_day';

        $items = DB::select($sql);
        
        $client_count_array = [];
        $pack_type_order_number = [];
        $type_ship_day_count = [];
        $temp_count = [];
        if (isset($items)) {
            
            foreach ($items as $item) {                
                $pack_type = str_replace(' ', '_', $item->pack_type);
                $ship_day = $item->ship_day;
                $order_number = $item->sub_order_number;
                if (isset($client_count_array[$pack_type][$ship_day])) {
                    $old_order_number = $client_count_array[$pack_type][$ship_day];
                    $client_count_array[$pack_type][$ship_day] = $old_order_number . ',' . $order_number;
                } else {
                    $client_count_array[$pack_type][$ship_day] = $order_number;
                }
                if (isset($pack_type_order_number[$pack_type])) {
                    $old_order_number = $pack_type_order_number[$pack_type];
                    $pack_type_order_number[$pack_type] = $old_order_number . ',' . $order_number;
                } else {
                    $pack_type_order_number[$pack_type] = $order_number;
                }
            }
        
            foreach($items as $item) {
                $temp = get_temp($item->fulfilled_by, $item->sub_order_number);
                if (isset($temp) && $temp !== '') {
                    $comp = $item->company_name;
                    $ship_day = $item->ship_day;
                    $value = 1;
                    $sub_order = $item->sub_order_number;
                    $type = str_replace(' ', '_', $item->pack_type);
                    if (isset($type_ship_day_count[$type][$temp][$comp][$ship_day])) {
                        $value = $type_ship_day_count[$type][$temp][$comp][$ship_day][0] + 1;
                        $sub_order = $type_ship_day_count[$type][$temp][$comp][$ship_day][1] . ',' . $item->sub_order_number;
                    }
                    $type_ship_day_count[$type][$temp][$comp][$ship_day][0] = $value;                    
                    $type_ship_day_count[$type][$temp][$comp][$ship_day][1] = $sub_order;                    
                }
            }
        
            foreach($type_ship_day_count as $key => $values) {
                $pack = $key;            
                foreach($values as $value_key => $value) {
                    $temp = $value_key;
                    foreach($value as $v_key => $v) {
                        foreach (range(1, 5) as $day) {
                            $day_order = isset($value[$v_key][$day][1]) ? $value[$v_key][$day][1] : '';
                            if (isset($temp_count[$pack][$temp][$day]) && $temp_count[$pack][$temp][$day] !== '') {
                                $day_order = $day_order !== ''
                                            ? $day_order . ',' . $temp_count[$pack][$temp][$day]
                                            : $temp_count[$pack][$temp][$day];
                            }
                            $temp_count[$pack][$temp][$day] = $day_order;
                        }                        
                    }
                }
            }
        }
        
        $count = [];
        $count['LINE_PACK'] = isset($pack_type_order_number['LINE_PACK'])
            ? $count['LINE_PACK'] = $pack_type_order_number['LINE_PACK']
            : '';
        $count['INDIVIDUAL_PACK'] = isset($pack_type_order_number['INDIVIDUAL_PACK'])
            ? $count['INDIVIDUAL_PACK'] = $pack_type_order_number['INDIVIDUAL_PACK']
            : '';

        return view('report_section.parts.order_table_ship_day',
            compact('client_count_array', 'input', 'count', 'type_ship_day_count', 'temp_count'));
    }

    public function GetClientOrdersGraphByShipDay(Request $request){

        $input = $request->all();
        $start_date = $request->sd_start_date;
        $end_date = $request->sd_end_date;
        $client_id = $request->client_id_sd_gr;
        $whs = $request->sd_gr_warehouse;
        $wh_selected = '';
        if (isset($whs) && count($whs) > 0) {
            foreach($whs as $wh) {
                $wh_selected = $wh_selected . '\'' . $wh . '\', ';
            }
            $wh_selected = substr($wh_selected, 0, -2);
        }
        
        $sql = 'SELECT c.company_name, od.sub_order_number, op.ship_day,
                case
                    when sum(od.customer_paid_price) > 60 then "INDIVIDUAL PACK"
                    else "LINE PACK"
                end as pack_type
                FROM order_details od
                INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
                inner join order_packages op on op.order_id = od.sub_order_number and op.Order_ETIN  = od.ETIN
                inner join clients c on os.client_id = c.id
                where od.status = 6
                and CONVERT(op.ship_date, DATE) between \''.$start_date.'\' and \''.$end_date.'\' ';

        if (isset($client_id)) {
            $sql = $sql . 'and os.client_id in ('. implode(',', $client_id) .')';            
        }

        if ($wh_selected !== '') {
            $sql = $sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $sql = $sql . 'group by c.company_name, od.sub_order_number, op.ship_day';
        
        $items = DB::select($sql);
        $client_count_array = [];
        $pack_type_order_number = [];
        if (isset($items)) {
            foreach ($items as $item) {                
                $pack_type = str_replace(' ', '_', $item->pack_type);
                $ship_day = $item->ship_day;
                $order_number = $item->sub_order_number;
                if (isset($client_count_array[$pack_type][$ship_day])) {
                    $prev_count = $client_count_array[$pack_type][$ship_day];
                    $client_count_array[$pack_type][$ship_day] = $prev_count += 1;
                } else {
                    $client_count_array[$pack_type][$ship_day] = 1;
                }
                if (isset($pack_type_order_number[$pack_type])) {
                    $old_order_number = $pack_type_order_number[$pack_type];
                    $pack_type_order_number[$pack_type] = $old_order_number . ',' . $order_number;
                } else {
                    $pack_type_order_number[$pack_type] = $order_number;
                }
            }
        }

        $ip_mon = isset($client_count_array['INDIVIDUAL_PACK']['1']) ? $client_count_array['INDIVIDUAL_PACK']['1'] : '0';
        $ip_tues = isset($client_count_array['INDIVIDUAL_PACK']['2']) ? $client_count_array['INDIVIDUAL_PACK']['2'] : '0';
        $ip_wed = isset($client_count_array['INDIVIDUAL_PACK']['3']) ? $client_count_array['INDIVIDUAL_PACK']['3'] : '0';
        $ip_thurs = isset($client_count_array['INDIVIDUAL_PACK']['4']) ? $client_count_array['INDIVIDUAL_PACK']['4'] : '0';
        $ip_fri = isset($client_count_array['INDIVIDUAL_PACK']['5']) ? $client_count_array['INDIVIDUAL_PACK']['5'] : '0';
        
        $lp_mon = isset($client_count_array['LINE_PACK']['1']) ? $client_count_array['LINE_PACK']['1'] : '0';
        $lp_tues = isset($client_count_array['LINE_PACK']['2']) ? $client_count_array['LINE_PACK']['2'] : '0';
        $lp_wed = isset($client_count_array['LINE_PACK']['3']) ? $client_count_array['LINE_PACK']['3'] : '0';
        $lp_thurs = isset($client_count_array['LINE_PACK']['4']) ? $client_count_array['LINE_PACK']['4'] : '0';
        $lp_fri = isset($client_count_array['LINE_PACK']['5']) ? $client_count_array['LINE_PACK']['5'] : '0';

        $chart_data = "['INDIVIDUAL_PACK', ".$ip_mon.", ".$ip_tues.", ".$ip_wed.", ".$ip_thurs.", ".$ip_fri."], ['LINE_PACK', ".$lp_mon.", ".$lp_tues.", ".$lp_wed.", ".$lp_thurs.", ".$lp_fri."]";
        // dd($chart_data);

        return view('report_section.parts.order_chart_ship_day', compact('chart_data', 'input', 'pack_type_order_number'));
    }

    public function GetClientOrdersModalByShipDay(Request $request){
        $start_date = str_replace('"', '', $request->sd_start_date);
        $end_date = str_replace('"', '', $request->sd_end_date);
        $client_id = $request->client_id_sd_gr;
        $pack_type = $request->pack_type;
        $o_nums = explode(',', $request->$pack_type);
        $str_onums = '';
        foreach($o_nums as $o_num) {
            $str_onums = $str_onums . '\'' . $o_num . '\', ';
        }
        $str_onums = substr($str_onums, 0, -2);

        $whs = $request->sd_gr_warehouse;
        $wh_selected = str_replace('[', '', $whs);
        $wh_selected = str_replace(']', '', $wh_selected);

        $sql = 'SELECT c.company_name, od.sub_order_number, od.fulfilled_by , op.ship_day 
            FROM order_details od
            INNER JOIN order_summary os ON od.order_number = os.etailer_order_number
            inner join clients c on os.client_id = c.id
            inner join order_packages op on op.order_id = od.sub_order_number and op.Order_ETIN  = od.ETIN
            where od.status = 6
            and od.sub_order_number in ('. $str_onums .') 
            and CONVERT(op.ship_date, DATE) between \''.$start_date.'\' and \''.$end_date.'\' ';

        if (isset($client_id)) {
            $sql = $sql . 'and os.client_id in ('. implode(',', $client_id) .') ';            
        }

        if ($wh_selected !== '') {
            $sql = $sql . 'and od.warehouse in ('. $wh_selected .') ';            
        }

        $sql = $sql . ' group by c.company_name, od.sub_order_number, od.fulfilled_by , op.ship_day';
        
        $items = DB::select($sql);

        $type_ship_day_count = [];

        if (isset($items)) {
            foreach($items as $item) {
                $temp = get_temp($item->fulfilled_by, $item->sub_order_number);
                if (isset($temp) && $temp !== '') {
                    $comp = $item->company_name;
                    $ship_day = $item->ship_day;
                    $value = 1;
                    $sub_order = $item->sub_order_number;
                    if (isset($type_ship_day_count[$temp][$comp][$ship_day])) {
                        $value = $type_ship_day_count[$temp][$comp][$ship_day][0] + 1;
                        $sub_order = $type_ship_day_count[$temp][$comp][$ship_day][1] . ',' . $item->sub_order_number;
                    }
                    $type_ship_day_count[$temp][$comp][$ship_day][0] = $value;                    
                    $type_ship_day_count[$temp][$comp][$ship_day][1] = $sub_order;                    
                }
            }
        }

        $temp_count = [];
        foreach($type_ship_day_count as $key => $values) {
            foreach($values as $value_key => $value) {
                $temp = $key;
                foreach (range(1, 5) as $day) {
                    $day_order = isset($values[$value_key][$day][1]) ? $values[$value_key][$day][1] : '';
                    if (isset($temp_count[$temp][$day]) && $temp_count[$temp][$day] !== '') {
                        $day_order = $day_order !== ''
                                    ? $day_order . ',' . $temp_count[$temp][$day]
                                    : $temp_count[$temp][$day];
                    }
                    $temp_count[$temp][$day] = $day_order;
                }                               
            }
        }


        return view('report_section.parts.model_total_sd_chart',compact('type_ship_day_count', 'temp_count'));
    }

    public function TotalOrderCSVDownload(Request $request) {

        $is_os = isset($request->is_os);
        $so_nums = isset($request->sub_orders) ? explode(',', $request->sub_orders) : NULL;
        $o_nums = isset($request->orders) ? explode(',', $request->orders) : NULL;

        $result = OrderSummary::join('order_details as od', 'od.order_number' , '=', 'order_summary.etailer_order_number')
            ->leftjoin('users as u', 'u.id', '=', 'od.picker_id')
            ->leftJoin('carriers', 'carriers.id', '=', 'od.carrier_id')
            ->join('order_summary_status as oss', 'order_summary.order_status', '=', 'oss.id')
            ->whereIn($so_nums != NULL ? 'od.sub_order_number' : 'od.order_number', $so_nums != NULL ? $so_nums : $o_nums)
            ->distinct()
            ->get(['order_summary.*', 'oss.order_status_name', 'u.name as picker_name', 'carriers.company_name as carrier_name', 'od.service_type_id']); 
        
        $ups_st = ShippingServiceType::whereIn('id', [1, 2, 3, 7, 8, 11])->where('service_type', 'ups')->get(['id', 'service_name']);
        $fedex_st = ShippingServiceType::where('service_type', 'fedex')->get(['id', 'service_name']);

        $to_export = [];
        if (isset($result) && count($result) > 0) {
            foreach ($result as $row) {
                $service_name = '';
                if (isset($row->carrier_name) && $row->carrier_name !== '') {
                    if (strtolower($row->carrier_name) === 'ups') {
                        foreach ($ups_st as $ups)
                        if ($row->service_type_id == $ups->id) {
                            $service_name = $ups->service_name;
                            break;
                        }
                    } elseif (strtolower($row->carrier_name) === 'fedex') {                         
                        foreach ($fedex_st as $fedex) {
                            if ($row->service_type_id == $fedex->id) {
                                $service_name = $fedex->service_name;
                                break;
                            }
                        }                                        
                    }
                }
                array_push($to_export, 
                     $is_os ? [
                        'created_at' => $row->created_at,
                        'etailer_order_number' => $row->etailer_order_number,
                        'client_name' => $row->client_name,
                        'order_source' => $row->order_source,
                        'state' => $row->ship_to_state,
                        'delivery_date' => $row->channel_estimated_delivery_date,
                        'ship_date' => isset($row->ship_by_date) ? $row->ship_by_date : '',
                        'shipper' => $service_name,
                        'picker' => $row->picker_name,
                        'status_name' => isset($row->order_status_name) ? $row->order_status_name : '',
                    ]
                    : [
                        'created_at' => $row->created_at,
                        'etailer_order_number' => $row->etailer_order_number,
                        'client_name' => $row->client_name,
                        'order_source' => $row->order_source,
                        'state' => $row->ship_to_state,
                        'delivery_date' => $row->channel_estimated_delivery_date,
                        'ship_date' => isset($row->ship_by_date) ? $row->ship_by_date : '',
                        'shipper' => $service_name,
                        'status_name' => isset($row->order_status_name) ? $row->order_status_name : '',
                    ]
                );
            } 
        }

        return Excel::download($is_os 
            ? new TotalOrderShipped($to_export) 
            : new TotalOrder($to_export)
        , 'total_order.csv');
    }
}