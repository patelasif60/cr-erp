<?php

namespace App\Http\Controllers;

use File;
use View;
use App\User;
use Response;
use App\Client;
use App\Contact;
use App\CsvData;
use App\TimeZone;
use App\CsvHeader;
use Carbon\Carbon;
use App\OrderTypes;
use App\AisleMaster;
use App\ClientEvent;
use App\MasterShelf;
use App\BackStockPallet;
use App\BackStockPalletItem;
use App\MasterProduct;
use App\PurchasingSummary;
use App\ReceivingDetail;
use App\PurchasingDetail;
use App\PutAway;
use App\ClientDocument;
use App\CustomerService;
use App\ClientAccountNote;
use App\ClientBillingNote;
use App\ClientBillingEvent;
use App\ClientDocumentsLink;
use App\ShippingServiceType;
use Illuminate\Http\Request;
use App\UserNotificationSetting;
use Yajra\DataTables\DataTables;
use App\ClientBillingAccountNote;
use Illuminate\Support\Facades\DB;
use App\ClientChannelConfiguration;
use App\MasterProductKitComponents;
use App\Services\BillingNoteService;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\ClientsRequest;
use App\ClientWarehouseAndFulfillment;
use App\Http\Requests\CsvImportRequest;
use App\OrderDetail;
use App\OrderSummary;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\HeadingRowImport;
use App\{ProductListingFilter,SmartFilter,WareHouse};

class ClientController extends Controller
{
    // public function __construct()
	// {
    //     $this->middleware('admin_and_manager');
	// }

