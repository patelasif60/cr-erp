<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DB;
use Log;
use Artisan;
class UpdateInventoryDotRLGL implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $get_sku;
    public function __construct($get_sku)
    {
        $this->get_sku = $get_sku;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            DB::table('sa_inventory_tempate')->truncate();
            $get_sku = $this->get_sku;
            foreach($get_sku as $row){
                $check_in_dot_rlgl = DB::table('dot_rlgl')->where('item_num',$row->dot_id)->first();
                Log::channel('UpdateSAInventoryTemplateFromDotRLGL')->info('Checked dot_id('.$row->dot_id.') in dot_rlgl');
                if($check_in_dot_rlgl){
                    Log::channel('UpdateSAInventoryTemplateFromDotRLGL')->info('dot_id('.$row->dot_id.') found in dot_rlgl');
                    $status = $check_in_dot_rlgl->illinois_inventory_status;
                    if($status == 'IN STOCK'){
                        $onhand = 5;
                    }else{
                        $onhand = 0;
                    }
                    Log::channel('UpdateSAInventoryTemplateFromDotRLGL')->info('dot_id('.$row->dot_id.') illinois_inventory_status status is: '.$status);
                    // $check_sku_in_sa_inventory_temp = DB::table('sa_inventory_tempate')->where('sku',$row->sku)->first();
                    // Log::channel('UpdateSAInventoryTemplateFromDotRLGL')->info('Checked sku('.$row->sku.') in sa_inventory_tempate');
                    // if($check_sku_in_sa_inventory_temp){
                    //     Log::channel('UpdateSAInventoryTemplateFromDotRLGL')->info('sku('.$row->sku.') found in sa_inventory_tempate');
                    //     DB::table('sa_inventory_tempate')->where('sku',$row->sku)->update([
                    //         'on_hand_quantity' => $onhand,
                    //         'warehouse_code' => '1795'
                    //     ]);
                    //     Log::channel('UpdateSAInventoryTemplateFromDotRLGL')->info('sku('.$row->sku.') Updated in sa_inventory_tempate with onhand: '.$onhand);
                    // }else{
                        Log::channel('UpdateSAInventoryTemplateFromDotRLGL')->info('sku('.$row->sku.') not found in sa_inventory_tempate');
                        DB::table('sa_inventory_tempate')->insert([
                            'sku' => $row->sku,
                            'warehouse_code' => $row->warehouse_code,
                            'on_hand_quantity' => $onhand,
                            'warehouse_code' => '1795'
                        ]); 
                        Log::channel('UpdateSAInventoryTemplateFromDotRLGL')->info('sku('.$row->sku.') inserted in sa_inventory_tempate',[
                            'sku' => $row->sku,
                            'warehouse_code' => $row->warehouse_code,
                            'on_hand_quantity' => $onhand
                        ]);
                    // }
                }
            }
            Artisan::call('command:export_sa_inventory');
            DB::table('sa_inventory_tempate')->truncate();
        } catch (\throwable $e) {
            Log::channel('UpdateSAInventoryTemplateFromDotRLGL')->info('sa_inventory_tempate updation has some error',[
                'e' => json_encode($e->getMessage()),
            ]);
        }
        
    }
}
