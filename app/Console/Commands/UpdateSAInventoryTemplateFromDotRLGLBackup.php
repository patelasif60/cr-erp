<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdateInventoryDotRLGL;
use DB;
use Log;
class UpdateSAInventoryTemplateFromDotRLGLBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:Update_SA_Inventory_Template_From_DotRLGL';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare SA inventory template data with SA inventory and DOT RLGL and update on_hand_qty based on conditions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::channel('UpdateSAInventoryTemplateFromDotRLGL')->info('Starting Cron for DotRLGL inventory');
        $get_sku = DB::table('sa_inventory')->where('inventory_data_from','Dot RLGL')->get();
        Log::channel('UpdateSAInventoryTemplateFromDotRLGL')->info('Get All Data From sa_inventory table marked With DotRLGL');
        if($get_sku){
            foreach($get_sku as $row_sku){
                try{
                    UpdateInventoryDotRLGL::dispatch($row_sku);
                    Log::channel('UpdateSAInventoryTemplateFromDotRLGL')->info('Job Created For SKU checking and Inventory Update',['e' => json_encode($row_sku)]);
                }catch(\Exception $e){
                    Log::channel('UpdateSAInventoryTemplateFromDotRLGL')->info('Job creation failed For: ',[
                        'e' => json_encode($e->getMessage()),
                        'row' => json_encode($row_sku)
                    ]);
                }
            }
        }
       
        return 0;
    }
}
