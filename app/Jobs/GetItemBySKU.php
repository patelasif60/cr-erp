<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DB;
use Log;
use App\MasterShelf;

class GetItemBySKU implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $row;
    public function __construct($row)
    {
        $this->row = $row;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $row = $this->row;
            $lobId = 11523;

            if($row->lob == 'EI'){
                $lobId = 11523; 
            }
            else if($row->lob == 'Rebel'){
                $lobId = 11769; 
            }
            else if($row->lob == 'RGF'){
                $lobId = 11770; 
            }
            else if($row->lob == 'Oatly'){
                $lobId = 14468; 
            }
            else if($row->lob == 'Mars Ice Cream Samples'){
                $lobId = 14420; 
            }
            else if($row->lob == "Bernatello's"){
                $lobId = 11920; 
            }
            else if($row->lob == 'Frozen Farmer'){
                $lobId = 14575; 
            }
            else if($row->lob == 'Serendipity'){
                $lobId = 14237; 
            }
            else if($row->lob == 'Ruby Jewel'){
                $lobId = 13088; 
            }

            Log::channel('UpdateSAInventoryTemplateFromIPC')->info('SKU('.$this->row->sku.')' );
            Log::channel('UpdateSAInventoryTemplateFromIPC')->info('LOB ('.$lobId.')' );

            $curl = curl_init();
            $sku = $this->row->sku;
            $url = 'https://etailerinc.infopluswms.com/infoplus-wms/api/beta/item/getBySKU?lobId='.$lobId.'&sku='.$sku;

            Log::channel('UpdateSAInventoryTemplateFromIPC')->info('URL ('.$url.')' );

            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'API-Key: 680E23BD00F354E16892C2A351E2B42BBD60BB3824E7DC2C8B553F777B0A9D72'
            ),
            ));

            $response = curl_exec($curl);
            
            curl_close($curl);
            $data = json_decode($response);
            Log::channel('UpdateSAInventoryTemplateFromIPC')->info('SKU('.$this->row->sku.') Orderable Quantity- '.$data->orderableQuantity);
            
            $avg = 0;
            if(isset($data->orderableQuantity) && $data->orderableQuantity > 0){
                $avg = $data->orderableQuantity * 0.9;
                $avg = round($avg);
            }

            // $count = 0;

            // $master_shelfs = MasterShelf::where('ETIN', $row->sku)->where('location_type_id', 1)->get();

            // if(count($master_shelfs) > 0){
            //     foreach($master_shelfs as $master_shelf){
            //         $count += $master_shelf->cur_qty;
            //     }
            // }
            

            $check_sku_in_sa_inventory_temp = DB::table('sa_inventory_tempate')->where('sku',$row->sku)->first();
            Log::channel('UpdateSAInventoryTemplateFromIPC')->info('Checked SKU('.$row->sku.') in sa_inventory_tempate');
            if($check_sku_in_sa_inventory_temp){
                Log::channel('UpdateSAInventoryTemplateFromIPC')->info('SKU('.$row->sku.') found in sa_inventory_tempate');

                DB::table('sa_inventory_tempate')->where('sku',$row->sku)->update([
                    'on_hand_quantity' => $avg,
                    'warehouse_code' => '1795',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                Log::channel('UpdateSAInventoryTemplateFromIPC')->info('SKU('.$row->sku.') ONHand Updated in sa_inventory_tempate');
            }else{
                Log::channel('UpdateSAInventoryTemplateFromIPC')->info('SKU('.$row->sku.') not found in sa_inventory_tempate');
                DB::table('sa_inventory_tempate')->insert([
                    'sku' => $row->sku,
                    'warehouse_code' => $row->warehouse_code,
                    'on_hand_quantity' => $avg,
                    'warehouse_code' => '1795',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                Log::channel('UpdateSAInventoryTemplateFromIPC')->info('SKU('.$row->sku.') Inserted IN in sa_inventory_tempate',['sku' => $row->sku,
                'warehouse_code' => $row->warehouse_code]);
            }
            
        } catch (\throwable $e) {
            Log::channel('UpdateSAInventoryTemplateFromIPC')->info('sa_inventory_tempate updation has some error',[
                'e' => json_encode($e->getMessage()),
            ]);
        }
        
    }
}
