<?php

namespace App\Http\Controllers;

use Auth;
use App\Help;
use App\Feedback;
use App\OrderSummary;
use App\MasterProduct;
use Illuminate\Http\Request;
use App\Exports\HelpReportExport;
use App\OrderPackage;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{

    private $types = [
        'cranium_error_code' => 'Cranium Error Code',
        'product_not_found' => 'Product not Found',
        'training_question' => 'Training Question',
        'general_help' => 'General Help'
    ];

    private $levels = [
        'urgent' => 'Urgent',
        'important' => 'Important',
        'not_urgent' => 'Not Urgent'
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
		//$feeds = DB::table('news_update_feed')->where('feed_auth_id', Auth::user()->id)->orderBy('feed_title')->get();	
		$feeds = DB::table('news_update_feed')->orderBy('created_at','DESC')->get();	
		$feedcount = DB::table('news_update_feed')->count();
		$approveproductcount = DB::table('master_product')->where('is_approve', 1)->count();		
		$notapproveproductcount = DB::table('master_product')->where('is_approve', 0)->count();		
		$queueproductcount = DB::table('master_product_queue')->count();		
        return view('landingpage', ['feeds' => $feeds, 'feedcount' => $feedcount, 'approveproductcount' => $approveproductcount, 'notapproveproductcount' => $notapproveproductcount, 'queueproductcount' => $queueproductcount,]);
    }

    public function submit_feedback(Request $request){
        $feedback = $request->input('feedback');
        $message = $request->input('message');

        $feedback_data = new Feedback;
        $feedback_data->user_id = Auth::user()->id;
        $feedback_data->feedback = $feedback;
        $feedback_data->message = $message;
        $feedback_data->save();

        if($feedback_data)
            return true;
        else
            return false;
    }

    public function MarkAsRead($id){
        auth()->user()
        ->notifications
        ->when($id, function ($query) use ($id) {
            return $query->where('id', $id);
        })
        ->markAsRead();

        return response()->noContent();

    }

    public function submit_help(Request $request){
        try {

            $type = $request->input('type');
            $urgent_level = $request->input('urgent_level');

            if (!isset($type) || $type === '') {
                return response()->json([
                    'error' => true,
                    'msg' => 'Type is mandatory'
                ]);
            }

            if (!isset($urgent_level) || $urgent_level === '') {
                return response()->json([
                    'error' => true,
                    'msg' => 'Urgent Level is mandatory'
                ]);
            }

            $textarea_help = $request->input('textarea_help');
            $location = '';

            if(isset($_FILES['file']['name'])){

                $location = "upload". DIRECTORY_SEPARATOR . $_FILES['file']['name'];
                
                $dir = dirname(public_path($location));
                if (!file_exists($dir)) {
                    mkdir($dir);
                }

                $imageFileType = strtolower(pathinfo($location, PATHINFO_EXTENSION));                
                
                /* Valid extensions */
                $valid_extensions = array("jpg", "jpeg", "bmp", "gif", "png");                

                if(in_array(strtolower($imageFileType), $valid_extensions)) {
                    move_uploaded_file($_FILES['file']['tmp_name'], public_path($location));
                } else {
                    return response()->json([
                        'error' => true,
                        'msg' => 'Invalid File Extension. Only JPG/JPEG/PNG are allowed.'
                    ]);
                }
            }

            $help = new Help;
            $help->type = $type;
            $help->urgent_level = $urgent_level;
            $help->desc = $textarea_help;
            $help->image_url = $location;
            $help->user_id = auth()->user()->id;
            $help->save();            

            if($help){
                return response()->json([
                    'error' => false,
                    'msg' => 'Feedback Submitted Successfully.'
                ]);
            }
            else{
                return response()->json([
                    'error' => true,
                    'msg' => 'Error Submittng Feedback'
                ]);
            }            
        }
        catch(\throwable $e){
            return response()->json([
                'error' => true,
                'msg' => $e->getMessage()
            ]);
        }        
    }

    public function get_help() {
        $helps = Help::where('status', 1)->get();
        $helps = $this->convert_help($helps);
        $helps_resolved = Help::where('status', 2)->get();
        $helps_resolved = $this->convert_help($helps_resolved);
        return view('cranium.reports.helps',compact('helps', 'helps_resolved'));
    }

    public function download_help_csv($type) {
        $helps = Help::where('status', $type)->get();
        $helps = $this->convert_help($helps);
        return Excel::download(new HelpReportExport($helps), $type == 1 ? 'help_reports.xlsx' : 'help_reports_resolved.xlsx');
    }

    public function get_help_details($help_id) {
        $help = Help::find($help_id);
        $help_ar = [$help];
        $help_ar = $this->convert_help($help_ar);
        $help = $help_ar[0];        
        return view('layouts.help_detail', compact('help'));
    }

    public function resolve_help($help_id) {
        Help::where('id', $help_id)->update(['status' => 2]);
        return response(['error' => false, 'msg' => 'Query Resolved']);
    }

    private function convert_help($help_in) {
        $helps = array();
        if (!isset($help_in) || count($help_in) < 0) return $helps;
        foreach($help_in as $help) {            
            $type = $this->types[$help->type];
            $level = $this->levels[$help->urgent_level];
            array_push($helps, [
                'name' => $help->user->name,
                'type' => $type,
                'urgent_level' => $level,
                'desc' => $help->desc,
                'date' => date('Y-m-d', strtotime($help->created_at)),
                'image_url' => $help->image_url,
                'id' => $help->id
            ]);
        }
        return $helps;
    }

    public function search_product_order($search_text) {
        $search_text = base64_decode($search_text);
        $or_text = $search_text;
        
        $search_text = str_replace(',', '', str_replace('.', '', str_replace('\'', '', $search_text)));

        $products = [];

        $cl_ids = DB::table('clients')
            ->orWhereRaw("replace(replace(replace(clients.company_name, '\'', ''), '.', ''), ',', '') like '%".$search_text."%'")
            ->pluck('id')->toArray();

        if (isset($cl_ids) && count($cl_ids) > 0) {
            $ids = implode(',', $cl_ids);
            $products = MasterProduct::leftjoin("categories",'categories.id',"=",'master_product.product_category')            
                ->where(function($query) use($search_text, $ids) {
                    $query->where('ETIN', 'like', '%'.$search_text.'%')
                        ->orWhere('gtin', 'like', '%'.$search_text.'%')
                        ->orWhere('product_listing_name', 'like', '%'.$search_text.'%')
                        ->orWhere('upc', 'like', '%'.$search_text.'%')
                        ->orWhereRaw('FIND_IN_SET(lobs , "'. $ids . '")');
                })->where('is_approve', 1)
                ->select(['master_product.id','master_product.ETIN','master_product.product_listing_name','master_product.product_type','master_product.upc','master_product.gtin','master_product.status','categories.name as product_category','master_product.item_form_description','master_product.is_approve','full_product_desc','about_this_item','ingredients','allergens','product_tags','current_supplier'])
                ->get();
        } else {
            $products = MasterProduct::leftjoin("categories",'categories.id',"=",'master_product.product_category')            
                ->where(function($query) use($search_text) {
                    $query->where('ETIN', 'like', '%'.$search_text.'%')
                        ->orWhere('gtin', 'like', '%'.$search_text.'%')
                        ->orWhere('product_listing_name', 'like', '%'.$search_text.'%')
                        ->orWhere('upc', 'like', '%'.$search_text.'%');
                })->where('is_approve', 1)
                ->select(['master_product.id','master_product.ETIN','master_product.product_listing_name','master_product.product_type','master_product.upc','master_product.gtin','master_product.status','categories.name as product_category','master_product.item_form_description','master_product.is_approve','full_product_desc','about_this_item','ingredients','allergens','product_tags','current_supplier'])
                ->get();
        }

        $orders = OrderSummary::leftjoin('clients', 'clients.id', '=', 'order_summary.client_id')
            ->leftjoin('order_summary_status', 'order_summary_status.id', '=', 'order_summary.order_status')
            ->where(function($query) use($search_text) {
                $query->where('etailer_order_number', 'like', '%'.$search_text.'%')
                    ->orWhere('channel_order_number', 'like', '%'.$search_text.'%')
                    ->orWhere('sa_order_number', 'like', '%'.$search_text.'%')
                    ->orWhereRaw("replace(replace(replace(clients.company_name, '\'', ''), '.', ''), ',', '') like '%".$search_text."%'")
                    ->orWhere('ship_to_name', 'like', '%'.$search_text.'%')
                    ->orWhere('ship_to_address1', 'like', '%'.$search_text.'%')
                    ->orWhere('ship_to_city', 'like', '%'.$search_text.'%')
                    ->orWhere('ship_to_state', 'like', '%'.$search_text.'%')
                    ->orWhere('ship_to_zip', 'like', '%'.$search_text.'%');
            })
            ->select('order_summary.*','order_summary_status.order_status_name', 'clients.company_name as client_name')
            ->get()->toArray();
        $orders = convertDateTimeTo12Hour($orders);
        $new_orders = array_replace([], $orders);

        $order_o_nums = [];
        if (isset($orders) && count($orders) > 0) {
            foreach($orders as $order) { array_push($order_o_nums, $order['etailer_order_number']); }
        }

        $sql = "select substr(op.order_id,1,5) as order_id, tracking_number  
                from order_packages op where op.tracking_number like '%".  $search_text ."%'
                group by tracking_number, op.order_id ";
        $order_ids = DB::select($sql);

        $o_id_t_num = [];
        if (isset($order_ids) && count($order_ids) > 0) {
            $o_ids = [];
            foreach($order_ids as $order_id) { 
                if (!in_array($order_id->order_id, $order_o_nums)) 
                    array_push($o_ids, $order_id->order_id); 
                    
                if (!array_key_exists($order_id->order_id, $o_id_t_num)) {                                                      
                    $o_id_t_num[$order_id->order_id] = $order_id->tracking_number;
                } else {                    
                    $o_id_t_num[$order_id->order_id] = $o_id_t_num[$order_id->order_id] . ',' . $order_id->tracking_number;
                }                
            }
            $tid_orders = OrderSummary::leftjoin('clients', 'clients.id', '=', 'order_summary.client_id')
                ->leftjoin('order_summary_status', 'order_summary_status.id', '=', 'order_summary.order_status')
                ->whereIn('etailer_order_number', $o_ids)
                ->select('order_summary.*','order_summary_status.order_status_name', 'clients.company_name as client_name')
                ->get()->toArray();

            if (isset($tid_orders) && count($tid_orders) > 0) {
                $tid_orders = convertDateTimeTo12Hour($tid_orders);
                
                foreach($tid_orders as $tid_order) {
                    array_push($new_orders, $tid_order);
                }
            }
        }
        
        $n_o = [];
        foreach($new_orders as $no) {
            if ($no['order_status'] == 17) {
                $op = OrderPackage::where('order_id', 'like', '%'.$no['etailer_order_number'].'%')
                    ->groupBy(['order_id', 'tracking_number'])
                    ->select(['tracking_number'])->get()->toArray();
                $t_nums = '';
                if (isset($op) && count($op)) {
                    foreach($op as $o) {
                        if ($t_nums == '') {
                            $t_nums = $o['tracking_number'];
                        } else {
                            $t_nums = $t_nums . ',' . $o['tracking_number'];
                        }
                        $no['track_number'] = $t_nums;
                    }
                }
            }
            array_push($n_o, $no);
        }
        return view('layouts.products-order', 
            [
                'search_text' => $or_text, 
                'products' => $products,
                'orders' => $n_o,
                't_num' => $o_id_t_num
            ]);
    }
}