    public function __construct(BillingNoteService $service)
    {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(auth()->user()->client != ''){
			return redirect(route('clients.edit',auth()->user()->client));
		}
        
        if(moduleacess('Clients') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        $result = Client::select(['clients.id','clients.company_name','clients.business_relationship','a_manager.name as account_manager','clients.is_enable','s_manager.name as sales_manager'])
        ->leftJoin('users as s_manager',function($join){
            $join->on('s_manager.id','=','clients.sales_manager');
        })->leftJoin('users as a_manager',function($join){
            $join->on('a_manager.id','=','clients.account_manager');
        })->get();
        return view('clients.index',compact('result'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(ReadWriteAccess('AddNewClient') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        $managers = User::pluck('name','id')->toArray();
        $time_zones = TimeZone::pluck('name','id')->toArray();
        return view('clients.create',compact('managers','time_zones'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientsRequest $request)
    {
        if(ReadWriteAccess('AddNewClient') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        $Client = new Client;
        $Client->company_name = $request->company_name;
        $Client->status = $request->status;
        $Client->business_relationship = $request->business_relationship;
        $Client->account_manager = $request->account_manager;
        $Client->sales_manager = $request->sales_manager;
        $Client->time_zone_id = $request->time_zone_id;
        $Client->address = $request->address;
        $Client->address2 = $request->address2;
        $Client->zip = $request->zip;
        $Client->city = $request->city;
        $Client->state = $request->state;
        $Client->is_enable = $request->active_status;
        $Client->save();
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('clients.edit',$Client->id)
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
        
        if(ReadWriteAccess('EditClient') == false  && auth()->user()->client == ''){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        $managers = User::pluck('name','id')->toArray();
        $time_zones = TimeZone::pluck('name','id')->toArray();

        $row = Client::find($id);
        $customerServiceRow = CustomerService::where('client_id',$id)->first();

		// $brand = DB::table('brand')->pluck('brand')->toArray();

		//Getting all Manufacturer Name list
		// $manufacturer = DB::table('manufacturer')->pluck('manufacturer_name')->toArray();

		//Getting all Suppliers list
		// $suppliers = DB::table('suppliers')->where('status', 'Active')->pluck('name')->toArray();

		//Getting all product list
		// $products = DB::table('product_type')->pluck('product_type')->toArray();

		//Getting all Unit Description list
		// $unitdesc = DB::table('unit_desc')->pluck('unit_description')->toArray();

		// $item_form_desc = DB::table('item_from_description')->pluck('item_desc')->toArray();
        $item_form_desc = [];
        $unitdesc = [];
        $getet = [];
        $products = [];
        $suppliers = [];
        $manufacturer = [];
        $brand = [];
            $warehouse = [];
        // $getet = DB::table('master_product')->where('is_approve',1)->whereNotNull('upc')->pluck('ETIN','id')->toArray();

		$upcs = [];
		// $getupcs = DB::table('master_product')->where('is_approve',1)->whereNotNull('upc')->select('upc','ETIN')->get();
		// foreach ($getupcs as $getupc){
		// 	$upcs[] = $getupc->upc;
		// 	$getet[] = $getupc->ETIN;
		// }

        // $getwarehouses = DB::table('warehouses')->get();
        // foreach ($getwarehouses as $warehouselist){
        //     $warehouse[] = $warehouselist->warehouses;
        // }

		$listing_name = [];
		// $getlisting_name = DB::table('master_product')->where('is_approve',1)->whereNotNull('product_listing_name')->select('product_listing_name')->distinct()->distinct()->get();
		// foreach ($getlisting_name as $listing){
		// 	$listing_name[] = $listing->product_listing_name;
		// }
        $priceGroup=DB::table('price_group')->whereRaw('FIND_IN_SET('.$id.',lobs)')->get();

        $result = [];

        // if (isset($row->business_relationship) && strtolower($row->business_relationship) === 'fulfillment') {
        //     $ps = DB::table('purchasing_summaries')
        //     ->join('warehouses', 'warehouses.id', '=', 'purchasing_summaries.warehouse_id')
        //     ->join('purchasing_details', 'purchasing_details.po', '=', 'purchasing_summaries.order')
        //     ->where('purchasing_summaries.client_id', $row->id)
        //     ->select(['purchasing_summaries.*', 'warehouses.warehouses',DB::raw('GROUP_CONCAT(DISTINCT(purchasing_details.bol_number) SEPARATOR ", ") as bol_numbers')])
        //     ->groupBy('purchasing_summaries.id')
        //     ->get();
        //     if ($ps && count($ps) > 0) {
        //         foreach($ps as $p) {
        //             array_push($result, [
        //                 'id' => $p->id,
        //                 'warehouse' => $p->warehouses,
        //                 'order' => $p->order,
        //                 'order_date' => $p->purchasing_asn_date,
        //                 'delivery_date' => $p->delivery_date,
        //                 'po_status' => $p->po_status,
        //                 'report_path' => $p->report_path,
        //                 'invoice' => $p->invoice,
        //                 'bol_numbers' => $p->bol_numbers
        //             ]);
        //         }
        //     }
        // }

        $selected_smart_filter = [];
		$visible_filters = [];
		$hidden_cols = '';
		$visible_columns = '';
		$smart_filter = [];
		$hidden_cols_arr = [];
		$not_default_columns = [];
		$main_filter = [];
		$max_chars_columns = '';
		$product_listing_filter = ProductListingFilter::where('type','product')->orderBy('sorting_order')->get();
		$smart_filters = SmartFilter::where('created_by',Auth::user()->id)->where('type','product')->get();
        $warehouse = WareHouse::orderBy('warehouses','ASC')->get();
        $client = Client::get()->pluck('company_name','id')->toArray();

        $ots = OrderTypes::get();
        $user_id = auth()->user()->id;
        $notification = UserNotificationSetting::where('user_id',$user_id)->where('order_by_client',$id)->first();
        $shipping_service_types = ShippingServiceType::all();
        return view('clients.edit',compact('row', 'customerServiceRow','brand','manufacturer','suppliers','products','unitdesc','item_form_desc','getet','upcs','listing_name','warehouse','managers','time_zones','priceGroup','result','ots','notification','shipping_service_types','client','product_listing_filter','smart_filters','selected_smart_filter','id','hidden_cols','visible_columns','visible_filters','smart_filter','main_filter','hidden_cols_arr'));
    }

    public function update(ClientsRequest $request, $id)
    {
        if(ReadWriteAccess('EditClient') == false   && auth()->user()->client == ''){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        $Client = Client::find($id);

        if ($Client->business_relationship != $request->business_relationship && auth()->user()->role != 1) {
            return response()->json([
                'error' => true,
                'msg' => 'To change business relationship. Contact Admin',
                'url' => route('clients.edit',$id)
            ]);
        }

        $Client->company_name = $request->company_name;
        $Client->status = $request->status;
        $Client->business_relationship = $request->business_relationship;
        $Client->account_manager = $request->account_manager;
        $Client->sales_manager = $request->sales_manager;
        $Client->time_zone_id = $request->time_zone_id;
        $Client->address = $request->address;
        $Client->address2 = $request->address2;
        $Client->zip = $request->zip;
        $Client->city = $request->city;
        $Client->state = $request->state;
        $Client->is_enable = $request->active_status;
        $Client->save();

        $this->changeOrderStatus($id, $request->active_status, null);

        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('clients.index')
        ]);
    }

    private function changeOrderStatus($client_id, $active_status, $channel_id) {
        // Status 
        // 1 = Active
        // 2 = On Hold
        if ($active_status == 2) {
            $os = null;
            if ($channel_id == null) {
                $os = OrderSummary::where('client_id', $client_id)
                   ->whereNotIn('order_status', [17, 20, 22, 24, 25])->get();
            } else {
                $os = OrderSummary::where('client_id', $client_id)
                    ->where('channel_id', $channel_id)
                    ->whereNotIn('order_status', [17, 20, 22, 24, 25])->get();
            }
            if (isset($os) && count($os) > 0) {
                foreach($os as $o) {
                    $ods = OrderDetail::where('order_number', $o->etailer_order_number)
                        ->whereNotIn('status', [6, 13, 14, 15, 2, 3, 4, 10, 11, 12])->get();
                    if (isset($ods) && count($ods) > 0) {
                        foreach($ods as $od) {
                            $od->status = 18;
                            $od->save();
                        }
                    }
                    $o->old_status = $o->order_status;
                    $o->order_status = 10;
                    $o->save();
                    
                    DB::table('order_history')->insert([
						'mp_order_number' => $o->channel_order_number,
						'etailer_order_number' => $o->etailer_order_number,
						'date' => date("Y-m-d H:i:s", strtotime('now')),
						'action' => 'Order On Hold',
						'details' => 'Due to Client/Channel On Hold, Order status is on Hold now',
						'user' => 'Auto Process',
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s")
					]);
                }
            }
        } else if ($active_status == 1) {
            $os = OrderSummary::where('client_id', $client_id)->where('order_status', 10)->get();
            if (isset($os) && count($os) > 0) {
                foreach($os as $o) {
                    $ods = OrderDetail::where('order_number', $o->etailer_order_number)
                        ->where('status', 18)->get();
                    if (isset($ods) && count($ods) > 0) {
                        foreach($ods as $od) {
                            $od->status = 1;
                            $od->save();
                        }
                    }
                    $o->order_status = $o->old_status;
                    $o->old_status = NULL;
                    $o->save();
                    
                    DB::table('order_history')->insert([
						'mp_order_number' => $o->channel_order_number,
						'etailer_order_number' => $o->etailer_order_number,
						'date' => date("Y-m-d H:i:s", strtotime('now')),
						'action' => 'Order Hold Released',
						'details' => 'Due to Client/Channel Hold Released, Order status is Released',
						'user' => 'Auto Process',
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s")
					]);
                }
            }
        }
    }

    function updateClientManagementDetails(Request $request,$id){
        if(ReadWriteAccess('EditClient') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        $Client = Client::find($id);
        $Client->inventory_manager = $request->inventory_management;
        $Client->inventory_management_notes = $request->inventory_management_notes;
        $Client->purchasing_management = $request->purchasing_management;
        $Client->purchasing_management_notes = $request->purchasing_management_notes;
        $Client->order_management = $request->order_management_notes;
        $Client->custom_packaging = $request->custom_packaging;
        $Client->custom_packaging_notes = $request->custom_packaging_notes;
        $Client->store_owner = $request->channel_owner;
        $Client->channel_owner_notes = $request->channel_owner_notes;
        $Client->price_manager = $request->price_management;
        $Client->price_management_notes = $request->price_management_notes;
        $Client->customer_service = $request->customer_service;
        $Client->customer_service_notes = $request->customer_service_notes;
        $Client->product_consignment = $request->product_consignment;

        if($request->customer_service == "e-tailer"){
            CustomerService::updateOrCreate([
                'client_id' => $id,
            ],[
                'is_phone_etailer' => (isset($request->is_phone_etailer) ? 1 :0),
                'phone_etailer_notes' => $request->phone_etailer_notes,
                'is_email_etailer' => (isset($request->is_email_etailer) ? 1 :0),
                'email_etailer_notes' => $request->email_etailer_notes,
                'is_live_chat_etailer' => (isset($request->is_live_chat_etailer) ? 1 :0),
                'live_chat_etailer_notes' => $request->live_chat_etailer_notes,
                'is_miscellaneous_etailer' => (isset($request->is_miscellaneous_etailer) ? 1 :0),
                'miscellaneous_etailer_notes' => $request->miscellaneous_etailer_notes,
            ]);
        }

        $Client->save();

        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('clients.edit',$id),
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
        if(ReadWriteAccess('DeleteClient') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        $Client = Client::find($id);
        $Client->delete();
        return redirect()->route('clients.index')->with('success','Deleted successfully');
    }

    public function channelList($id){
        $channels = ClientChannelConfiguration::where('client_id',$id)->get();
        $cl = Client::find($id);
        $role = Auth::user()->role;
        return Datatables::of($channels)
        ->addIndexColumn()
        ->addColumn('action', function($channels) use($role, $cl){
                $btn = '';
                $url = route('clients.editChannel',$channels->id);
                $hold_url = route('clients.update_status',[$channels->id, $cl->id]);
                $btn .= '<a href="javascript:void(0);" onclick="getModal(\''.$url.'\')"  class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                if($role == 1){
                    $btn .= '<a  onclick="deleteChanel(\''.route('clients.deleteChannel',$channels->id).'\')"   class="delete btn btn-danger btn-sm text-white mr-2">Delete</a>';
                }
                $caption = $channels->is_active == 1 ? 'Put On Hold' : 'Activate';
                if ($cl->is_enable == 1) {
                    $btn .= '<a href="'.$hold_url.'" class="delete btn btn-warning btn-sm text-white">'.$caption.'</a>';
                } else {
                    $btn .= '<a href="javascript:void(0)" class="delete btn btn-outline-warning btn-sm" style="pointer-events: none;">'.$caption.'</a>';
                }
                return $btn;
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function WareHouseOrders(Request $request,$id){
        $client_info = Client::find($id);
        $res = PurchasingSummary::leftjoin('warehouses', 'warehouses.id', '=', 'purchasing_summaries.warehouse_id')
        ->leftjoin('purchasing_details', 'purchasing_details.po', '=', 'purchasing_summaries.order')
        ->where('purchasing_summaries.client_id', $id)
        ->select('purchasing_summaries.*','warehouses.warehouses as warehouse','purchasing_summaries.order','purchasing_asn_date','delivery_date','po_status','report_path','invoice',DB::raw('GROUP_CONCAT(DISTINCT(purchasing_details.bol_number) SEPARATOR ", ") as bol_numbers'), DB::raw('GROUP_CONCAT(DISTINCT(purchasing_details.status) SEPARATOR ", ") as status'))
        ->groupBy('purchasing_summaries.id')
        ->orderBy('id','DESC')
        ->get();
        $role = Auth::user()->role;
        return Datatables::of($res)
        
        ->addColumn('action', function($res) use($id,$role,$client_info){
                $btn = '';
                if($res->po_status && ($res->po_status == 'Pending' || $res->po_status == 'Submitted')){
                    $btn .= '<a href="'.url('/purchase_order/edit/' . $id . '/' . $res->id . '/client').'" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Edit">
                        <i class="nav-icon i-Pen-2 "></i>
                    </a>';
                }

                
                                                                    
                 if($res->po_status != 'Pending'){
                    if($res->report_path){
                        $btn .= ' <a href="'.url('/' . $res->report_path).'" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Download Report">
                            <i class="nav-icon i-Down"></i>
                        </a>';
                    }
                    if($res->po_status && $res->po_status != 'Pending'){
                        $btn .= ' <a href="'.url('/purchase_order/editasnbol/' . $id . '/' . $res->id . '/client').'" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Submit ASN/BOL">
                            <i class="nav-icon">Submit ASN/BOL</i>
                        </a>';
                    }


                    if(isset($client_info->exp_lot) && $client_info->exp_lot != 'NO'){
                        $btn .= ' <a href="javascript:void(0)" onClick="getModal(\''.url('/purchase_order/' . $res->order . '/get_lot_and_exp').'\')" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Submit ASN/BOL">
                            <i class="nav-icon">Submit Lot & Exp. #\'s</i>
                        </a>';
                    }
                    
                 }                                               
                
            
                $status = explode(',',$res->status);
                $total_status = count($status);
                $status_increment = 0;
                if($status){
                    foreach($status as $row_status){
                        if($row_status == 'Received'){
                            $status_increment++;
                        }
                    }
                }

                if($status_increment != $total_status){
                    if($res->bol_numbers == ''){
                        $btn .= ' <a href="javascript:void(0)" onClick="DeleteWarehouseOrder('.$res->id.',0)" class="btn btn-danger"  data-toggle="tooltip" data-placement="top" title="Delete">
                            Delete
                        </a>';
                    }else{
                        if($role == 1){
                            $btn .= ' <a href="javascript:void(0)" onClick="DeleteWarehouseOrder('.$res->id.',1)" class="btn btn-danger"  data-toggle="tooltip" data-placement="top" title="Delete">
                            Delete
                            </a>';
                        }
                        
                    }
                }

                return $btn;
        })
        ->editColumn('status', function($res){
            if($res->status == ''){
                return $res->po_status;
                
            }else{
                return $res->status;
            }
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function DeleteWarehouseOrder($id,$all){
        $PS = PurchasingSummary::find($id);
        $PD = PurchasingDetail::select('bol_number')->where('po',$PS->order)->groupBy('bol_number')->get();
        if($PD){
            foreach($PD as $ROW_PD){
                if($all == 1){
                    $PTAWAY = PutAway::where('bol_number',$ROW_PD->bol_number)->get();
                    if($PTAWAY){
                        foreach($PTAWAY as $ROWPTAWAY){
                            $transfered = $ROWPTAWAY->transfered;
                            if($transfered == 1){
                                $ms = MasterShelf::where('address', $ROWPTAWAY->location)->where('ETIN', $ROWPTAWAY->etin)->first();
                                if($ms){
                                    InventoryAdjustmentLog([
                                        'ETIN' => $ms->ETIN,
                                        'location' => $ms->address,
                                        'starting_qty' => $ms->cur_qty,
                                        'ending_qty' => $ms->cur_qty - $ROWPTAWAY->quantity,
                                        'total_change' => '-'.$ROWPTAWAY->quantity,
                                        'reference' => 'DeleteWarehouseOrder',
                                        'reference_value' => 'id: '.$id,
                                        'reference_description' => 'Deducting Qty while DeleteWarehouseOrder'
                                    ]);

                                    $ms->cur_qty = $ms->cur_qty - $ROWPTAWAY->quantity;
                                    $ms->save();
                                }
                                
                            }

                            $ROWPTAWAY->delete();
                        }
                    }

                    $bsp = BackStockPallet::where('bol_number', $ROW_PD->bol_number)->first();
                    if($bsp){
                        $bsp_items = BackStockPalletItem::where('backstock_pallet_id', $bsp->id)->get();
                        if($bsp_items){
                            foreach($bsp_items as $row_bsp_items){
                                $bs_ms = MasterShelf::where('address', $row_bsp_items->location)->where('ETIN', $row_bsp_items->ETIN)->first();
                                if($bs_ms){
                                    InventoryAdjustmentLog([
                                        'ETIN' => $bs_ms->ETIN,
                                        'location' => $bs_ms->address,
                                        'starting_qty' => $bs_ms->cur_qty,
                                        'ending_qty' => $bs_ms->cur_qty - $row_bsp_items->quantity,
                                        'total_change' => '-'.$row_bsp_items->quantity,
                                        'reference' => 'DeleteWarehouseOrder',
                                        'reference_value' => 'id: '.$id,
                                        'reference_description' => 'Deducting Qty while DeleteWarehouseOrder'
                                    ]);
                                    $bs_ms->cur_qty = $bs_ms->cur_qty - $row_bsp_items->quantity;
                                    $bs_ms->save();
                                }

                                $row_bsp_items->delete();
                            }
                        }

                        $bsp->delete();
                    }


                    ReceivingDetail::where('bol_number',$ROW_PD->bol_number)->delete();


                }        
                // $PD->delete();
            }
        }
        
        PurchasingDetail::where('po',$PS->order)->delete();
        $PS->delete();
        return response(['error' > false, 'msg' => 'success']);
    }

    public function createChannel($id){
        return view('channels.create',compact('id'));
    }

    public function storeChannel(Request $request){                

        $result = ClientChannelConfiguration::create([
            'client_id' => $request->client_id,
            'channel' => $request->channel,
            'store_url' => $request->store_url,
            'admin_url' => $request->admin_url,
            'username' => $request->username,
            'password' => $request->password,
            'channel_type' => $request->channel_type,
        ]);
        if ($result) {
            return response()->json([
                'error' => 0,
                'msg' => 'Success'
            ]);
        } else{
            return response()->json([
                'error' => 1,
                'msg' => 'Something Went Wrong'
            ]);
        }
    }

    public function editChannel($id){
        $row = ClientChannelConfiguration::find($id);
        return view('channels.edit',compact('id','row'));
    }

    public function updateChannel(Request $request,$id){
        
        $channel = ClientChannelConfiguration::find($id);

        $channel->client_id = $request->client_id;
        $channel->channel = $request->channel;
        $channel->store_url = $request->store_url;
        $channel->admin_url = $request->admin_url;
        $channel->username = $request->username;
        $channel->password = $request->password;
        $channel->channel_type = $request->channel_type;
        $channel->save();
        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
    }

    public function deleteChannel($id){
       $channel = ClientChannelConfiguration::find($id);
       if (!empty($channel)) {
            $channel->delete();
            return response()->json([
                'error' => 0,
                'msg' => 'Success'
            ]);
       }
       else{
            return response()->json([
                'error' => 1,
                'msg' => 'Something went wrong!'
            ]);
       }
    }

    public function eventList($id){
        $events = ClientEvent::where('client_id',$id)->leftJoin('users',function($join){
            $join->on('users.id','=','client_events.owner');
        })->select('client_events.*','users.name as owner')->get();
        return Datatables::of($events)
					->addIndexColumn()
                    ->addColumn('action', function($event){
							$btn = '';
                            $url = route('clients.editEvent',$event->id);
                            $btn .= '<a href="javascript:void(0);" onclick="editEventModal(\''.$url.'\')"  class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                            $btn .= '<a onclick="deleteEvent(\''.route('clients.deleteEvent',$event->id).'\')"   class="delete btn btn-danger btn-sm text-white">Delete</a>';
                            return $btn;
                    })
                    ->editColumn('date',function($accountNote){
                        return date("m-d-Y h:i",strtotime($accountNote->day_and_time));
                    })
                    ->rawColumns(['action'])
                    ->make(true);
    }

    public function createEvent($id){
        $users = User::select('id','name')->get();
        return view('events.create',compact('id','users'));
    }

    public function storeEvent(Request $request){
      $result = ClientEvent::create([
        'client_id' => $request->client_id,
        'event' => $request->event,
        'frequency' => $request->frequency,
        'day_and_time' => $request->day_and_time,
        'details' => $request->details,
        'owner' => $request->owner,
      ]);
      if ($result) {
        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
      }else{
           return response()->json([
            'error' => 0,
            'msg' => 'Something Went Wrong'
        ]);
      }
    }

    public function editEvent($id){
        $row = ClientEvent::find($id);
        $users = User::select('id','name')->get();
        return view('events.edit',compact('id','row','users'));
    }


    public function updateEvent(Request $request,$id){
        $event = ClientEvent::find($id);
        $event->client_id = $request->client_id;
        $event->event = $request->event;
        $event->frequency = $request->frequency;
        $event->day_and_time = $request->day_and_time;
        $event->details = $request->details;
        $event->owner = $request->owner;
        $event->update();
        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
    }

    public function deleteEvent($id){
       $event = ClientEvent::find($id);
       if (!empty($event)) {
            $event->delete();
            return response()->json([
                'error' => 0,
                'msg' => 'Success'
            ]);
       }
       else{
            return response()->json([
                'error' => 1,
                'msg' => 'Something went wrong!'
            ]);
       }
    }

    public function noteList($id){
        $accountNotes = ClientAccountNote::where('client_id',$id)->leftJoin('users',function($join){
            $join->on('users.id','=','client_account_notes.added_by');
        })->select('client_account_notes.*','users.name as added_by')->get();
        return Datatables::of($accountNotes)
            ->addIndexColumn()
            ->addColumn('action', function($accountNote){
                    $btn = '';
                    $url = route('clients.editNote',$accountNote->id);
                    $btn .= '<a href="javascript:void(0);" onclick="editNoteModal(\''.$url.'\')" class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                    $btn .= '<a onclick="deleteNote(\''.route('clients.deleteNote',$accountNote->id).'\')" class="delete btn btn-danger btn-sm text-white">Delete</a>';
                    return $btn;
            })
            ->editColumn('date',function($accountNote){
                return date("m-d-Y h:i",strtotime($accountNote->date_and_time));
            })
            ->rawColumns(['date','action'])
            ->make(true);
    }

    public function createNote($id){
        return view('notes.create',compact('id'));
    }
    public function storeNote(Request $request){
      $result = ClientAccountNote::create([
        'client_id' => $request->client_id,
        'event' => $request->event,
        'details' => $request->details,
        'date_and_time' => date('m-d-Y h:i'),
        'added_by' => Auth::user()->id
      ]);
      if ($result) {
        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
      }else{
           return response()->json([
            'error' => 0,
            'msg' => 'Something Went Wrong'
        ]);
      }
    }

    public function editNote($id){
        $row = ClientAccountNote::find($id);
        return view('notes.edit',compact('id','row'));
    }

    public function updateNote(Request $request,$id){

        $Eventnote = ClientAccountNote::find($id);
        $Eventnote->client_id = $request->client_id;
        $Eventnote->event = $request->event;
        $Eventnote->details = $request->details;
        $Eventnote->date_and_time = $request->date_and_time;
        $Eventnote->update();
        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
    }

    public function deleteNote($id){
       $event = ClientAccountNote::find($id);
       if (!empty($event)) {
            $event->delete();
            return response()->json([
                'error' => 0,
                'msg' => 'Success'
            ]);
       }
       else{
            return response()->json([
                'error' => 1,
                'msg' => 'Something went wrong!'
            ]);
       }
    }

    public function warehouseAndFulfillmentList($id){
        $accountNotes = ClientWarehouseAndFulfillment::where('client_id',$id)->leftJoin('users',function($join){
            $join->on('users.id','=','client_warehouse_and_fulfillments.owner');
        })->select('client_warehouse_and_fulfillments.*','users.name as owner')->get();
        return Datatables::of($accountNotes)
            ->addIndexColumn()
            ->addColumn('action', function($accountNote){
                    $btn = '';
                    $url = route('clients.editWarehouseAndFulfillment',$accountNote->id);
                    $btn .= '<a href="javascript:void(0);" onclick="getModal(\''.$url.'\')"  class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                    $btn .= '<a onclick="deleteWarehouseAndFulfillment(\''.route('clients.deleteWarehouseAndFulfillment',$accountNote->id).'\')" href="javascript:void(0);" onClick="return confirm(\'Are You Sure You Want To Delete This? \')"  class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
            })
            ->editColumn('date',function($accountNote){
                    return date("m-d-Y h:i",strtotime($accountNote->day_and_time));
            })
            ->rawColumns(['date','action'])
            ->make(true);
    }

    public function createWarehouseAndFulfillment($id){
        $users = User::pluck('name','id')->toArray();
        return view('warehouse_and_fulfillments.create',compact('id','users'));
    }

    public function storeWarehouseAndFulfillment(Request $request){
      $result = ClientWarehouseAndFulfillment::create([
        'client_id' => $request->client_id,
        'event' => $request->event,
        'frequency' => $request->frequency,
        'day_and_time' => $request->day_and_time,
        'details' => $request->details,
        'owner' => $request->owner,
      ]);
      if ($result) {
        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
      }else{
           return response()->json([
            'error' => 0,
            'msg' => 'Something Went Wrong'
        ]);
      }
    }

    public function editWarehouseAndFulfillment($id){
        $row = ClientWarehouseAndFulfillment::find($id);
        $users = User::pluck('name','id')->toArray();
        return view('warehouse_and_fulfillments.edit',compact('id','row','users'));
    }


    public function updateWarehouseAndFulfillment(Request $request,$id){
        $warehouse = ClientWarehouseAndFulfillment::find($id);
        $warehouse->client_id = $request->client_id;
        $warehouse->event = $request->event;
        $warehouse->frequency = $request->frequency;
        $warehouse->day_and_time = $request->day_and_time;
        $warehouse->details = $request->details;
        $warehouse->owner = $request->owner;
        $result = $warehouse->update();
        if ($result) {
            return response()->json(['error' => 0 , 'msg' => 'Success']);
        }
        else{
            return response()->json(['error' => 1 , 'msg' => 'Something went wrong.']);
        }

    }

    public function deleteWarehouseAndFulfillment($id){
       $warehouse = ClientWarehouseAndFulfillment::find($id);
       if (!empty($warehouse)) {
            $warehouse->delete();
            return response()->json([
                'error' => 0,
                'msg' => 'Success'
            ]);
       }
       else{
            return response()->json([
                'error' => 1,
                'msg' => 'Something went wrong!'
            ]);
       }
    }

    public function contactList($id){
        $contacts = Contact::where('client_id',$id)->get();
        return Datatables::of($contacts)
            ->addIndexColumn()
            ->addColumn('action', function($contact){
                    $btn = '';
                    $url = route('clients.editContact',$contact->id);
                    $btn .= '<a href="javascript:void(0);" onclick="getModal(\''.$url.'\')"  class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                    $btn .= '<a  href="javascript:void(0);" onclick="deleteContact(\''.route('clients.deleteContact',$contact->id).'\')"  class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
            })
            ->editColumn('cranium',function($contact){
                if ($contact->is_contract) {
                    return "Yes";
                }
                else{
                    return "No";
                }
            })
            ->editColumn('status',function($contact){
                $checked = '';
                if ($contact->is_primary == 1) {
                    $checked = "checked";
                }
                return '<input type="checkbox" onclick="setPrimaryContact(this,\''.$contact->id.'\')" name="is_primary" value="1" '.$checked.'>';
            })
            ->rawColumns(['cranium','status','action'])
            ->make(true);
    }

    public function setPrimaryContact(Request $request){
        $contact = Contact::find($request->id);
        if ($contact->is_primary) {
            $contact->is_primary = 0;
            $contact->update();
            return response()->json(['msg' => 'Success', 'error' => 0]);
        }
        else{
            $contact->is_primary = 1;
            $contact->update();

            $unsetAll = Contact::where('id','!=',$request->id)->where('client_id',$contact->client_id)->update(['is_primary' => 0]);
            return response()->json(['msg' => 'Success', 'error' => 0]);
        }
    }

    public function createContact($id){
        return view('contacts.create',compact('id'));
    }
    public function storeContact(Request $request){
      $result = Contact::create([
        'client_id' => $request->client_id,
        'name' => $request->name,
        'title' => $request->title,
        'email' => $request->email,
        'office_phone' => $request->office_phone,
        'cell_phone' => $request->cell_phone,
        'is_contract' => $request->is_contract,
        'contact_note' => $request->contact_note
      ]);
      if ($result) {
        return response()->json(['msg' => 'Success', 'error' => 0]);
      }
    }

    public function editContact($id){
        $row = Contact::find($id);
        return view('contacts.edit',compact('id','row'));
    }

    public function updateContact(Request $request,$id){
        $contact = Contact::find($id);
        $contact->client_id = $request->client_id;
        $contact->name = $request->name;
        $contact->title = $request->title;
        $contact->email = $request->email;
        $contact->office_phone = $request->office_phone;
        $contact->cell_phone = $request->cell_phone;
        $contact->is_contract = $request->is_contract;
        $contact->contact_note = $request->contact_note;
        $contact->update();
        return response()->json(['msg' => 'Success', 'error' => 0]);
    }

    public function deleteContact($id){
       $contact = Contact::find($id);
       if (!empty($contact)) {
            $contact->delete();
            return response()->json(['msg' => 'Success', 'error' => 0]);
       }
       else{
        return response()->json(['msg' => 'Something went wrong!', 'error' => 1]);
       }
    }

    public function documentList($id){
        $documents = ClientDocument::where('client_id',$id)->select('*',DB::raw('DATE_FORMAT(created_at,"%m-%d-%Y %H:%i:%s") as created_date'))->get();

        return Datatables::of($documents)
            ->addIndexColumn()
            ->editColumn('date',function($document){
                return date('m-d-y',strtotime($document->date));
            })
            ->addColumn('action', function($document){
                    $btn = '';
                    $btn .= '<a href="'.route('clients.document.download',$document->id).'" class="edit btn btn-primary btn-sm mr-2">Download</a>';
                    $btn .= '<a onclick="deleteDocument(\''.route('clients.deleteDocument',$document->id).'\')" class="delete btn btn-danger btn-sm text-white">Delete</a>';
                    return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function createDocument($id){
        return view('documents.create',compact('id'));
    }
    public function storeDocument(Request $request){
        if($request->hasFile('document'))
        {
            $file = $request->file('document');
            $docPath = public_path('/client_documents/');
            if (!file_exists($docPath)) {
                mkdir($docPath, 0775, true);
            }
            $document = md5(time().'_'.$file->getClientOriginalName()).'.'.$file->getClientOriginalExtension();
            $file->move($docPath,$document);
        }
      $result = ClientDocument::create([
        'client_id' => $request->client_id,
        'type' => $request->type,
        'name' => $request->name,
        'description' => $request->description,
        'date' => $request->date,
        'document' => $document ?? ''
      ]);
      if ($result) {
        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
      }else{
           return response()->json([
            'error' => 0,
            'msg' => 'Something Went Wrong'
        ]);
      }
    }

    public function downloadDocument($id)
    {
        $getFile = ClientDocument::find($id);
        $file = public_path().'/client_documents/'.$getFile->document;
        if(File::exists($file)){
            return Response::download($file);
            session()->flash('Success');
        }
        else{
            return back()->with(['error' => 'No file is there!']);
        }
    }

    public function deleteDocument($id){
       $document = ClientDocument::find($id);

       if (!empty($document)) {
            $document->delete();
            return response()->json([
                'error' => 0,
                'msg' => 'Success'
            ]);
       }
       else{
            return response()->json([
                'error' => 1,
                'msg' => 'Something went wrong!'
            ]);
       }
    }


    public function linkList($id){
        $links = ClientDocumentsLink::where('client_id',$id)->select('*',DB::raw('DATE_FORMAT(created_at,"%m-%d-%Y %H:%i:%s") as created_date'))->get();
        return Datatables::of($links)
            ->addIndexColumn()
            // ->editColumn('date',function($link){
            //     $date = Carbon::createFromFormat('Y-m-d', $link->date)->format('d-m-y');
            //     return $date;
            // })
            ->editColumn('url',function($link){
                return '<a target="blank" href="http://'.$link->url.'">'.$link->url.'</a>';
            })
            ->addColumn('action', function($link){
                    $btn = '';
                    $url = route('clients.editLink',$link->id);
                    $btn .= '<a href="javascript:void(0);" onclick="getModal(\''.$url.'\')"  class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                    $btn .= '<a  href="javascript:void(0);" onclick="deleteLink(\''.route('clients.deleteLink',$link->id).'\')"  class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
            })
            ->rawColumns(['url','action'])
            ->make(true);
    }

    public function createLink($id){
        return view('clients.document_links.create',compact('id'));
    }
    public function storeLink(Request $request){
        $result = ClientDocumentsLink::create([
            'client_id' => $request->client_id,
            'url' => $request->url,
            'name' => $request->name,
            'description' => $request->description,
            'date' => $request->date,
          ]);
      if ($result) {
        return response()->json(['msg' => 'Success', 'error' => 0]);
      }
    }

    public function editLink($id){
        $row = ClientDocumentsLink::find($id);
        return view('clients.document_links.edit',compact('id','row'));
    }

    public function updateLink(Request $request,$id){
        $link = ClientDocumentsLink::find($id);
        $link->client_id = $request->client_id;
        $link->url = $request->url;
        $link->name = $request->name;
        $link->description = $request->description;
        $link->date = date('Y-m-d',strtotime($request->date));
        $link->update();
        return response()->json(['msg' => 'Success', 'error' => 0]);
    }

    public function deleteLink($id){
       $link = ClientDocumentsLink::find($id);
       if (!empty($link)) {
            $link->delete();
            return response()->json(['msg' => 'Success', 'error' => 0]);
       }
       else{
        return response()->json(['msg' => 'Something went wrong!', 'error' => 1]);
       }
    }

    public function updateWarehouseAssigned(Request $request){
        $checked_vals = json_decode($request->checked_val);
        $checked_vals = implode(',',$checked_vals);

        $client = Client::find($request->client_id);

        $client = Client::updateOrCreate([
            'id' => $request->client_id,
        ],[
            'product_locations' => $checked_vals,
        ]);
        return true;
    }

    public function upload_bulk_product($id){
        $csvHeader = CsvHeader::where('client_id', $id)->first();
        return view('cranium.upload_bulk_product',['client_id' => $id,'csvHeader' => $csvHeader]);
    }

    public function map_client_product_file($id){
        $csvHeader = CsvHeader::where('client_id', $id)->first();
        return view('cranium.map_client_product_file',['client_id' => $id,'csvHeader' => $csvHeader]);
    }

    public function MapClilentProduct(CsvImportRequest $request)
    {
        try{
            $user = auth()->user();
            $mimes = array('csv');
            $extension = pathinfo($request->file('csv_file')->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = $request->input('name');
            $client_id = $request->client_id;
            if (in_array($extension, $mimes)) {
                $path = $request->file('csv_file')->getRealPath();
                $data = (new HeadingRowImport)->toArray(request()->file('csv_file'));



                if (count($data[0]) > 0) {

                    $csv_header_fields = [];
                    foreach ($data[0] as $key => $value) {
                        $csv_header_fields[] = $key;
                    }

                    //$csv_data = $data[0];
                    $csv_data = array_slice($data[0], 0, 1);
                    $header = $data[0][0];

                    if (isset($header[0])) {
                        $csv_data_file = CsvData::create([
                            'client_id' => $client_id,
                            'csv_filename' => $request->file('csv_file')->getClientOriginalName(),
                            'csv_header' => json_encode($header),
                            'csv_data' => json_encode($data[0])
                        ]);
                        $lastInsertedId = $csv_data_file->id;
                    } else {
                        return response()->json([
                            'error' => true,
                            'msg' => 'First Column of your CSV file is Blank, Unable to Map your Headers'
                        ]);
                    }


                } else {
                    return response()->json([
                        'error' => true,
                        'msg' => 'Something Went Wrong'
                    ]);
                }
                $view = (string)View::make('cranium.import_fields', compact('csv_header_fields', 'csv_data', 'csv_data_file', 'lastInsertedId', 'client_id'));
                return response()->json([
                    'error' => false,
                    'msg' => 'Success',
                    'result' => $view
                ]);

            } else {
                return response()->json([
                    'error' => true,
                    'msg' => 'Please upload CSV formatted file'
                ]);
            }
        }
        catch (\Throwable $e) {
            // dd($e);
            return response()->json([
                'error' => true,
                'msg' => $e->getMessage()
            ]);
        }

    }

    public function saveClientImportHeaders(Request $request){
        $user = auth()->user();
        $csvHeader = CsvHeader::where('client_id', $request->client_id)->first();
        if (!$csvHeader) {
            $fields = $request->input('fields');
            $fields = array_flip($fields);
            unset($fields['Select']);
            $fields = array_flip($fields);
            $csv_header_data = new CsvHeader();
            $csv_header_data->client_id = $request->client_id;
            $csv_header_data->map_type = 'client';
            $csv_header_data->map_data = json_encode($fields);
            $csv_header_data->save();
        } else {
            $fields = $request->input('fields');
            $fields = array_flip($fields);
            unset($fields['Select']);
            $fields = array_flip($fields);
            $csvHeader->saved_headers = json_encode($fields);
            $csvHeader->save();
        }

        return response()->json([
            'error' => false,
            'msg' => 'Success'
        ]);
    }

    public function dalete_client_product_header($id){
        $csvHeader = CsvHeader::where('id', $id)->delete();
        return back()->with(['success' => 'Success']);
    }

    // Billing Section

    public function billingeventList($id){
        $events = ClientBillingEvent::where('client_id',$id)->leftJoin('users',function($join){
            $join->on('users.id','=','client_billing_events.owner');
        })->select('client_billing_events.*','users.name as owner')->get();

        return Datatables::of($events)
					->addIndexColumn()
                    ->addColumn('action', function($event){
							$btn = '';
                            $url = route('clients.editBillingEvent',$event->id);
                            $btn .= '<a href="javascript:void(0);" onclick="editEventModal(\''.$url.'\')"  class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                            $btn .= '<a onclick="deleteEvent(\''.route('clients.deleteBillingEvent',$event->id).'\')"   class="delete btn btn-danger btn-sm text-white">Delete</a>';
                            return $btn;
                    })
                    ->editColumn('date',function($accountNote){
                        return date("m-d-Y h:i",strtotime($accountNote->day_and_time));
                    })
                    ->rawColumns(['action'])
                    ->make(true);
    }

    public function createBillingEvent($id){
        $users = User::select('id','name')->get();
        return view('clients.billing.events.create',compact('id','users'));
    }

    public function storeBillingEvent(Request $request){
      $result = ClientBillingEvent::create([
        'client_id' => $request->client_id,
        'event' => $request->event,
        'frequency' => $request->frequency,
        'day_and_time' => $request->day_and_time,
        'details' => $request->details,
        'owner' => $request->owner,
      ]);
      if ($result) {
        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
      }else{
           return response()->json([
            'error' => 0,
            'msg' => 'Something Went Wrong'
        ]);
      }
    }

    public function editBillingEvent($id){
        $row = ClientBillingEvent::find($id);
        $users = User::select('id','name')->get();
        return view('clients.billing.events.edit',compact('id','row','users'));
    }

    public function updateBillingEvent(Request $request,$id){
        $event = ClientBillingEvent::find($id);
        $event->client_id = $request->client_id;
        $event->event = $request->event;
        $event->frequency = $request->frequency;
        $event->day_and_time = $request->day_and_time;
        $event->details = $request->details;
        $event->owner = $request->owner;
        $event->update();
        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
    }

    public function deleteBillingEvent($id){
       $event = ClientBillingEvent::find($id);
       if (!empty($event)) {
            $event->delete();
            return response()->json([
                'error' => 0,
                'msg' => 'Success'
            ]);
       }
       else{
            return response()->json([
                'error' => 1,
                'msg' => 'Something went wrong!'
            ]);
       }
    }


    // billing note

    public function billingNoteList($id){

        $accountNotes = ClientBillingNote::where('client_id',$id)->leftJoin('users',function($join){
            $join->on('users.id','=','client_billing_notes.added_by');
        })->select('client_billing_notes.*','users.name as added_by')->get();

        return Datatables::of($accountNotes)
            ->addIndexColumn()
            ->addColumn('action', function($accountNote){
                    $btn = '';
                    $url = route('clients.editBillingNote',$accountNote->id);
                    $btn .= '<a href="javascript:void(0);" onclick="editNoteModal(\''.$url.'\')" class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                    if($accountNote->document){
                        $btn .= '<a href="'.route('clients.billingDocument.download',$accountNote->id).'" class="edit btn btn-warning btn-sm mr-2">Download</a>';
                    }
                    else{
                        $btn .= '<a href="#" class="edit btn btn-warning btn-sm mr-2 disabled">Download</a>';
                    }
                    
                    $btn .= '<a onclick="deleteNote(\''.route('clients.deleteBillingNote',$accountNote->id).'\')" class="delete btn btn-danger btn-sm text-white">Delete</a>';
                    return $btn;
            })
            // ->editColumn('date',function($accountNote){
            //     return date("m-d-Y",strtotime($accountNote->date));
            // })
            ->editColumn('is_billable',function($accountNote){
                if($accountNote->is_billable){
                    return "Yes";
                }
                else{
                    return "No";
                } 
            })
            ->editColumn('created_at',function($accountNote){
               return \Carbon\Carbon::parse($accountNote->created_at)->format('m-d-Y');
                //return date("m-d-Y",strtotime($accountNote->created_at));
            })
            // ->editColumn('invoice_date',function($accountNote){
            //     return date("m-d-Y",strtotime($accountNote->invoice_date));
            // })
            ->rawColumns(['date','created_at','invoice_date','is_billable','action'])
            ->make(true);
    }

    public function createBillingNote($id){
        $billingNotes = $this->service->getAll();
        return view('clients.billing.notes.create',compact('id','billingNotes'));
    }
    public function storeBillingNote(Request $request){
        $document = '';
        if($request->hasFile('document'))
        {
            $file = $request->file('document');
            $docPath = public_path('/client_documents/');
            if (!file_exists($docPath)) {
                mkdir($docPath, 0775, true);
            }
            $document = md5(time().'_'.$file->getClientOriginalName()).'.'.$file->getClientOriginalExtension();
            $file->move($docPath,$document);
        }

      $result = ClientBillingNote::create([
        'client_id' => $request->client_id,
        'type' => $request->type,
        'location' => $request->location,
        'details' => $request->details,
        'date' => $request->date,
        'added_by' => Auth::user()->id,
        'document' => $document ?? null,
        'invoice_date'=>$request->invoice_date,
        'is_billable'=> $request->is_billable ? $request->is_billable : 0,
      ]);
      if ($result) {
        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
      }else{
           return response()->json([
            'error' => 0,
            'msg' => 'Something Went Wrong'
        ]);
      }
    }

    public function editBillingNote($id){
        $row = ClientBillingNote::find($id);
        $billingNotes = $this->service->getAll();
        return view('clients.billing.notes.edit',compact('id','row','billingNotes'));
    }

    public function updateBillingNote(Request $request,$id){

        $Eventnote = ClientBillingNote::find($id);
        if($request->hasFile('document'))
        {
            if(isset($Eventnote->document)){

                $file_path = public_path('client_documents/'.$Eventnote->document);
                if(!File::isDirectory($file_path)){
                    unlink($file_path);
                }

                // if(file_exists($file_path)){
                //     unlink($file_path);
                // }
            }
            $file = $request->file('document');
            $docPath = public_path('/client_documents/');
            if (!file_exists($docPath)) {
                mkdir($docPath, 0775, true);
            }
            $document = md5(time().'_'.$file->getClientOriginalName()).'.'.$file->getClientOriginalExtension();
            $file->move($docPath,$document);

            $Eventnote->document = $document;
        }


        $Eventnote->client_id = $request->client_id;
        $Eventnote->type = $request->type;
        $Eventnote->details = $request->details;
        $Eventnote->location = $request->location;
        $Eventnote->date = $request->date;
        $Eventnote->invoice_date = $request->invoice_date;
        $Eventnote->is_billable = $request->is_billable;
        $Eventnote->added_by = Auth::user()->id;

        $Eventnote->update();
        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
    }

    public function deleteBillingNote($id){
       $event = ClientBillingNote::find($id);
       if (!empty($event)) {
            $event->delete();
            return response()->json([
                'error' => 0,
                'msg' => 'Success'
            ]);
       }
       else{
            return response()->json([
                'error' => 1,
                'msg' => 'Something went wrong!'
            ]);
       }
    }

    public function downloadBillingDocument($id){
        $getFile = ClientBillingNote::find($id);

        if(!isset($getFile->document) || $getFile->document == ""){
            return back()->with(['error' => 'No file is there!']);
        }
        $file = public_path().'/client_documents/'.$getFile->document;
        if(File::exists($file)){
            return Response::download($file);
            session()->flash('Success');
        }
        else{
            return back()->with(['error' => 'No file is there!']);
        }
    }

    public function client_orders(){
        $id = auth()->user()->client;
        $row = Client::find($id);
        return view('clients.parts.orders',compact('row'));
    }

    public function client_products_management(){
        $id = auth()->user()->client;
        $row = Client::find($id);
        
        return view('clients.parts.products_management',compact('row'));
    }

    public function client_warehouse_orders(){
        $id = auth()->user()->client;
        $row = Client::find($id);
        $result = [];

        if (isset($row->business_relationship) && strtolower($row->business_relationship) === 'fulfillment') {
            $ps = DB::table('purchasing_summaries')
            ->join('warehouses', 'warehouses.id', '=', 'purchasing_summaries.warehouse_id')
            ->where('purchasing_summaries.client_id', $row->id)
            ->select(['purchasing_summaries.*', 'warehouses.warehouses'])
            ->get();
            if ($ps && count($ps) > 0) {
                foreach($ps as $p) {
                    array_push($result, [
                        'id' => $p->id,
                        'warehouse' => $p->warehouses,
                        'order' => $p->order,
                        'order_date' => $p->purchasing_asn_date,
                        'delivery_date' => $p->delivery_date,
                        'po_status' => $p->po_status,
                        'report_path' => $p->report_path,
                        'invoice' => $p->invoice
                    ]);
                }
            }
        }    
        return view('clients.parts.client_warehouse_orders',compact('row','result'));
    }

    public function client_contacts(){
        $id = auth()->user()->client;
        $row = Client::find($id);
        return view('clients.parts.client_contacts',compact('row'));
    }

    public function clients_documents(){
        $id = auth()->user()->client;
        $row = Client::find($id);
        return view('clients.parts.clients_documents',compact('row'));
    }

    public function clients_information(){
        $id = auth()->user()->client;
        $row = Client::find($id);
        $managers = User::pluck('name','id')->toArray();
        $time_zones = TimeZone::pluck('name','id')->toArray();
        $priceGroup=DB::table('price_group')->whereRaw('FIND_IN_SET('.$id.',lobs)')->get();
        return view('clients.parts.clients_information',compact('row','managers','time_zones','priceGroup'));
    }

    public function clients_reports(){
        $id = auth()->user()->client;
        $row = Client::find($id);
        return view('clients.parts.clients_reports',compact('row'));
    }

    public function update_notification(Request $request, $id)
    {
        
        $user_id = auth()->user()->id;
        $UN = UserNotificationSetting::where('order_by_client',$id)->where('user_id',$user_id)->first();
        if(!$UN){
            $UN = new UserNotificationSetting();
        }
        $UN->user_id = $user_id;
        $UN->notification_type = (isset($request->notification_type) ? implode(',',$request->notification_type) : NULL);
        $UN->order_by_client = $id;
        $UN->order_by_order_type = (isset($request->order_by_order_type) ? implode(',',$request->order_by_order_type) : NULL);
        $UN->order_by_shipping_speed = (isset($request->order_by_shipping_speed) ? implode(',',$request->order_by_shipping_speed) : NULL);
        
        $UN->save();
        return response()->json([
            'error' => false,
            'msg' => 'Success'
        ]);
    }

    public function get_product_warehouse_qty($etin){
        $getwarehouses = DB::table('warehouses')->orderBy('warehouses', 'ASC')->get();
		$result = [];
        $etins = [];
        $is_kit = false;
        $mp = MasterProduct::where('ETIN', $etin)->first();
        
        if (isset($mp) && isset($mp->item_form_description) && str_contains(strtolower($mp->item_form_description), 'kit')) {
            $kit_comps = MasterProductKitComponents::leftJoin('master_product', function($join){
                $join->on('master_product.ETIN','=','master_product_kit_components.components_ETIN');
            })
            ->select('master_product_kit_components.*')
            ->where('master_product_kit_components.ETIN', $mp->ETIN)->get();
            
            if($kit_comps && count($kit_comps) > 0){
                foreach($kit_comps as $row_kit_components){
                    array_push($etins, $row_kit_components->components_ETIN);
                }
                $is_kit = true;
            }
        } else {
            array_push($etins, $etin);
        }
		foreach ($getwarehouses as $warehouselist) {
			$AisleMaster = AisleMaster::where('warehouse_id',$warehouselist->id)->pluck('id')->toArray();
            $count = 0;

            if ($is_kit) {
                $masterShelfSum = 0;
                foreach($etins as $et) {
                    $shelfSum = MasterShelf::where('ETIN',$et)->whereIN('aisle_id',$AisleMaster)->whereIN('location_type_id', [1,2])->sum('cur_qty');
                    $mp = MasterProduct::where('ETIN', $et)->first();
                    if (isset($mp) && isset($mp->parent_ETIN)) {		
                        $etin = $mp->parent_ETIN;
                        $units_in_pack_child = $mp->unit_in_pack;
            
                        $parent = MasterProduct::where('ETIN', $etin)->first();
                        if($parent){
                            $units_in_pack_parent = $parent->unit_in_pack;
    
                            $masterShelfSum_parent = MasterShelf::where('ETIN',$etin)->whereIN('aisle_id',$AisleMaster)->whereIN('location_type_id', [1,2])->sum('cur_qty');
                            if (isset($masterShelfSum_parent) && $masterShelfSum_parent > 0 && $units_in_pack_child > 0) {
                                $count = floor(($masterShelfSum_parent * $units_in_pack_parent)/$units_in_pack_child);
                            }
                            if ($masterShelfSum < ($shelfSum + $count)) {
                                $masterShelfSum = $shelfSum + $count;
                            }
                        }                    
                    } else {
                        $sum = MasterShelf::whereIn('ETIN',$etins)
                            ->whereIN('aisle_id',$AisleMaster)
                            ->whereIN('location_type_id', [1,2])
                            ->where('cur_qty', '>', 0)
                            ->orderBy('cur_qty', 'asc')
                            ->limit(1)
                            ->sum('cur_qty');
                        $masterShelfSum += !isset($sum) && $sum <= 0 ? 0 : $sum;                        
                    }
                }                
            } else {
                $masterShelfSum = MasterShelf::whereIn('ETIN',$etins)->whereIN('aisle_id',$AisleMaster)->whereIN('location_type_id', [1,2])->sum('cur_qty');
                if (isset($mp) && isset($mp->parent_ETIN)) {		
                    $etin = $mp->parent_ETIN;
                    $units_in_pack_child = $mp->unit_in_pack;
        
                    $parent = MasterProduct::where('ETIN', $etin)->first();
                    if($parent){
                        $units_in_pack_parent = $parent->unit_in_pack;

                        $masterShelfSum_parent = MasterShelf::where('ETIN',$etin)->whereIN('aisle_id',$AisleMaster)->whereIN('location_type_id', [1,2])->sum('cur_qty');
                        if (isset($masterShelfSum_parent) && $masterShelfSum_parent > 0 && $units_in_pack_child > 0) {
                            $count = floor(($masterShelfSum_parent * $units_in_pack_parent)/$units_in_pack_child);
                        }
                        $masterShelfSum = $masterShelfSum + $count;
                    }                    
                }
            }        	
			$result[] = [
				'count' => $masterShelfSum,
				'name' => $warehouselist->warehouses
			];
		}
        return view('clients.product_warehouse_qty', compact('result'));
    }

    public function change_channel_status($id, $cl_id) {

        $cl = ClientChannelConfiguration::find($id);
        if (!isset($cl)) {
            return redirect()->route('clients.edit',$cl_id)->with('error','Invalid Channel/Channel not found.');
        }

        $cl->is_active = !$cl->is_active;
        $cl->save();

        $this->changeOrderStatus($cl->client_id, $cl->is_active == 1 ? 1 : 2, $id);

        return redirect()->route('clients.edit',$cl_id)->with('success','Channel active status updated successfully');
    }
    
}
