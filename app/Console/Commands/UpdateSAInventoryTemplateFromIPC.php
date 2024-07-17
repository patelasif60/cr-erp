<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Jobs\GetItemBySKU;
use Log;
class UpdateSAInventoryTemplateFromIPC extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:Update_SA_Inventory_Template_From_IPC';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Item detail By SKU';

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
        Log::channel('UpdateSAInventoryTemplateFromIPC')->info('Starting Cron for IPC inventory');
        $get_sku = DB::table('sa_inventory')->where('inventory_data_from','IPC')->get();
        Log::channel('UpdateSAInventoryTemplateFromIPC')->info('Get All Data From sa_inventory table marked With IPC');
        if($get_sku){
            foreach($get_sku as $row_sku){
                try{
                    GetItemBySKU::dispatch($row_sku);
                    Log::channel('UpdateSAInventoryTemplateFromIPC')->info('Job Created For SKU checking and Inventory Update',['e' => json_encode($row_sku)]);
                }catch(\throwable $e){
                    Log::channel('UpdateSAInventoryTemplateFromIPC')->info('Job creation failed For: ',[
                        'e' => json_encode($e->getMessage()),
                        'row' => json_encode($row_sku)
                    ]);
                }
            }
        }

        return 0;
    }
}
