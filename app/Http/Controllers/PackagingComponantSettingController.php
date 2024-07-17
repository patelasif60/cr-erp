<?php

namespace App\Http\Controllers;

use App\Client;
use DataTables;
use App\MasterProduct;
use App\PackagingMaterials;
use App\ProductTemperature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ClientChannelConfiguration;
use App\CustomClientOuterBox;
use App\Services\PackagingComponantSettingService;

class PackagingComponantSettingController extends Controller
{
	public function __construct(PackagingComponantSettingService $service)
	{
        $this->service = $service;
	}
	public function index(){
        $ccob = CustomClientOuterBox::all();
        return view('cranium.packagingcomponantsetting.index', compact('ccob'));
    }
    public function getPackagingComponents(Request $request){
        $results = $this->service->getPackagematirial($request);
        if($request->type == 'settings')
        {
            return Datatables::of($results)->addColumn('action', function($row){
                $btn = '';
                $btn .= '<a href="javascript:void(0)" onClick=openQtyModal(\''.$row->id.'\') class="btn btn-primary btn-sm">Add package</a>';
                return $btn;
                
            })->rawColumns(['action'])->make(true);
        }
        if(count($results)>0){
            //$results->first()->packagingMaterials()->groupBy('product_description') 
            $data = PackagingMaterials::whereIn('material_type_id',$results->pluck('id')->toArray())
                ->groupBy('product_description')->get();
            return Datatables::of($data)->addColumn('action', function($result)
            {
                $btn = '';
                $btn .= '<a href="'.route('packagingcomponant.edit',$result->id).'" class="btn btn-primary">View</a>';
                return $btn;
                
            })->rawColumns(['action'])->make(true);
        }
        else
        {
            return Datatables::of($results)->addColumn('action', function($row){

                $btn = '';
                $btn .= '<a href="javascript:void(0)" onClick=openQtyModal(\''.$row->id.'\') class="btn btn-primary btn-sm">Add package</a>';
                return $btn;
                
            })->rawColumns(['action'])->make(true);
        }
    }
    public function editPackagingComponantSetting($id)
    {
        $producttemp = ProductTemperature::orderBy('product_temperature','ASC')->pluck('product_temperature','id')->toArray();
        $producttemp[0]= 'All Temperatures';
        sort($producttemp);
        $componentsSettings = $this->service->getPackagematirialSetting($id);
        $packMat = PackagingMaterials::find($id);
        $desc = $packMat->product_description;
        return view('cranium.packagingcomponantsetting.edit',compact('producttemp','componentsSettings','id','desc'));
    }
    public function getTempComponents(Request $request){
        $tempComponents = $this->service->getTempComponents($request);
        if($tempComponents)
        {
            $res[$request->parentId] = 0;
            foreach($tempComponents as $tempComponentsKey => $tempComponentsVal){
                $res[$tempComponentsVal->child_packaging_materials_id] = $tempComponentsVal['qty'];
            }
            $response['selectedData'] = json_encode($res);
            return view('cranium.packagingcomponantsetting.gettempcomponents',compact('tempComponents','response'));
        }
        return ;
    }
    public function update(Request $request,$id)
    {
        $this->service->update($request,$id);
         return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('packagingcomponant.edit')
        ]);
    }

    public function new_custom_outer() {
        $outer_box = DB::select("select * from packaging_materials pm where pm.id not in 
            (select ccob.box_id from custom_client_outer_boxes ccob)");
        $clients = Client::all();
        return view('cranium.packagingcomponantsetting.add_custom_outer', compact('outer_box', 'clients'));
    }

    public function edit_custom_outer($map_id) {
        $outer_box = PackagingMaterials::all();
        $clients = Client::all();

        $ccob = CustomClientOuterBox::find($map_id);

        $channels = ClientChannelConfiguration::where('client_id', $ccob->client_id)->get()->toArray();
        $result_obj = MasterProduct::whereRaw('FIND_IN_SET(' . $ccob->client_id. ',lobs)');
		$result_obj->select(DB::raw('CONCAT(product_listing_name," - ", ETIN) as product_name'),'ETIN','id');	
		$products = !isset($result_obj) ? array() : $result_obj->get()->toArray();

        return view('cranium.packagingcomponantsetting.edit_custom_outer', 
            compact('outer_box', 'clients', 'ccob', 'channels', 'products'));
    }

    public function get_client_channels_and_product($client_id) {

        $channels = ClientChannelConfiguration::where('client_id', $client_id)->get()->toArray();

		$result_obj = MasterProduct::whereRaw('FIND_IN_SET(' . $client_id. ',lobs) > 0');
		$result_obj->select(DB::raw('CONCAT(product_listing_name," - ", ETIN) as product_name'),'ETIN','id','unit_description','item_form_description');	
		$products = !isset($result_obj) ? array() : $result_obj->get()->toArray();

        return response(['channels' => $channels, 'products' => $products], 200);
	}

    public function store_custom_outer(Request $request) {
        
        $box_name = $request->box_name;
        $client_id = $request->client_id;
        $is_edit = $request->is_edit;

        $ccob = CustomClientOuterBox::where('box_id', $box_name)->first();
        if (isset($ccob) && $ccob->client_id != $client_id && $is_edit != 1) {
            return response()->json([
                'error' => 1, 'msg' => 'Box is alredy assigned to a '
            ]);
        }

        $channel_ids = NULL;
        if (isset($request->channel_ids)) $channel_ids = $request->channel_ids;
        $product_ids = $request->product_ids;
        $transit_day = $request->transit_day;
        $max_item_count = $request->max_item_count;

        $map_id = $request->id;

        if ($is_edit != 1) {
            CustomClientOuterBox::create([
                'box_id' => $box_name,
                'client_id' => $client_id,
                'channel_ids' => $channel_ids,
                'product_ids' => $product_ids,
                'transit_days' => $transit_day,
                'max_item_count' => $max_item_count
            ]);
        } else {
            $ccob = CustomClientOuterBox::find($map_id);
            $ccob->box_id = $box_name;
            $ccob->client_id = $client_id;
            $ccob->channel_ids = $channel_ids;
            $ccob->product_ids = $product_ids;
            $ccob->transit_days = $transit_day;
            $ccob->max_item_count = $max_item_count;
            $ccob->save();
        }  

        return response()->json([
            'error' => 0, 'msg' => 'Outer box mapped successfully'
        ]);
    }
}