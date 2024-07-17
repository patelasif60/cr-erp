<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Excel;
use App\DotRlGl;
use Log;
class JobProcessRLGLImport implements ShouldQueue
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
            
            $check_if_gtin_exist = DotRlGl::where('item_num',$row['item_number'])->first();
            if($check_if_gtin_exist){
                $DotRlGl = DotRlGl::find($check_if_gtin_exist->id);
                $status = 'Updated';
            }else{
                $DotRlGl = new DotRlGl();
                $status = 'Saved';
            }
            
            $DotRlGl->dot_cust_num = $row['dot_customer_number'];
            $DotRlGl->dot_cust_shipto = $row['dot_customer_shipto'];
            $DotRlGl->gtin = $row['gtin_number'];
            $DotRlGl->cust_item_num = $row['customer_item_number'];
            $DotRlGl->item_num = $row['item_number'];
            $DotRlGl->mfg_number = $row['mfg_number'];
            $DotRlGl->unabbreviated_desc = $row['unabbreviated_desc'];
            $DotRlGl->supplier_name = $row['supplier_name'];
            $DotRlGl->temp = $row['temperature'];
            $DotRlGl->illinois_inventory_status = $row['illinois_inventory_status'];
            $DotRlGl->maryland_inventory_status = $row['maryland_inventory_status'];
            $DotRlGl->modesto_inventory_status = $row['modesto_inventory_status'];
            $DotRlGl->oklahoma_inventory_status = $row['oklahoma_inventory_status'];
            $DotRlGl->burley_inventory_status = $row['burley_inventory_status'];
            $DotRlGl->arizona_inventory_status = $row['arizona_inventory_status'];
            $DotRlGl->stock_eta_date = $row['stock_eta_date'];
            $DotRlGl->save();
            Log::channel('DotRLGLImport')->info('DOTRLGL Data has been '.$status.': ',['row' => json_encode($row)]);
        } catch (\throwable $e) {
            Log::channel('DotRLGLImport')->info('Erro Encountered While Importing: ', [
                'e' => json_encode($e->getMessage()),
            ]);
        }
        
    }
}
