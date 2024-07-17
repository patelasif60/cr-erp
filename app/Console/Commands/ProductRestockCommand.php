<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\MasterShelf;
use App\Jobs\ProductRestockJob;
use App\Settings;


class ProductRestockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:generate_product_restock_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This report will generate Restock Report';

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
        $result_obj = MasterShelf::with(['backstock_location' => function($bl){
            $bl->where("location_type_id", 2);
        }, 'product', 'ailse','backstock_location.ailse'])
        ->where('location_type_id', 1)
        ->whereHas("backstock_location", function($subQuery){ 
            $subQuery->where("location_type_id", 2);
            $subQuery->where('cur_qty', '>', 0);
        })->whereColumn('max_qty', '<>', 'cur_qty')
        ->where('ETIN', 'ETFZ-1001-6726');
        
        // $qry = str_replace(array('%', '?'), array('%%', '%s'), $result_obj->toSql());
        // $qry = vsprintf($qry, $result_obj->getBindings());
        // dd($qry);
        $products = $result_obj->get();
        // dd($products->toArray());
        $settings = Settings::first();
        if($products){
            foreach($products as $row_pro){
                ProductRestockJob::dispatch($row_pro,$settings);
            }
        }
    }
}
