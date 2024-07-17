<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductInventoryService;
use App\MasterShelf;
use App\InventorySummery;
use DB;

class GenerateInventoryReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update_inventory_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will run all the calculations from different tables and update inventory table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ProductInventoryService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $results = MasterShelf::whereNotNull('ETIN')->groupBy('ETIN')->get();
        if($results){
            foreach($results as $row){
                $mstPro = DB::table('master_product')->where('ETIN',$row->ETIN)->first();
                $WI_qty = $this->service->masterShelfSum(1,$row->ETIN);
                $PA_qty = $this->service->masterShelfSum(2,$row->ETIN);
                $NV_qty = $this->service->masterShelfSum(3,$row->ETIN);
                $OKC_qty = $this->service->masterShelfSum(4,$row->ETIN);
                $wi_each_qty = 0;
                if($mstPro){
                    $wi_each_qty = $mstPro->unit_in_pack * $mstPro->pack_form_count * $WI_qty;
                }

                $pa_each_qty = 0;
                if($mstPro){
                    $pa_each_qty = $mstPro->unit_in_pack * $mstPro->pack_form_count * $PA_qty;
                }

                $nv_each_qty = 0;
                if($mstPro){
                    $nv_each_qty = $mstPro->unit_in_pack * $mstPro->pack_form_count * $NV_qty;
                }

                $okc_each_qty = 0;
                if($mstPro){
                    $okc_each_qty = $mstPro->unit_in_pack * $mstPro->pack_form_count * $OKC_qty;
                }

                $wi_open_order_qty = $this->service->OpenOrderQty('WI',$row->ETIN);
                $pa_open_order_qty = $this->service->OpenOrderQty('PA',$row->ETIN);
                $nv_open_order_qty = $this->service->OpenOrderQty('NV',$row->ETIN);
                $okc_open_order_qty = $this->service->OpenOrderQty('OKC',$row->ETIN);

                $wi_orderable_qty = $this->service->OrderableQty('WI',$row->ETIN,$wi_open_order_qty);
                $pa_orderable_qty = $this->service->OrderableQty('PA',$row->ETIN,$pa_open_order_qty);
                $nv_orderable_qty = $this->service->OrderableQty('NV',$row->ETIN,$nv_open_order_qty);
                $okc_orderable_qty = $this->service->OrderableQty('OKC',$row->ETIN,$okc_open_order_qty);


                $wi_fulfilled_qty = $this->service->fulfilledQty(1,$row->ETIN);
                $pa_fulfilled_qty = $this->service->fulfilledQty(2,$row->ETIN);
                $nv_fulfilled_qty = $this->service->fulfilledQty(3,$row->ETIN);
                $okc_fulfilled_qty = $this->service->fulfilledQty(4,$row->ETIN);

                $parent_ETIN = NULL;
                if(isset($mstPro->parent_ETIN)){
                    $parent_ETIN = $mstPro->parent_ETIN?$mstPro->parent_ETIN:NULL;
                }

                InventorySummery::updateOrCreate(['ETIN' => $row->ETIN],[
                    'ETIN' => $row->ETIN,
                    'parent_ETIN'  => $parent_ETIN,
                    'wi_qty' => $WI_qty,
                    'wi_each_qty' => $wi_each_qty,
                    'wi_orderable_qty' => $wi_orderable_qty,
                    'wi_fulfilled_qty' => $wi_fulfilled_qty,
                    'wi_open_order_qty' => $wi_open_order_qty,
                    'pa_qty' => $PA_qty,
                    'pa_each_qty' => $pa_each_qty,
                    'pa_orderable_qty' => $pa_orderable_qty,
                    'pa_fulfilled_qty' => $pa_fulfilled_qty,
                    'pa_open_order_qty' => $pa_open_order_qty,
                    'nv_qty' => $NV_qty,
                    'nv_each_qty' => $nv_each_qty,
                    'nv_orderable_qty' => $nv_orderable_qty,
                    'nv_fulfilled_qty' => $nv_fulfilled_qty,
                    'nv_open_order_qty' => $nv_open_order_qty,
                    'okc_qty' => $OKC_qty,
                    'okc_each_qty' => $okc_each_qty,
                    'okc_orderable_qty' => $okc_orderable_qty,
                    'okc_fulfilled_qty' => $okc_fulfilled_qty,
                    'okc_open_order_qty' => $okc_open_order_qty
                ]);
            }
        }
    }
}
