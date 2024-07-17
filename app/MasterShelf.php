<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\BayMaster;
use App\LocationType;
use App\AisleMaster;
use App\WareHouse;
use Illuminate\Support\Facades\Log;

class MasterShelf extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'master_shelf';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    public $timestamps = true;

    protected $hidden = ['created_at', 'updated_at'];
    
    public function bay_name(){
        return $this->belongsTo(BayMaster::class,'bay_id','id');
    }
    public function location_type(){
        return $this->belongsTo(LocationType::class,'location_type_id','id');
    }

    public function ailse(){
        return $this->belongsTo(AisleMaster::class,'aisle_id','id');
    }

    public function backstock_location(){
        return $this->hasMany(MasterShelf::class,'ETIN','ETIN');
    }

    public function product(){
        return $this->belongsTo(MasterProduct::class,'ETIN','ETIN');
    }

    public function child(){
        return $this->hasMany(MasterShelf::class,'parent_id','id');
    }

    public function GetTheMasterShelfQty($input){
        $ETIN = isset($input['ETIN']) ? $input['ETIN'] : NULL;
        $address = isset($input['address']) ? $input['address'] : NULL;
        
        if($ETIN != '' && $address != ''){
            $MS = MasterShelf::where('ETIN',$ETIN)->where('address',$address)->first();
            if($MS){
                return $MS->toArray();
            }
        }

        return false;

    }



    public function FinnishPutAway($pa,$user_id,$bol_number){
        $ms = MasterShelf::where('address', $pa->location)->where('ETIN', $pa->etin)->first();
        $ailse = $ms->ailse;
        $warehouse_id = isset($ailse->warehouse_id) ? $ailse->warehouse_id : NULL;
        InventoryAdjustmentLog([
            'ETIN' => $ms->ETIN,
            'location' => $ms->address,
            'starting_qty' => $ms->cur_qty,
            'ending_qty' => $ms->cur_qty + $pa->quantity,
            'total_change' => '+'.$pa->quantity,
            'user' => $user_id,
            'reference' => 'Put Away',
            'reference_value' => 'bol_number: '.$bol_number,
            'reference_description' => 'Updating Qty for ETIN while pickup order: savePutAway'
        ]);

        Log::channel('Inventory')->info('Before Current Qty: '.$ms->cur_qty);
        Log::channel('Inventory')->info('Qty to add through Put Away: '.$pa->quantity);
        $ms->cur_qty = $ms->cur_qty + $pa->quantity;
        $ms->save();

        if($pa->lot != ''){
            $MSLE = MasterShelfLotAndExpiry::where('ETIN',$ms->ETIN)->where('address',$ms->address)->where('exp_date',$pa->exp_date)->where('lot',$pa->lot)->where('warehouse',$warehouse_id)->first();
            if($MSLE){
                $MSLE->qty = $MSLE->qty + $pa->quantity;
                $MSLE->save();
            }else{
                $MSLE = new MasterShelfLotAndExpiry;
                $MSLE->ETIN = $ms->ETIN;
                $MSLE->address = $ms->address;
                $MSLE->qty = $pa->quantity;
                $MSLE->exp_date = $pa->exp_date;
                $MSLE->lot = $pa->lot;
                $MSLE->warehouse = $warehouse_id;
                $MSLE->save();
            }
        }
        
        
    }

    // ,$CurrentMSLE
    public function TransferQty($request,$user_id,$check_destination_location,$destination_location_key,$current_location,$CurrentMSLE){

        if(isset($check_destination_location[$destination_location_key])){
            $destination_location = $check_destination_location[$destination_location_key];
            $transfer_to = MasterShelf::where('id',$destination_location['id'])->first();
            if($transfer_to){
                $starting_qty = $transfer_to->cur_qty;
                $ending_qty = $transfer_to->cur_qty + $request->current_qty;
                $transfer_to->cur_qty = $ending_qty;
                $transfer_to->save();

                if($request->lot != '' && $CurrentMSLE){
                    $MSLE = MasterShelfLotAndExpiry::where('ETIN',$transfer_to->ETIN)->where('address',$transfer_to->address)->where('lot',$request->lot)->first();
                    if($MSLE){
                        $MSLE->qty = $MSLE->qty + $request->current_qty;
                        $MSLE->save();
                    }else{
                        $MSLE = new MasterShelfLotAndExpiry;
                        $MSLE->ETIN = $transfer_to->ETIN;
                        $MSLE->address = $transfer_to->address;
                        $MSLE->qty = $request->current_qty;
                        $MSLE->exp_date = $CurrentMSLE->exp_date;
                        $MSLE->lot = $CurrentMSLE->lot;
                        $MSLE->save();
                    }
                }
                


                InventoryAdjustmentLog([
                    'ETIN' => $destination_location['ETIN'],
                    'location' => $destination_location['address'],
                    'starting_qty' => $starting_qty,
                    'ending_qty' => $ending_qty,
                    'total_change' => '+'.$request->current_qty,
                    'user' => $user_id,
                    'reference' => 'Inventory Transfer',
                    'reference_value' => json_encode($request->all()),
                    'reference_description' => 'Increment ETIN Qty from Inventory Transfer: transaferProductQTY'
                ]);
            }
        }else{
            $destination_location = $check_destination_location[0];
            $max_qty = $destination_location['max_qty'];
            if($destination_location['location_type_id'] == 1){
                if($qty > 100){
                    $max_qty = $qty;
                }else{
                    $max_qty = 100;
                }
            }
            
            MasterShelf::create([
                'aisle_id'    => $destination_location['aisle_id'],
                'bay_id' => $destination_location['bay_id'],
                'shelf' => $destination_location['shelf'],
                'slot'  => $destination_location['slot'],
                'ETIN'  => $destination_location['ETIN'],
                'max_qty' => $max_qty,
                'cur_qty' => $request->current_qty,
                'address' => $destination_location['address'],
                'location_type_id' => $destination_location['location_type_id'],
                'parent_id' => $destination_location['parent_id']
            ]);

            if($request->lot != '' && $CurrentMSLE){
                $MSLE = MasterShelfLotAndExpiry::where('ETIN',$destination_location['ETIN'])->where('address',$destination_location['address'])->where('lot',$request->lot)->first();
                if($MSLE){
                    $MSLE->qty = $MSLE->qty + $request->current_qty;
                    $MSLE->save();
                }else{
                    $MSLE = new MasterShelfLotAndExpiry;
                    $MSLE->ETIN = $destination_location['ETIN'];
                    $MSLE->address = $destination_location['address'];
                    $MSLE->qty = $request->current_qty;
                    $MSLE->exp_date = $CurrentMSLE->exp_date;
                    $MSLE->lot = $CurrentMSLE->lot;
                    $MSLE->save();
                }
            }


            InventoryAdjustmentLog([
                'ETIN' => $destination_location['ETIN'],
                'location' => $destination_location['address'],
                'starting_qty' => $starting_qty,
                'ending_qty' => $ending_qty,
                'total_change' => '+'.$request->current_qty,
                'user' => $user_id,
                'reference' => 'Inventory Transfer',
                'reference_value' => json_encode($request->all()),
                'reference_description' => 'Increment ETIN Qty from Inventory Transfer: transaferProductQTY'
            ]);

        }
        


        $transfer_from = MasterShelf::where('id',$current_location['id'])->first();
        if($transfer_from){
            $starting_qty = $transfer_from->cur_qty;
            $ending_qty = $transfer_from->cur_qty - $request->current_qty;
            $transfer_from->cur_qty = $ending_qty;
            $transfer_from->save();

            if($request->lot != '' && $CurrentMSLE){
                $CurrentMSLE->qty = $CurrentMSLE->qty - $request->current_qty;
                $CurrentMSLE->save();
            }

            InventoryAdjustmentLog([
                'ETIN' => $current_location['ETIN'],
                'location' => $current_location['address'],
                'starting_qty' => $starting_qty,
                'ending_qty' => $ending_qty,
                'total_change' => '-'.$request->current_qty,
                'user' => $user_id,
                'reference' => 'Inventory Transfer',
                'reference_value' => json_encode($request->all()),
                'reference_description' => 'Decrement ETIN Qty from Inventory Transfer: transaferProductQTY'
            ]);
        }

        $transferInventoryDetails = TransferInventoryDetails::create([
            'etin' => $request->current_upc,
            'current_upc' => $request->current_upc,
            'current_warehouse' => $request->current_warehouse,
            'current_location' => $request->current_location,
            'quantity' => $request->current_qty,
            'transfer_warehouse' => $request->transfer_warehouse,
            'transfer_location' => $request->transfer_location,
            'user_id' => $user_id,
            'lot' => $request->lot
        ]);


    }


    public function AddProductToQuorantineLocation($etin,$qty,$address,$user_id,$row_pro){
    
        $isItemExist = MasterShelf::where('ETIN',$etin)->where('address',$address)->where('location_type_id',7)->first();
        if($isItemExist){
            $cur_qty = $isItemExist->cur_qty;
            $isItemExist->cur_qty = $cur_qty + $qty;
            $isItemExist->save();

            if($row_pro['lot'] != ''){
                $MSLE = MasterShelfLotAndExpiry::where('ETIN',$isItemExist->ETIN)->where('address',$address)->where('lot',$row_pro['lot'])->first();
                if($MSLE){
                    $MSLE->qty = $MSLE->qty + $qty;
                    $MSLE->save();
                }else{
                    $MSLE = new MasterShelfLotAndExpiry;
                    $MSLE->ETIN = $isItemExist->ETIN;
                    $MSLE->address = $address;
                    $MSLE->qty = $qty;
                    $MSLE->exp_date = $row_pro['exp_date'];
                    $MSLE->lot = $row_pro['lot'];
                    $MSLE->save();
                }
            }


            InventoryAdjustmentLog([
                'ETIN' => $etin,
                'location' => $isItemExist->address,
                'starting_qty' => $cur_qty,
                'ending_qty' => $isItemExist->cur_qty,
                'total_change' => $qty,
                'user' => $user_id,
                'reference' => 'AddProductToQuorantineLocation',
                'reference_value' => json_encode($row_pro),
                'reference_description' => 'Update Current Qty: AddProductToQuorantineLocation'
            ]);
    
            UserLogs([
                'user_id' => $user_id,
                'action' => 'Click',
                'task' => 'Receiving',
                'details' => 'Item '.$etin.' Sattled to quorantine location '.$row_pro['bol_number'],
                'type' => 'CWMS',
                'bol_number' => $row_pro['bol_number']
            ]);
        }else{
            $address_db = MasterShelf::where('address',$address)->where('location_type_id',7)->first();
            if($address_db){
                // dd($address_db);
                if($address_db->ETIN == ''){
                    $address_db->ETIN = $etin;
                    $address_db->cur_qty = $qty;
                    $address_db->save();

                    if($row_pro['lot'] != ''){
                        $MSLE = MasterShelfLotAndExpiry::where('ETIN',$address_db->ETIN)->where('address',$address)->where('lot',$row_pro['lot'])->first();
                        if($MSLE){
                            $MSLE->qty = $MSLE->qty + $qty;
                            $MSLE->save();
                        }else{
                            $MSLE = new MasterShelfLotAndExpiry;
                            $MSLE->ETIN = $address_db->ETIN;
                            $MSLE->address = $address;
                            $MSLE->qty = $qty;
                            $MSLE->exp_date = $row_pro['exp_date'];
                            $MSLE->lot = $row_pro['lot'];
                            $MSLE->save();
                        }
                    }
                    

                    InventoryAdjustmentLog([
                        'ETIN' => $etin,
                        'location' => $address_db->address,
                        'starting_qty' => 0,
                        'ending_qty' => $qty,
                        'total_change' => $qty,
                        'user' => $user_id,
                        'reference' => 'AddProductToQuorantineLocation',
                        'reference_value' => json_encode($row_pro),
                        'reference_description' => 'Update Current Qty: AddProductToQuorantineLocation'
                    ]);
                    UserLogs([
                        'user_id' => $user_id,
                        'action' => 'Click',
                        'task' => 'Receiving',
                        'details' => 'Item '.$etin.' Sattled to quorantine location '.$row_pro['bol_number'],
                        'type' => 'CWMS',
                        'bol_number' => $row_pro['bol_number']
                    ]);
                }else{

                    MasterShelf::create([
                        'aisle_id'    => $address_db->aisle_id,
                        'bay_id' => $address_db->bay_id,
                        'shelf' => $address_db->shelf,
                        'slot'  => $address_db->slot,
                        'ETIN'  => $etin,
                        'max_qty' => $address_db->max_qty,
                        'cur_qty' => $qty,
                        'address' => $address,
                        'location_type_id' => $address_db->location_type_id,
                        'parent_id' => $address_db->parent_id
                    ]);

                    if($row_pro['lot'] != ''){
                        $MSLE = MasterShelfLotAndExpiry::where('ETIN',$etin)->where('address',$address)->where('lot',$row_pro['lot'])->first();
                        if($MSLE){
                            $MSLE->qty = $MSLE->qty + $qty;
                            $MSLE->save();
                        }else{
                            $MSLE = new MasterShelfLotAndExpiry;
                            $MSLE->ETIN = $etin;
                            $MSLE->address = $address;
                            $MSLE->qty = $qty;
                            $MSLE->exp_date = $row_pro['exp_date'];
                            $MSLE->lot = $row_pro['lot'];
                            $MSLE->save();
                        }
                    }

                    InventoryAdjustmentLog([
                        'ETIN' => $etin,
                        'location' => $address_db->address,
                        'starting_qty' => 0,
                        'ending_qty' => $qty,
                        'total_change' => $qty,
                        'user' => $user_id,
                        'reference' => 'AddProductToQuorantineLocation',
                        'reference_value' => json_encode($row_pro),
                        'reference_description' => 'Insert Current Qty: AddProductToQuorantineLocation'
                    ]);
                    UserLogs([
                        'user_id' => $user_id,
                        'action' => 'Click',
                        'task' => 'Receiving',
                        'details' => 'Item '.$etin.' Sattled to quorantine location '.$row_pro['bol_number'],
                        'type' => 'CWMS',
                        'bol_number' => $row_pro['bol_number']
                    ]);
                }
                
            }
        }
        
    }

    public function RestockQty($input){
        $transfer_to = MasterShelf::where('ETIN',$input['ETIN'])->where('address',$input['to_location'])->first();
        if($transfer_to){
            $starting_qty = $transfer_to->cur_qty;
            $ending_qty = $transfer_to->cur_qty + $input['quantity'];
            $transfer_to->cur_qty = $ending_qty;
            $transfer_to->save();

            if($input['lot'] != ''){
                $CurrentMSLE = MasterShelfLotAndExpiry::where('ETIN',$transfer_to->ETIN)->where('address',$input['from_location'])->where('lot',$input['lot'])->first();
                $MSLE = MasterShelfLotAndExpiry::where('ETIN',$transfer_to->ETIN)->where('address',$transfer_to->address)->where('lot',$input['lot'])->first();
                if($MSLE){
                    $MSLE->qty = $MSLE->qty + $input['quantity'];
                    $MSLE->save();
                }else{
                    $MSLE = new MasterShelfLotAndExpiry;
                    $MSLE->ETIN = $transfer_to->ETIN;
                    $MSLE->address = $transfer_to->address;
                    $MSLE->qty = $input['quantity'];
                    $MSLE->exp_date = $CurrentMSLE->exp_date;
                    $MSLE->lot = $CurrentMSLE->lot;
                    $MSLE->save();
                }
            }
            


            InventoryAdjustmentLog([
                'ETIN' => $input['ETIN'],
                'location' => $input['to_location'],
                'starting_qty' => $starting_qty,
                'ending_qty' => $ending_qty,
                'total_change' => '+'.$input['quantity'],
                'user' => $input['user_id'],
                'reference' => 'Restock',
                'reference_value' => json_encode($input),
                'reference_description' => 'Increment ETIN Qty from editItem: RestockQty'
            ]);
        }

        $transfer_from = MasterShelf::where('ETIN',$input['ETIN'])->where('address',$input['from_location'])->first();
        if($transfer_from){
            $starting_qty = $transfer_from->cur_qty;
            $ending_qty = $transfer_from->cur_qty - $input['quantity'];
            $transfer_from->cur_qty = $ending_qty;
            $transfer_from->save();

            if($input['lot'] != ''){
                $MSLE = MasterShelfLotAndExpiry::where('ETIN',$transfer_from->ETIN)->where('address',$transfer_from->address)->where('lot',$input['lot'])->first();
                if($MSLE){
                    $MSLE->qty = $MSLE->qty - $input['quantity'];
                    $MSLE->save();
                }
            }
            


            InventoryAdjustmentLog([
                'ETIN' => $input['ETIN'],
                'location' => $input['from_location'],
                'starting_qty' => $starting_qty,
                'ending_qty' => $ending_qty,
                'total_change' => '+'.$input['quantity'],
                'user' => $input['user_id'],
                'reference' => 'Restock',
                'reference_value' => json_encode($input),
                'reference_description' => 'Decrement ETIN Qty from editItem: RestockQty'
            ]);
        }
    }

    public function GetMasterShelpLotAndExp($input){
        $ETIN = isset($input['ETIN']) ? $input['ETIN'] : NULL;
        $address = isset($input['address']) ? $input['address'] : NULL;
        if($ETIN != '' && $address != ''){
            $MSLE = MasterShelfLotAndExpiry::where('ETIN',$ETIN)->where('address',$address)->where('qty','>',0)->orderBy('exp_date','ASC')->get();
            return $MSLE;
        }

        return [];

    }

    public function PickExpANdLot($input){
        $id = isset($input['id']) ? $input['id'] : NULL;
        $qty = isset($input['qty']) ? $input['qty'] : NULL;
        $sub_order = isset($input['sub_order']) ? $input['sub_order'] : NULL;
        $Main_ETIN = isset($input['Main_ETIN']) ? $input['Main_ETIN'] : NULL;
        


        $MasterShelf = MasterShelf::find($id);
        if($id){
            $address = $MasterShelf->address;
            $ETIN = $MasterShelf->ETIN;

            $MSLE = MasterShelfLotAndExpiry::where('ETIN',$ETIN)->where('address',$address)->where('qty','>',0)->orderBy('exp_date','ASC')->get();
            if($MSLE){
                foreach($MSLE as $ROWMSLE){
                    $exp_qty = $ROWMSLE->qty;
                    if($qty > 0){
                        if($qty >= $exp_qty){
                            $PLAE = new PickedLotAndExp;
                            $PLAE->sub_order = $sub_order;
                            $PLAE->ETIN = $ETIN;
                            $PLAE->master_shelf_id = $ROWMSLE->id;
                            $PLAE->address = $address;
                            $PLAE->qty = $exp_qty;
                            $PLAE->lot = $ROWMSLE->lot;
                            $PLAE->exp = $ROWMSLE->exp_date;
                            $PLAE->Main_ETIN = $Main_ETIN;
                            $PLAE->save();

                            $PLAE = new PickedLotAndExp;
                            $PLAE->sub_order = $sub_order;
                            $PLAE->ETIN = $ETIN;
                            $PLAE->master_shelf_id = $ROWMSLE->id;
                            $PLAE->address = $address;
                            $PLAE->qty = $exp_qty;
                            $PLAE->lot = $ROWMSLE->lot;
                            $PLAE->exp = $ROWMSLE->exp_date;
                            $PLAE->Main_ETIN = $Main_ETIN;
                            $PLAE->for_the_log = 1;
                            $PLAE->save();


                            $ROWMSLE->qty = $ROWMSLE->qty - $exp_qty;
                            $ROWMSLE->save();
                            $qty = $qty - $exp_qty;
                        }else if($qty < $exp_qty){
                            $PLAE = new PickedLotAndExp;
                            $PLAE->sub_order = $sub_order;
                            $PLAE->ETIN = $ETIN;
                            $PLAE->master_shelf_id = $ROWMSLE->id;
                            $PLAE->address = $address;
                            $PLAE->qty = $qty;
                            $PLAE->lot = $ROWMSLE->lot;
                            $PLAE->exp = $ROWMSLE->exp_date;
                            $PLAE->Main_ETIN = $Main_ETIN;
                            $PLAE->save();

                            $PLAE = new PickedLotAndExp;
                            $PLAE->sub_order = $sub_order;
                            $PLAE->ETIN = $ETIN;
                            $PLAE->master_shelf_id = $ROWMSLE->id;
                            $PLAE->address = $address;
                            $PLAE->qty = $qty;
                            $PLAE->lot = $ROWMSLE->lot;
                            $PLAE->exp = $ROWMSLE->exp_date;
                            $PLAE->Main_ETIN = $Main_ETIN;
                            $PLAE->for_the_log = 1;
                            $PLAE->save();

                            $ROWMSLE->qty = $ROWMSLE->qty - $qty;
                            $ROWMSLE->save();
                            $qty = $qty - $qty;
                        }
                    }
                }
            }
        }
    }

    public function PickExpAndLotWithParent($input,$sub_order,$Main_ETIN,$pick_qty){
        $Parent = isset($input[0]) ? $input[0] : [];
        $Backstock = (isset($input[1]['backstock']) && $input[1]['backstock'] == 1) ? $input[1] : [];
        $Child = (count($Backstock) > 0) ? $input[2] : $input[1];


        $unit_in_pack = $Parent['unit_in_pack'];
        $chid_qty = $Child['qty'];
        $qty_from_parent = $unit_in_pack - $chid_qty;

        $total_deducted_qty = 0;
        $MasterShelfChild = MasterShelf::find($Child['id']);
        if($MasterShelfChild){
            $address = $MasterShelfChild->address;
            $ETIN = $MasterShelfChild->ETIN;
            $MSLEChild = MasterShelfLotAndExpiry::where('ETIN',$ETIN)->where('address',$address)->where('qty','>',0)->orderBy('exp_date','ASC')->get();
            if($MSLEChild){
                foreach($MSLEChild as $ROWMSLEChild){
                    $exp_qty = $ROWMSLEChild->qty;
                    $PLAE = new PickedLotAndExp;
                    $PLAE->sub_order = $sub_order;
                    $PLAE->ETIN = $ETIN;
                    $PLAE->master_shelf_id = $ROWMSLEChild->id;
                    $PLAE->address = $address;
                    $PLAE->qty = $exp_qty;
                    $PLAE->lot = $ROWMSLEChild->lot;
                    $PLAE->exp = $ROWMSLEChild->exp_date;
                    $PLAE->Main_ETIN = $Main_ETIN;
                    $PLAE->save();

                    $PLAE = new PickedLotAndExp;
                    $PLAE->sub_order = $sub_order;
                    $PLAE->ETIN = $ETIN;
                    $PLAE->master_shelf_id = $ROWMSLEChild->id;
                    $PLAE->address = $address;
                    $PLAE->qty = $exp_qty;
                    $PLAE->lot = $ROWMSLEChild->lot;
                    $PLAE->exp = $ROWMSLEChild->exp_date;
                    $PLAE->Main_ETIN = $Main_ETIN;
                    $PLAE->for_the_log = 1;
                    $PLAE->save();


                    $ROWMSLEChild->qty = $ROWMSLEChild->qty - $exp_qty;
                    $ROWMSLEChild->save();
                    $total_deducted_qty = $total_deducted_qty + $exp_qty;
                }
            }

        }

        $MasterShelfParent = MasterShelf::find($Parent['id']);
        if($MasterShelfParent){
            $qty = $Parent['qty'];
            $address = $MasterShelfParent->address;
            $ETIN = $MasterShelfParent->ETIN;
            $MSLEParent = MasterShelfLotAndExpiry::where('ETIN',$ETIN)->where('address',$address)->where('qty','>',0)->orderBy('exp_date','ASC')->get();
            $LastLotInfo = NULL;
            if($MSLEParent){
                foreach($MSLEParent as $ROWMSLEParent){
                    $exp_qty = $ROWMSLEParent->qty;
                    $total_deducted_qty = $total_deducted_qty + ($exp_qty * $Parent['unit_in_pack']);
                    if($qty > 0){
                        if($qty >= $exp_qty){
                            $PLAE = new PickedLotAndExp;
                            $PLAE->sub_order = $sub_order;
                            $PLAE->ETIN = $ETIN;
                            $PLAE->master_shelf_id = $ROWMSLEParent->id;
                            $PLAE->address = $address;
                            $PLAE->qty = $total_deducted_qty > $pick_qty ? $qty_from_parent : ($exp_qty * $Parent['unit_in_pack']);
                            $PLAE->lot = $ROWMSLEParent->lot;
                            $PLAE->exp = $ROWMSLEParent->exp_date;
                            $PLAE->Main_ETIN = $Main_ETIN;
                            $PLAE->unit_in_pack = $Parent['unit_in_pack'];
                            $PLAE->save();

                            $PLAE = new PickedLotAndExp;
                            $PLAE->sub_order = $sub_order;
                            $PLAE->ETIN = $ETIN;
                            $PLAE->master_shelf_id = $ROWMSLEParent->id;
                            $PLAE->address = $address;
                            $PLAE->qty = $exp_qty;
                            $PLAE->lot = $ROWMSLEParent->lot;
                            $PLAE->exp = $ROWMSLEParent->exp_date;
                            $PLAE->Main_ETIN = $Main_ETIN;
                            $PLAE->unit_in_pack = $Parent['unit_in_pack'];
                            $PLAE->for_the_log = 1;
                            $PLAE->save();


                            $ROWMSLEParent->qty = $ROWMSLEParent->qty - $exp_qty;
                            $ROWMSLEParent->save();
                            $qty = $qty - $exp_qty;
                        }else if($qty < $exp_qty){
                            $PLAE = new PickedLotAndExp;
                            $PLAE->sub_order = $sub_order;
                            $PLAE->ETIN = $ETIN;
                            $PLAE->master_shelf_id = $ROWMSLEParent->id;
                            $PLAE->address = $address;
                            $PLAE->qty = $total_deducted_qty > $pick_qty ? $qty_from_parent : ($qty * $Parent['unit_in_pack']);
                            $PLAE->lot = $ROWMSLEParent->lot;
                            $PLAE->exp = $ROWMSLEParent->exp_date;
                            $PLAE->Main_ETIN = $Main_ETIN;
                            $PLAE->unit_in_pack = $Parent['unit_in_pack'];
                            $PLAE->save();

                            $PLAE = new PickedLotAndExp;
                            $PLAE->sub_order = $sub_order;
                            $PLAE->ETIN = $ETIN;
                            $PLAE->master_shelf_id = $ROWMSLEParent->id;
                            $PLAE->address = $address;
                            $PLAE->qty = $qty;
                            $PLAE->lot = $ROWMSLEParent->lot;
                            $PLAE->exp = $ROWMSLEParent->exp_date;
                            $PLAE->Main_ETIN = $Main_ETIN;
                            $PLAE->unit_in_pack = $Parent['unit_in_pack'];
                            $PLAE->for_the_log = 1;
                            $PLAE->save();


                            $ROWMSLEParent->qty = $ROWMSLEParent->qty - $qty;
                            $ROWMSLEParent->save();
                            $qty = $qty - $qty;
                            
                        }

                        $LastLotInfo = $ROWMSLEParent;
                    }
                }
            }

            if($LastLotInfo != NULL && $Child['qty'] > 0){
                $MasterShelfLotAndExpiry = new MasterShelfLotAndExpiry;
                $MasterShelfLotAndExpiry->warehouse = $LastLotInfo->warehouse;
                $MasterShelfLotAndExpiry->ETIN = $MasterShelfChild->ETIN;
                $MasterShelfLotAndExpiry->address = $LastLotInfo->address;
                $MasterShelfLotAndExpiry->qty = $Child['qty'];
                $MasterShelfLotAndExpiry->exp_date = $LastLotInfo->exp_date;
                $MasterShelfLotAndExpiry->lot = $LastLotInfo->lot;
                $MasterShelfLotAndExpiry->save();

                $PLAE = new PickedLotAndExp;
                $PLAE->sub_order = $sub_order;
                $PLAE->ETIN = $MasterShelfLotAndExpiry->ETIN;
                $PLAE->master_shelf_id = $MasterShelfLotAndExpiry->id;
                $PLAE->address = $MasterShelfLotAndExpiry->address;
                $PLAE->qty = $MasterShelfLotAndExpiry->qty;
                $PLAE->lot = $MasterShelfLotAndExpiry->lot;
                $PLAE->exp = $MasterShelfLotAndExpiry->exp_date;
                $PLAE->Main_ETIN = $Main_ETIN;
                $PLAE->FromTheParent = 1;
                $PLAE->for_the_log = 1;
                $PLAE->save();
            }
        }



    }

}
