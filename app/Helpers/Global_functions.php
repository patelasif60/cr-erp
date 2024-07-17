<?php

use App\HotRoute;
use App\OrderDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
function ProductEditPermission($key){
    $array = [
                'full_product_desc' => [
                    'name' => 'Full Product Description',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'lobs' => [
                    'name' => 'LOB(s) (Clients & Sites Assigned)',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'about_this_item' => [
                    'name' => 'About this item',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'manufacturer' => [
                    'name' => 'Manufacturer',
                    'user' => 0,
                    'manager' => 1,
                    'admin' => 1
                ],
                'brand' => [
                    'name' => 'Brand',
                    'user' => 0,
                    'manager' => 1,
                    'admin' => 1
                ],
                'flavor' => [
                    'name' => 'Flavor',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'product_type' => [
                    'name' => 'Product Type',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'unit_num' => [
                    'name' => 'Unit Size',
                    'user' => 0,
                    'manager' => 0,
                    'admin' => 1
                ],
                'unit_num_duplicate' => [
                    'name' => 'Unit Size',
                    'user' => 1,
                    'manager' => 0,
                    'admin' => 1
                ],
                'unit_list' => [
                    'name' => 'Unit List',
                    'user' => 0,
                    'manager' => 0,
                    'admin' => 1
                ],
                'unit_description' => [
                    'name' => 'Unit Description',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'pack_form_count' => [
                    'name' => 'Pack Form Count',
                    'user' => 0,
                    'manager' => 0,
                    'admin' => 1
                ],
                'unit_in_pack' => [
                    'name' => 'Units in Pack',
                    'user' => 0,
                    'manager' => 0,
                    'admin' => 1
                ],
                'item_form_description' => [
                    'name' => 'Item Form Description',
                    'user' => 0,
                    'manager' => 0,
                    'admin' => 1
                ],
                'product_category' => [
                    'name' => 'Product Category',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'product_subcategory1' => [
                    'name' => 'Product Subcategory 1',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'product_tags' => [
                    'name' => 'Product Tags',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'MFG_shelf_life' => [
                    'name' => 'MFG Shelf Life',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'hazardous_materials' => [
                    'name' => 'Hazardous Materials',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'ingredients' => [
                    'name' => 'Ingredients ',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'allergens' => [
                    'name' => 'Allergens',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'prop_65_flag' => [
                    'name' => 'Prop 65 Flag',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'prop_65_ingredient' => [
                    'name' => 'Prop 65 Ingredient',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'product_temperature' => [
                    'name' => 'Product Temperature',
                    'user' => 0,
                    'manager' => 0,
                    'admin' => 1
                ],
                'supplier_product_number' => [
                    'name' => 'Supplier Product Number',
                    'user' => 0,
                    'manager' => 1,
                    'admin' => 1
                ],
                'upc' => [
                    'name' => 'UPC',
                    'user' => 0,
                    'manager' => 0,
                    'admin' => 1
                ],
                'gtin' => [
                    'name' => 'GTIN',
                    'user' => 0,
                    'manager' => 0,
                    'admin' => 1
                ],
                'asin' => [
                    'name' => 'ASIN',
                    'user' => 0,
                    'manager' => 0,
                    'admin' => 1
                ],
                'weight' => [
                    'name' => 'Weight (lbs)',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'length' => [
                    'name' => 'Length (in)',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'width' => [
                    'name' => 'Width (in)',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'height' => [
                    'name' => 'Height (in)',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'etailer_availability' => [
                    'name' => 'e-tailer Availability',
                    'user' => 0,
                    'manager' => 1,
                    'admin' => 1
                ],
                'dropship_available' => [
                    'name' => 'Dropship Available',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'current_supplier' => [
                    'name' => 'Current Supplier',
                    'user' => 0,
                    'manager' => 1,
                    'admin' => 1
                ],
                'supplier_status' => [
                    'name' => 'Supplier Status',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'cost' => [
                    'name' => 'Cost',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'acquisition_cost' => [
                    'name' => 'Aquisition Cost',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'warehouses_assigned' => [
                    'name' => 'Warehouse(s) Assigned',
                    'user' => 0,
                    'manager' => 1,
                    'admin' => 1
                ],
                'status' => [
                    'name' => 'Status',
                    'user' => 1,
                    'manager' => 1,
                    'admin' => 1
                ],
                'manufacture_product_number' => [
                    'name' => 'Manufacture Product Number',
                    'user' => 0,
                    'manager' => 0,
                    'admin' => 1
                ],

            ];
            $role = Auth::user()->role;
            if($role == 1){
                $user = 'admin';
            }

            if($role == 2){
                $user = 'manager';
            }

            if($role == 3){
                $user = 'user';
            }
            $return_val = 0;
            if(isset($array[$key])){
                $return_val = $array[$key][$user];
            }
            return $return_val;
}


function Menu(){

    $result = DB::table('modules')->where('parent_menu_id',0)->where('is_module',1)->orderBy('sort','ASC')->get();
    $first_menu_html = '';
    $second_html = '';
	if(!empty($result)){
		foreach($result as $res)
		{
			$access = moduleacess($res->module_slug,'access',$res->id);
			$Display_cart_icon_or_not=0;
            $second_level = DB::table('modules')->where('parent_menu_id',$res->id)->where('is_module',1)->orderBy('sort','ASC')->get();
            if(count($second_level)>0) $Display_cart_icon_or_not=1; else $Display_cart_icon_or_not=0;
			if($res->module_link!=''){
				$link = url($res->module_link);
			}else{
				$link ='#';
			}
			$menu_title = $res->menu_title;
            if($access){
                $first_menu_html.=' <li class="nav-item '.(request()->is(''.$link.'/*') ? 'active' : '').'" '.($Display_cart_icon_or_not=='1'?'data-item="child_'.$res->id.'"':'').'>
                    <a class="nav-item-hold" href="'.url($link).'">
                        <i class="'.$res->module_icon.'"></i>
                        <span class="nav-text">'.$menu_title.'</span>
                    </a>
                    <div class="triangle"></div>
                </li>';

                if($Display_cart_icon_or_not == 1){
                    $second_html.='<ul class="childNav" data-parent="child_'.$res->id.'">';
                        if($second_level){
                            foreach($second_level as $row_second){
                                if($row_second->module_link!=''){
                                    $link = url($row_second->module_link);
                                }else{
                                    $link ='#';
                                }
                                $access1 = moduleacess($row_second->module_slug,'access',$row_second->id);
                                if($access1){
                                    $second_html.='<li class="nav-item">
                                        <a class="" href="'.url($link).'">
                                            <i class="'.$row_second->module_icon.'"></i>
                                            <span class="item-name">'.$row_second->menu_title.'</span>
                                        </a>
                                    </li>';
                                }
                            }
                        }
                    $second_html.='</ul>';
                }
            }
		}
	}

    return [
        'first_menu_html' => $first_menu_html,
        'second_html' => $second_html
    ];
}

// function moduleacess($link,$type,$id = ''){
// 	$role = Auth::user()->role;
// 	// if($role == 1) return true;
// 	if($link == ''){
// 		$result = DB::table('role_permissions')->where('role_id',$role)->where('module_id',$id)->where($type,1)->count();
// 	}else{
// 		$result = DB::table('role_permissions')->where('role_id',$role)->where('module_link',$link)->where($type,1)->count();
// 	}

//     if($result>0){
//         return true;
//     }else{
//         return false;
//     }

// }

function moduleacess($link){
    $role = Auth::user()->role;
    $result = 0;
    if($role == 1){
        $result = DB::table('roles_permissions')->where('administrator',1)->where('module_link',$link)->count();
    }
    if($role == 2){
        $result = DB::table('roles_permissions')->where('manager',1)->where('module_link',$link)->count();
    }
    if($role == 3){
        $result = DB::table('roles_permissions')->where('user',1)->where('module_link',$link)->count();
    }
    if($result > 0){
        return true;
    }else{
        return false;
    }
}

//for buttons
function ReadWriteAccess($link){
    $role = Auth::user()->role;
    $result = 0;
    if($role == 1){
        $result = DB::table('roles_permissions')->where('administrator',1)->where('module_link',$link)->count();
    }
    if($role == 2){
        $result = DB::table('roles_permissions')->where('manager',1)->where('module_link',$link)->count();
    }
    if($role == 3){
        $result = DB::table('roles_permissions')->where('user',1)->where('module_link',$link)->count();
    }
    if($result > 0){
        return true;
    }else{
        return false;
    }
}

function ModuleAcessWithRole($link,$role){
    $result = 0;
    $result = DB::table('roles_permissions')->where($role,1)->where('module_link',$link)->count();
    if($result > 0){
        return true;
    }else{
        return false;
    }
}

function GetRolesForPermission($link){
    $row = [];
    $result = DB::table('roles_permissions')->where('module_link',$link)->first();
    if($result){
        if($result->administrator == 1){
            $row[] = 1;
        }

        if($result->manager == 1){
            $row[] = 2;
        }

        if($result->user == 1){
            $row[] = 3;
        }

        if($result->wms_manager == 1){
            $row[] = 4;
        }

        if($result->wms_user == 1){
            $row[] = 5;
        }
    }
    return $row;
}

function SendProductNotification(){
    $users = DB::table('user_notification_settings')->where('product_management',1)->get();
    $row = [];
    if($users){
        foreach($users as $row_users){
            $row[] = ['id' => $row_users->user_id,'type' => $row_users->notification_type];
        }
    }

    return $row;
}

function GetUsersForPermission($link){
    $users = DB::table('users')->whereRaw('FIND_IN_SET("'.$link.'", notification)')->get();
    $row = [];
    if($users){
        foreach($users as $row_users){
            $row[] = $row_users->id;
        }
    }

    return $row;
}

function ProperInput($input){
    $special_chars = htmlspecialchars_decode($input);
    $str_trim = ltrim(rtrim($special_chars));
    $str_case = ucwords(strtolower($str_trim));
    return $str_case;
}


function allergensName($id){
    $name = '';
    $result = DB::table('allergens')->where('id',$id)->first();
    if(isset($result->allergens)){
        $name = $result->allergens;
    }
    return $name;
}

function allergensID($name){
    $id = NULL;
    $result = DB::table('allergens')->where('allergens',$name)->first();
    if(isset($result->id)){
        $id = $result->id;
    }
    return $id;
}

function countryName($id){
    $name = '';
    $result = DB::table('country_of_origin')->where('id',$id)->first();
    if(isset($result->country_of_origin)){
        $name = $result->country_of_origin;
    }
    return $name;
}


function countryID($name){
    $id = NULL;
    $result = DB::table('country_of_origin')->where('country_of_origin',$name)->first();
    if(isset($result->id)){
        $id = $result->id;
    }
    return $id;
}
function prop_65_name($id){
    $name = '';
    $result = DB::table('prop_ingredients')->where('id',$id)->first();
    if(isset($result->prop_ingredients)){
        $name = $result->prop_ingredients;
    }
    return $name;
}

function producttageName($id){
    $name = '';
    $result = DB::table('product_tags')->where('id',$id)->first();
    if(isset($result->tag)){
        $name = $result->tag;
    }
    return $name;
}

function clientName($id){
    $name = '';
    $result = DB::table('clients')->where('id',$id)->first();
    if(isset($result->company_name)){
        $name = $result->company_name;
    }
    return $name;
}

function GetClientExpLotSetting($id){
    $exp_lot = '';
    $result = DB::table('clients')->where('id',$id)->first();
    if(isset($result->exp_lot)){
        $exp_lot = $result->exp_lot;
    }
    return $exp_lot;
}

function SupplierStatus($id){
    $name = '';
    $result = DB::table('supplier_status')->where('id',$id)->first();
    if(isset($result->supplier_status)){
        $name = $result->supplier_status;
    }
    return $name;
}

function SupplierStatusID($name){
    $id = NULL;
    $result = DB::table('supplier_status')->where('supplier_status',$name)->first();
    if(isset($result->supplier_status)){
        $id = $result->id;
    }
    return $id;
}

function etailerName($id){
    $name = '';
    $result = DB::table('etailer_availability')->where('id',$id)->first();
    if(isset($result->etailer_availability)){
        $name = $result->etailer_availability;
    }
    return $name;
}



function EtailerAvailabilityName($id){
    $name = '';
    $result = DB::table('etailer_availability')->where('id',$id)->first();
    if(isset($result->etailer_availability)){
        $name = $result->etailer_availability;
    }
    return $name;
}
function supplierStatusName($id){
    $name = '';
    $result = DB::table('supplier_status')->where('id',$id)->first();
    if(isset($result->supplier_status)){
        $name = $result->supplier_status;
    }
    return $name;
}

function CategoryName($id){
    $name = '';
    $result = DB::table('categories')->where('id',$id)->first();
    if(isset($result->name)){
        $name = $result->name;
    }
    return $name;
}

function CategoryID($name){
    $id = NULL;
    $result = DB::table('categories')->where('name',$name)->first();
    if(isset($result->id)){
        $id = $result->id;
    }
    return $id;
}

function SupplierName($id){
    $name = NULL;
    $result = DB::table('suppliers')->where('id',$id)->first();
    if(isset($result->name)){
        $name = $result->name;
    }
    return $name;
}

function GetSupplierExpLotSetting($id){
    $exp_lot = '';
    $result = DB::table('suppliers')->where('id',$id)->first();
    if(isset($result->exp_lot)){
        $exp_lot = $result->exp_lot;
    }
    return $exp_lot;
}



function OrderDetailStatusName($id){
    $name = NULL;
    $result = DB::table('order_details_status')->where('id',$id)->first();
    if(isset($result->status)){
        $name = $result->status;
    }
    return $name;
}

function OrderSummeryStatusName($id){
    $name = NULL;
    $result = DB::table('order_summary_status')->where('id',$id)->first();
    if(isset($result->order_status_name)){
        $name = $result->order_status_name;
    }
    return $name;
}

function UserName($id){
    $name = '';
    $result = DB::table('users')->where('id',$id)->first();
    if(isset($result->name)){
        $name = $result->name;
    }
    return $name;
}

function GetOption($data){

    $table = $data['table'];
    $value = $data['value'];
    $label = $data['label'];
    $selected_value = $data['selected_value'];
    $option = '';
    if($table != '' && $value != '' && $label != ''){
        if($table == 'master_product'  && $data['column_name'] == 'ETIN' ){
            $query = DB::table($table)->where('is_approve',1)->orderBy($label,'ASC');
        }
        else{
            $query = DB::table($table)->orderBy($label,'ASC');    
        }
        
        if($data['column_name'] == 'product_category')
        {
            $query = DB::table($table)->where('level',0)->orderBy($label,'ASC');
        }
        if($data['column_name'] == 'product_subcategory1')
        {
            $query = DB::table($table)->where('level',1)->orderBy($label,'ASC');
        }
        if($data['column_name'] == 'product_subcategory2')
        {
            $query = DB::table($table)->where('level',2)->orderBy($label,'ASC');
        }
        if($data['column_name'] == 'product_subcategory3')
        {
            $query = DB::table($table)->where('level',3)->orderBy($label,'ASC');
        }
        if($data['column_name'] == 'product_subcategory4')
        {
            $query = DB::table($table)->where('level',4)->orderBy($label,'ASC');
        }
        if($data['column_name'] == 'product_subcategory5')
        {
            $query = DB::table($table)->where('level',5)->orderBy($label,'ASC');
        }
        if($data['column_name'] == 'product_subcategory6')
        {
            $query = DB::table($table)->where('level',6)->orderBy($label,'ASC');
        }
        if($data['column_name'] == 'product_subcategory7')
        {
            $query = DB::table($table)->where('level',7)->orderBy($label,'ASC');
        }
        if($data['column_name'] == 'product_subcategory8')
        {
            $query = DB::table($table)->where('level',8)->orderBy($label,'ASC');
        }
        if($data['column_name'] == 'product_subcategory9')
        {
            $query = DB::table($table)->where('level',9)->orderBy($label,'ASC');
        }
        $result = $query->get();
        if($result){
            $used = [];
            foreach($result as $row){
                if($selected_value != '') {
                    $option.='<option value="'.$row->$value.'" '.(in_array($row->$value,explode(',',$selected_value)) ? 'selected': '').'>'.$row->$label.'</option>';
                } else {
                    if ($table === 'order_summary') {
                        if (in_array($row->$label, $used)) {
                            continue;
                        }
                    }
                    $option.='<option value="'.$row->$value.'">'.$row->$label.'</option>';
                    array_push($used, $row->$label);
                }
            }
        }
    }
    echo $option;

}


function rand_color() {
    // return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    return 'rgb( '.mt_rand(0, 255).' '.mt_rand(0, 255).' '.mt_rand(0, 255).' /30%)';
}

function AllUsers(){
    $data = [];
    $result = DB::table('users')->get();
    if($result){
        foreach($result as $row){
            $data[] = $row->name;
        }
    }
    return $data;
}

function get_temp($fullfilledBy, $subOrderNumber) {
    switch(strtolower($fullfilledBy)) {
        case "e-tailer":
            if (str_contains($subOrderNumber, '.001')) {
                return 'Frozen';
            } else if (str_contains($subOrderNumber, '.002')) {
                return 'Dry';
            } else if (str_contains($subOrderNumber, '.003')) {
                return 'Refrigerated';
            }
            return "";
        case "dot":
            if (str_contains($subOrderNumber, '.004')) {
                return 'Frozen';
            } else if (str_contains($subOrderNumber, '.005')) {
                return 'Dry';
            } else if (str_contains($subOrderNumber, '.006')) {
                return 'Refrigerated';
            }  
            return "";              
        case "kehe":
            if (str_contains($subOrderNumber, '.006')) {
                return 'Dry';
            } 
            return "";
        default:
            return "";
    }
}

function UpdateOrderHistory($input){
    $sub_order_number = isset($input['sub_order_number']) ? $input['sub_order_number']:NULL;
    $etailer_order_number = isset($input['order_number']) ? $input['order_number']:NULL;
    $user_id = isset($input['user_id']) ? $input['user_id']:NULL;
    $details = isset($input['detail']) ? $input['detail']:NULL;
    $action = isset($input['title']) ? $input['title']:NULL;
    $reference = isset($input['reference']) ? $input['reference']:NULL;
    $extras = isset($input['extras']) ? $input['extras']:NULL;
     
    
    DB::table('order_history')->insert([
        'etailer_order_number' => $etailer_order_number,
        'sub_order_number' => $sub_order_number,
        'user_id' => $user_id,
        'details' => $details,
        'action' => $action,
        'reference' => $reference,
        'extras' => $extras,
        'date' => date("Y-m-d H:i:s"),
        'created_at' => date("Y-m-d H:i:s"),
        'updated_at' => date("Y-m-d H:i:s")
    ]);
    

}

function find_parent_ETIN($ETIN){
    $parent_ETIN = NULL;

    $master_product = DB::table('master_product')->where('ETIN', $ETIN)->first();
    if($master_product){
        $parent_ETIN = $master_product->parent_ETIN;
        return $parent_ETIN;
    }
    
    return NULL;
}

function ExtractToken($token){
    $user_id = null;
    if($token !== ''){
        $user_id = str_replace('Bearer ', '', $token);
        $user_id = base64_decode($user_id);
    }

    return $user_id;
}



function _group_by($array, $key) {
    $return = array();
    foreach($array as $val) {
        $return[$val[$key]][] = $val;
    }
    return $return;
}

function convertDateTimeTo12Hour($data) {

    $toReturn = [];
    foreach ($data as $datum) {

        $time = strtotime($datum['created_at']);
        $new_time = date("Y-m-d g:i:s A", $time);
        $datum['created_at'] = $new_time;       
        array_push($toReturn, $datum);
    }
    return $toReturn;
}

function create_time_range($start, $end, $interval = '30 mins', $format = '24') {
    $startTime = strtotime($start); 
    $endTime   = strtotime($end);
    $returnTimeFormat = ($format == '12')?'g:i A':'G:i';

    $current   = time(); 
    $addTime   = strtotime('+'.$interval, $current); 
    $diff      = $addTime - $current;

    $times = array(); 
    while ($startTime < $endTime) { 
        $times[] = date($returnTimeFormat, $startTime); 
        $startTime += $diff; 
    } 
    $times[] = date($returnTimeFormat, $startTime); 
    return $times; 
}

function checkHotRoute($summary) {    

    Log::channel('IncomingOrderProcessing')->info('Starting Hot Route Processing for Order: ' . $summary->etailer_order_number);
    $zip = $summary->ship_to_zip;
    if (!isset($zip)) {
        Log::channel('IncomingOrderProcessing')->info('Aborting. No Zip found for Order: ' . $summary->etailer_order_number);
        return;
    }
    
    $order_details = OrderDetail::where('order_number', $summary->etailer_order_number)->get();
    
    if (!isset($order_details) || count($order_details) <= 0) {
        Log::channel('IncomingOrderProcessing')->info('Aborting. No Items found for Order: ' . $summary->etailer_order_number);
        return;
    }
    
    foreach($order_details as $od) {    
        
        
        if (isset($od->warehouse) && ($od->warehouse == 'CF' || $od->status == '17')) {
            continue;
        }
        
        if (isset($od->warehouse) && isset($od->carrier_id) && isset($od->service_type_id) 
                && ($od->service_type_id == 1 || $od->service_type_id == 19)) {
            $hr = HotRoute::where('wh_id', $od->warehouse_info->id)->where('carrier_id', $od->carrier_id)
                ->where('zip', $zip)->first();
            if (isset($hr)) {

                $default_tz = date_default_timezone_get();
                date_default_timezone_set($od->warehouse_info->time_zone);
                $time = localtime(time(),true);
                $now_time = $time['tm_hour'] . ':' . $time['tm_min'];
                date_default_timezone_set($default_tz);

                if (isset($hr->cut_off_time) && $hr->cut_off_time != '' 
                    && date('H:i', strtotime($now_time)) > date('H:i', strtotime($hr->cut_off_time))) {
                        continue;
                    }

                Log::channel('IncomingOrderProcessing')
                    ->info('Setting TD for Sub-Order: ' . $od->sub_order_number. '. Old TD: ' . $od->transit_days . '. New TD: ' . $hr->transit_days);
                $od->transit_days = $hr->transit_days;
                $od->hot_route = 1;
                $od->save();
            }
        }
    }
    Log::channel('IncomingOrderProcessing')->info('Hot Route Processing completed for Order: ' . $summary->etailer_order_number);
}

function DeveloperLog($data){
    DB::table('developer_log')->insert([
        'reference' => $data['reference'],
        'ref_request' => $data['ref_request'],
        'ref_response' => $data['ref_response'],
        'created_at' => date('Y-m-d H:i:s')
    ]);
}


function InventoryAdjustmentLog($input){
    $ETIN = isset($input['ETIN']) ? $input['ETIN'] : NULL;
    $location = isset($input['location']) ? $input['location'] : NULL;
    $starting_qty = isset($input['starting_qty']) ? $input['starting_qty'] : NULL;
    $ending_qty = isset($input['ending_qty']) ? $input['ending_qty'] : NULL;
    $total_change = isset($input['total_change']) ? $input['total_change'] : NULL;
    $user = isset($input['user']) ? $input['user'] : NULL;
    $warehouse = isset($input['warehouse']) ? $input['warehouse'] : NULL;
    $reference = isset($input['reference']) ? $input['reference'] : NULL;
    $reference_value = isset($input['reference_value']) ? $input['reference_value'] : NULL;
    $reference_description = isset($input['reference_description']) ? $input['reference_description'] : NULL;

    DB::table('inventry_adjustment_report')->insert([
        'ETIN' => $ETIN,
        'location' => $location,
        'starting_qty' => $starting_qty,
        'ending_qty' => $ending_qty,
        'total_change' => $total_change,
        'user' => $user,
        'warehouse' => $warehouse,
        'reference' => $reference,
        'reference_value' => $reference_value,
        'reference_description' => $reference_description,
        'created_at' => date('Y-m-d H:i:s')
    ]);
}

function UserLogs($input){
    $user_id = isset($input['user_id']) ? $input['user_id'] : NULL;
    $action = isset($input['action']) ? $input['action'] : NULL;
    $task = isset($input['task']) ? $input['task'] : NULL;
    $details = isset($input['details']) ? $input['details'] : NULL;
    $etailer_order_number = isset($input['etailer_order_number']) ? $input['etailer_order_number'] : NULL;
    $channel_order_number = isset($input['channel_order_number']) ? $input['channel_order_number'] : NULL;
    $client_order_number = isset($input['client_order_number']) ? $input['client_order_number'] : NULL;
    $tracking_number = isset($input['tracking_number']) ? $input['tracking_number'] : NULL;
    $type = isset($input['type']) ? $input['type'] : NULL;
    $order_date = isset($input['order_date']) ? $input['order_date'] : NULL;
    $order_time = isset($input['order_time']) ? $input['order_time'] : NULL;
    $po_number = isset($input['po_number']) ? $input['po_number'] : NULL;
    $bol_number = isset($input['bol_number']) ? $input['bol_number'] : NULL;

    DB::table('user_logs')->insert([
        'user_id' => $user_id,
        'action' => $action,
        'task' => $task,
        'details' => $details,
        'etailer_order_number' => $etailer_order_number,
        'channel_order_number' => $channel_order_number,
        'client_order_number' => $client_order_number,
        'tracking_number' => $tracking_number,
        'type' => $type,
        'order_date' => date('Y-m-d'),
        'order_time' => date('H:i:s'), 
        'po_number' => $po_number,
        'bol_number' => $bol_number,
        'created_at' => date('Y-m-d H:i:s')
    ]);

}

function LogIncommingOrderProcessing($input){
    $req = isset($input['req']) ? $input['req'] : NULL;
    $res = isset($input['res']) ? $input['res'] : NULL;

    DB::table('incoming_order_processing')->insert([
        'req' => $req,
        'res' => $res,
        'created_at' => date('Y-m-d H:i:s')
    ]);

}