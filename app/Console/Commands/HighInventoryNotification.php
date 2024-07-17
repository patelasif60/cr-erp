<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\MasterShelf;
use App\AisleMaster;
use App\User;
use App\MasterProduct;
use App\Repositories\NotificationRepository;
use DB;


class HighInventoryNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:high_inventory_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Notification if a product is High';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(NotificationRepository $NotificationRepository)
    {
        parent::__construct();
        $this->NotificationRepository = $NotificationRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $products = MasterShelf::select('*',DB::raw('(cur_qty/max_qty)*100 as inv'))->whereRaw('(cur_qty/max_qty)*100 >= 80')->get();
        foreach($products as $product){
            $warehouse_name = $product->ailse->warehouse_name->warehouses;
            $address = $product->address;
            $location = $product->location_type->type;
            $note = $product->ETIN." is High of Stock for ".$warehouse_name." warehouse ".$location." location";
            $url_id = '';
            $master_product = $product->product;
            if(isset($master_product->id)){
                $url_id = $master_product->id;
            }
            $url = '/editmasterproduct/'.$url_id;
            $type = "High Stock Product";
            $this->NotificationRepository->SendInventoryNotification([
                'subject' => $type,
                'body' => $note,
                'url' => $url,
                'notification_type' => 'inventory_low_stock'
            ]);

            
        }
        return 0;
    }
}
