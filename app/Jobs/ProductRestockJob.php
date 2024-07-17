<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DB;
use Log;
use App\OrderDetail;
use App\ProductRestock;

class ProductRestockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $row,$settings;
    public function __construct($row,$settings)
    {
        $this->row = $row;
        $this->settings = $settings;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $settings = $this->settings;
            $row_pro = $this->row;
            $max_qty = $row_pro->max_qty;
            $cur_qty = $row_pro->cur_qty;
            $status = NULL;
            $product = $row_pro->product;
            $warehouse_id = NULL;
            $ailse = $row_pro->ailse;
            if(isset($ailse->warehouse_id)) $warehouse_id = $ailse->warehouse_id;
            if($warehouse_id == ''){
                return false;
            }
            $backstock_location = NULL;
            if($row_pro->backstock_location){
                foreach($row_pro->backstock_location as $row_BL){
                    $B_Ailse = $row_BL->ailse;
                    $BWarehouse = NULL;
                    if(isset($B_Ailse->warehouse_id)){
                        $BWarehouse = $B_Ailse->warehouse_id;
                    }
                    if($row_BL->location_type_id == 2 && $BWarehouse == $warehouse_id){
                        $backstock_location = $row_BL;
                        break;
                    }
                }
            }

            if($backstock_location === NULL){
                return false;
            }
            
            
            if($cur_qty == '' || $cur_qty == 0){
                $status = 1;
            }

            if($status == null){
                $items_needed = OrderDetail::where('ETIN',$row_pro->ETIN)->whereIN('status',[1,2,7,9,10])->sum('quantity_fulfilled');
                if($cur_qty < $items_needed){
                    $status = 2;
                }
            }

            if($status == null){
                $qty_per = ($cur_qty/$max_qty) * 100;
                if($qty_per >= $settings->high_min && $qty_per <= $settings->high_max){
                    $status = 2;
                }elseif($qty_per >= $settings->mid_min && $qty_per <= $settings->mid_max){
                    $status = 3;
                }else{
                    $status = 4;
                }
            }

            $row_pro->priority = $status;
            $row_pro->priority_name = $status !== null ? config('caranium.INV_Priority')[$status]: NULL;

            if($product){
                $PR = ProductRestock::where('ETIN', $row_pro->ETIN)
                    ->where('backstock_location', $backstock_location->address)
                    ->where('pick_location', $row_pro->address)
                    ->first();

                if ($PR) {
                    $ProductRestock = $PR;
                }else{
                    $ProductRestock = new ProductRestock();
                }
                $ProductRestock->ETIN = $row_pro->ETIN;
                $ProductRestock->product_listing_name = $product->product_listing_name;
                if($product->supplier_type == 'client'){
                    $ProductRestock->client_supplier = clientName($product->client_supplier_id);
                }else{
                    $ProductRestock->client_supplier = SupplierName($product->client_supplier_id);
                }
                $ProductRestock->supplier_type = $product->supplier_type;
                $ProductRestock->client_supplier_id = $product->client_supplier_id;
                $ProductRestock->upc = $product->upc;
                $ProductRestock->pick_location = $row_pro->address;
                $ProductRestock->backstock_location = $backstock_location->address;
                $ProductRestock->pallet_id = $row_pro->pallet_number;
                $ProductRestock->qty_to_restock = ($max_qty - $cur_qty);
                $ProductRestock->priority = $status;
                $ProductRestock->priority_name = $status !== null ? config('caranium.INV_Priority')[$status]: NULL;
                $ProductRestock->warehouse = $warehouse_id;
                $ProductRestock->from_id = $backstock_location->id;
                $ProductRestock->to_id = $row_pro->id;
                $ProductRestock->save();
            }
        } catch (\throwable $e) {
            dump($e);
        }
        
    }
}
