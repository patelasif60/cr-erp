<?php

namespace App\Console\Commands;

use App\User;
use App\Carrier;
use App\HotRoute;
use App\WareHouse;
use App\AisleMaster;
use App\ClientChannelConfiguration;
use App\MasterShelf;
use App\OrderDetail;
use App\OrderSummary;
use App\MasterProduct;
use App\EtailerService;
use phpseclib3\Net\SFTP;
use App\SkuOrderExclusion;
use App\ShippingServiceType;
use App\OrderAutomaticUpgrades;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\MasterProductKitComponents;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\UpsController;
use App\Repositories\NotificationRepository;

class IncomingOrderProcessing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:incoming_order_processing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The command fetches the order details and does the following: 
                                1. Check the address from UPS Service
                                2. Check the ETIN.
                                3. Check the fulfilled by
                                4. Create Sub-Orders based upon fulfilled by
                                5. Assign warehouse for orders to get dispatched.';

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

    private $cf_sub_order_number;
    private $selected_wh;
    private $selected_td;

    private function getRandomString($n)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    private $service_tr_map = [
        'Next Day Air Saver' => 1,
        'Next Day Air' => 1,
        'UPS Next Day Air® Early' => 1,
        '2nd Day Air' => 2,
        '2nd Day Air A.M.' => 2,
        'Overnight' => 1,
        'Priority Overnight' => 1,
        '2 Day' => 2,
        '2 Day AM' => 2,
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {

        Log::channel('IncomingOrderProcessing')->info('Starting Orders Processing');
        DB::table('incoming_order_processing')->truncate();
        LogIncommingOrderProcessing([
            'req' => 'Starting Orders Processing'
        ]);

        Log::channel('IncomingOrderProcessing')->info('Getting all new orders');
        
        $summaries = OrderSummary::whereIn('order_status', [1, 19])
            ->whereIn('order_type_id', [1, 2])
            ->get();
        LogIncommingOrderProcessing([
            'req' => 'Getting all new orders',
            'res' => json_encode($summaries)
        ]);
            
        if (!$summaries || count($summaries) == 0 ) {
            LogIncommingOrderProcessing([
                'req' => 'No new Orders'
            ]);
            Log::channel('IncomingOrderProcessing')->info('No new Orders');
            Log::channel('IncomingOrderProcessing')->info('=================================');
            Log::channel('IncomingOrderProcessing')->info('');
            return 0;
        }

        // $this->connectAndSendFileToDot();
        // $this->connectAndSendFileToKehe();

        foreach($summaries as $summary){

            $this->cf_sub_order_number = $this->getRandomString(3);

            $subOrdersArray = [];

            if ($summary) {
                Log::channel('IncomingOrderProcessing')->info('************Start of '.$summary->etailer_order_number.' **************');
                Log::channel('IncomingOrderProcessing')->info('Order number: ' . $summary->etailer_order_number);
                LogIncommingOrderProcessing([
                    'req' => 'Order number: ' . $summary->etailer_order_number,
                    'res' => json_encode($summary)
                ]); 
                $isManual = $summary->order_status == 19;            
                $this->selected_wh = '';     
                $this->selected_td = '';   

                // Check address
                Log::channel('IncomingOrderProcessing')->info('Checking address for: ' . $summary->etailer_order_number);
                $status = $this->checkAddressFromUPS($summary);
                LogIncommingOrderProcessing([
                    'req' => 'Checking address for: ' . $summary->etailer_order_number,
                    'res' => json_encode($status)
                ]); 
                if (strtolower($status) === 'error') {
                    Log::channel('IncomingOrderProcessing')->info('Error in Address');
                    Log::channel('IncomingOrderProcessing')->info('=================================');
                    Log::channel('IncomingOrderProcessing')->info('');
                    LogIncommingOrderProcessing([
                        'req' => 'Error in Address'
                    ]); 
                    return 0;
                    //continue;
                }

                //Check order address if address1 have PO than order status will be Review Address
                if ((strpos($summary->ship_to_address1, 'PO') !== false) || (strpos($summary->ship_to_address1, 'P.O') !== false) || (strpos($summary->ship_to_address1, 'P O') !== false)) {
                    $summary->order_status = 15;
                    Log::channel('IncomingOrderProcessing')->info('Order have PO Address: ' . $summary->etailer_order_number);
                    LogIncommingOrderProcessing([
                        'req' => 'Order have PO Address: ' . $summary->etailer_order_number
                    ]); 
                    $summary->save();
                    //continue;
                }

                
                $orders = OrderDetail::where('order_number', $summary->etailer_order_number)->get();
                if (!$orders || count($orders) <= 0) {
                    Log::channel('IncomingOrderProcessing')
                    ->info('No details found for Order Number: ' . $summary->etailer_order_number);
                    LogIncommingOrderProcessing([
                        'req' => 'No details found for Order Number: ' . $summary->etailer_order_number
                    ]); 
                    // return 0;
                    continue;
                }
                
                $this->checkETINAndAssignFlag($orders, $summary->client_id, $summary->channel_id, $summary->etailer_order_number);

                Log::channel('IncomingOrderProcessing')->info('************Start WH Checking*************');
                LogIncommingOrderProcessing([
                    'req' => '************Start WH Checking*************'
                ]); 
                $wh_assign = $this->checkIfItemsArePresentAndAssignWarehouse($orders, $summary);       
                Log::channel('IncomingOrderProcessing')->info('************End WH Checking**************');
                LogIncommingOrderProcessing([
                    'req' => '************End WH Checking**************'
                ]); 
                foreach ($orders as $order) {
                    
                    Log::channel('IncomingOrderProcessing')
                        ->info('Checking ETIN for Order: ' . $summary->etailer_order_number . ' ETIN: ' . $order->ETIN);            
                        LogIncommingOrderProcessing([
                            'req' => 'Checking ETIN for Order: ' . $summary->etailer_order_number . ' ETIN: ' . $order->ETIN
                        ]); 
                    $this->checkETIN($summary, $order);

                    Log::channel('IncomingOrderProcessing')
                        ->info('Checking Fullfilled By for Order: ' . $summary->etailer_order_number . ' ETIN: ' . $order->ETIN);
                    LogIncommingOrderProcessing([
                        'req' => 'Checking Fullfilled By for Order: ' . $summary->etailer_order_number . ' ETIN: ' . $order->ETIN
                    ]); 
                    $this->checkFulfilledBy($summary, $order);
                    if ($wh_assign && (!isset($order->warehouse) || $order->warehouse == '')) {
                        Log::channel('IncomingOrderProcessing')
                            ->info('Assigning WH for Order: ' . $summary->etailer_order_number . ' ETIN: ' . $order->ETIN);
                        LogIncommingOrderProcessing([
                            'req' => 'Assigning WH for Order: ' . $summary->etailer_order_number . ' ETIN: ' . $order->ETIN
                        ]); 
                        $this->assignWarehouse3($summary, $order);                  
                    } else {
                        Log::channel('IncomingOrderProcessing')
                            ->info('WH already assigned for Order: ' . $summary->etailer_order_number . ' ETIN: ' . $order->ETIN . '. WH: ' . $order->warehouse);
                        LogIncommingOrderProcessing([
                            'req' => 'WH already assigned for Order: ' . $summary->etailer_order_number . ' ETIN: ' . $order->ETIN . '. WH: ' . $order->warehouse
                        ]); 
                        $this->selected_wh = $order->warehouse;
                        $this->selected_td = $order->transit_days;
                    }
                    
                    
                    Log::channel('IncomingOrderProcessing')
                        ->info('fetchItems: ' . $summary->etailer_order_number . ' ETIN: ' . $order->ETIN);
                    LogIncommingOrderProcessing([
                        'req' => 'fetchItems: ' . $summary->etailer_order_number . ' ETIN: ' . $order->ETIN
                    ]); 
                    
                    

                    Log::channel('IncomingOrderProcessing')
                        ->info('Creating Sub-Order for Order: ' . $summary->etailer_order_number . ' ETIN: ' . $order->ETIN);
                    LogIncommingOrderProcessing([
                        'req' => 'Creating Sub-Order for Order: ' . $summary->etailer_order_number . ' ETIN: ' . $order->ETIN
                    ]); 
                    $sB_order = $this->createSubOrders($summary, $order);   
                    if($sB_order == ""){
                        LogIncommingOrderProcessing([
                            'req' => 'Sub-Order Could not be generated: ' . $summary->etailer_order_number . ' ETIN: ' . $order->ETIN
                        ]); 
                        continue;
                    }

                    if($this->selected_wh != ''){
                        $items = $this->fetchItems($order->ETIN, $this->selected_wh,$order->quantity_ordereds);
                        if ($items <= 0) {
                            Log::channel('IncomingOrderProcessing')->info('ETIN: ' . $order->ETIN . ' is OOS.');
                            LogIncommingOrderProcessing([
                                'req' => 'ETIN: ' . $order->ETIN . ' is OOS.'
                            ]); 
                            DB::table('order_history')->insert([
                                'mp_order_number' => $summary->channel_order_number,
                                'etailer_order_number' => $summary->etailer_order_number,
                                'date' => date("Y-m-d H:i:s", strtotime('now')),
                                'action' => 'Error: WH Assignment.',
                                'details' => 'Error: ETIN: ' . $order->ETIN . ' is OOS.',
                                'user' => 'Auto Process',
                                'created_at' => date("Y-m-d H:i:s"),
                                'updated_at' => date("Y-m-d H:i:s")
                            ]);

                            $order->status = '16';
                            if (isset($this->selected_wh) && isset($this->selected_td) && $this->selected_wh !== '') {
                                $order->warehouse = $this->selected_wh;
                                $order->transit_days = $this->selected_td;               
                            }  
                            $order->save();

                            $summary->order_status = '26';
                            $summary->save();
                            return;
                        }
                    }            

                    //if (!$isManual) {
                    if (isset($order->warehouse) && $order->warehouse !== '' && $order->warehouse !== 'CF' && isset($order->sub_order_number) 
                            && isset($order->fulfilled_by) && isset($summary->client_id) && $order->status != '16') {
                        Log::channel('IncomingOrderProcessing')
                            ->info('Assigning Carrier, Account and Service Type for Order:  ' . $summary->etailer_order_number . ' ETIN: ' . $order->ETIN . ', WH: ' . $order->warehouse);
                        LogIncommingOrderProcessing([
                            'req' => 'Assigning Carrier, Account and Service Type for Order:  ' . $summary->etailer_order_number . ' ETIN: ' . $order->ETIN . ', WH: ' . $order->warehouse
                        ]); 
                        $this->assignCarrierAndAccountAndServiceType($order, $summary);
                    } else {
                        if ($order->status == 17) {
                            Log::channel('IncomingOrderProcessing')
                                ->info('Cannot assign Carrier/Service for Client Fulfilled ETIN: ' . $order->ETIN);
                            LogIncommingOrderProcessing([
                                'req' => 'Cannot assign Carrier/Service for Client Fulfilled ETIN: ' . $order->ETIN
                            ]); 
                        } else {
                            Log::channel('IncomingOrderProcessing')
                                ->info('Cannot assigning Carrier, Account and Service Type for Order: ' . $summary->etailer_order_number . ' ETIN: ' . $order->ETIN . '. Mandatory Param(s) missing. [warehouse, sub order number, fulfilled by, client id]');
                            LogIncommingOrderProcessing([
                                'req' => 'Cannot assigning Carrier, Account and Service Type for Order: ' . $summary->etailer_order_number . ' ETIN: ' . $order->ETIN . '. Mandatory Param(s) missing. [warehouse, sub order number, fulfilled by, client id]'
                            ]); 
                        }                    
                    }
                    //}

                    $subOrdersArray[] = $order->status;
                }
                
                Log::channel('IncomingOrderProcessing')
                        ->info('Checking if WH assigned to all Sub-Orders Order: ' . $summary->etailer_order_number);
                LogIncommingOrderProcessing([
                    'req' => 'Checking if WH assigned to all Sub-Orders Order: ' . $summary->etailer_order_number
                ]); 
                $wh = $orders->unique('warehouse')->pluck("warehouse");

                // Set the status to Ready for Pick iff all ETINs have WH assigned to them.
                if (!$wh->contains(null) && !in_array($summary->order_status, [7, 8, 9, 15, 26])) {
                    Log::channel('IncomingOrderProcessing')
                        ->info('WH assigned to all. Order: ' . $summary->etailer_order_number);
                    LogIncommingOrderProcessing([
                        'req' => 'WH assigned to all. Order: ' . $summary->etailer_order_number
                    ]); 
                    if(!$isManual) {
                        Log::channel('IncomingOrderProcessing')
                            ->info('Assigning Ready to Ship Status to Order Number: ' . $summary->etailer_order_number);
                        LogIncommingOrderProcessing([
                            'req' => 'Assigning Ready to Ship Status to Order Number: ' . $summary->etailer_order_number
                        ]); 
                        $summary->order_status = '2';
                    } else {
                        Log::channel('IncomingOrderProcessing')
                            ->info('Assigning Manual - Ready to Ship Status to Order Number: ' . $summary->etailer_order_number);
                        LogIncommingOrderProcessing([
                            'req' => 'Assigning Manual - Ready to Ship Status to Order Number: ' . $summary->etailer_order_number
                        ]); 
                        $summary->order_status = '23';
                    }

                    /* Notify other admins */		
                    $this->notifyOtherAdmins($summary);
                }
                
                if(in_array($subOrdersArray, [17]) && !in_array($subOrdersArray, [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,18])){
                    $summary->order_status = 27;
                }
                
                $summary->save();

                checkHotRoute($summary);
                if ($summary->saturday_eligible == 1) {
                    $this->assignServiceForSaturdayDelivery($orders);
                }
                
                UpdateOrderHistory([
                    'order_number' => $summary->etailer_order_number,
                    'detail' => 'Order #'.$summary->etailer_order_number.' Status changed to '.OrderSummeryStatusName($summary->order_status).' From Automatic Jobs',
                    'title' => 'Order Status Changed',
                    'reference' => 'Cron',
                    'extras' => json_encode($summary)
                ]);

                Log::channel('IncomingOrderProcessing')->info('************End of '.$summary->etailer_order_number.' **************');
            }
        }



        Log::channel('IncomingOrderProcessing')->info('Completed Orders Processing');
        Log::channel('IncomingOrderProcessing')->info('=================================');
        Log::channel('IncomingOrderProcessing')->info('');
        LogIncommingOrderProcessing([
            'req' => 'Completed Orders Processing'
        ]);

        return 0;
    }

    private function assignServiceForSaturdayDelivery($orders) {
        LogIncommingOrderProcessing([
            'req' => 'assignServiceForSaturdayDelivery',
            'res' => json_encode($order)
        ]);
        $day = strtolower(jddayofweek(date("w", strtotime('now')) - 1, 1));	
        foreach($orders as $od) {
            $service_id = '';
            if ($od->carrier_id == 1) {
                $service_id = $day == 'thursday' ? '5' : '2';
            } else if ($od->carrier_id == 2) {
                $service_id = $day == 'thursday' ? '22' : '20';
            }
            OrderDetail::where('order_number', $od->order_number)->update(['service_type_id' => $service_id]);
            Log::channel('IncomingOrderProcessing')->info('As SAT eligible, Change Service Type to: ' . $service_id . ' for ETIN: ' . $od->ETIN);
            LogIncommingOrderProcessing([
                'req' => 'As SAT eligible, Change Service Type to: ' . $service_id . ' for ETIN: ' . $od->ETIN
            ]);
            UpdateOrderHistory([
                'order_number' => $od->order_number,
                'detail' => 'As SAT eligible, Change Service Type to: ' . $service_id . ' for ETIN: ' . $od->ETIN,
                'title' => 'Order Status Changed',
                'reference' => 'Cron',
                'extras' => json_encode($od)
            ]);
        }
    }    
    
    private function notifyOtherAdmins($summary) {
        $note = $summary->etailer_order_number.' Ready to Pick';
        $url_id = $summary ? $summary->id : '';				
        $type = "Order Status Update";
        $this->NotificationRepository->SendOrderNotification([
            'subject' => $type,
            'body' => $note,
            'order_number' => $summary->etailer_order_number
        ]);
    }

    // Carrier, Account and Service Type
    private function assignCarrierAndAccountAndServiceType($order, $summary) {
        LogIncommingOrderProcessing([
            'req' => 'assignCarrierAndAccountAndServiceType',
            'res' => json_encode([
                'order' => $order,
                'summary' => $summary
            ])
        ]);
        // If multiple client config, then which to choose ?

        $customClient = DB::table("carrier_order_account_assignments")
            ->where('client_id', $summary->client_id)->first();

        $warehouse = $order->warehouse;

        if (isset($warehouse) && $warehouse !== '' && !in_array(strtolower($warehouse), ['wi', 'pa', 'nv', 'okc'])) {
            Log::channel('IncomingOrderProcessing')->info('WH not set. ETIN: ' . $order->ETIN);
            LogIncommingOrderProcessing([
                'req' => 'WH not set. ETIN: ' . $order->ETIN
            ]);
            DB::table('order_history')->insert([
                'mp_order_number' => $summary->channel_order_number,
                'etailer_order_number' => $summary->etailer_order_number,
                'date' => date("Y-m-d H:i:s", strtotime('now')),
                'action' => 'Error: Cannot Assign Carrier. WH not Set',
                'details' => 'WH not set. ETIN: ' . $order->ETIN,
                'user' => 'Auto Process',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $summary->order_status = '8';
            $summary->save();
            return;
        }

        if(strtolower($warehouse) == 'okc') { $warehouse = 'ok'; }
        $temp = $this->getTemp($order->fulfilled_by, $order->sub_order_number);
        if (!isset($temp) || $temp == '') {
            Log::channel('IncomingOrderProcessing')->info('Temperature cannot be set. Fulfilled/Sub Order Number is empty or net set. ETIN: ' . $order->ETIN);
            LogIncommingOrderProcessing([
                'req' => 'Temperature cannot be set. Fulfilled/Sub Order Number is empty or net set. ETIN: ' . $order->ETIN
            ]);
            DB::table('order_history')->insert([
                'mp_order_number' => $summary->channel_order_number,
                'etailer_order_number' => $summary->etailer_order_number,
                'date' => date("Y-m-d H:i:s", strtotime('now')),
                'action' => 'Error: Cannot Assign Carrier',
                'details' => 'Temprature cannot be set. Fulfilled/Sub Order Number is empty or net set. ETIN: ' . $order->ETIN,
                'user' => 'Auto Process',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $summary->order_status = '8';
            $summary->save();
            return;
        }

        Log::channel('IncomingOrderProcessing')->info($order->fulfilled_by . ' ' . $order->sub_order_number . ' ' . $temp);
        LogIncommingOrderProcessing([
            'req' => $order->fulfilled_by . ' ' . $order->sub_order_number . ' ' . $temp
        ]);
        $carrierColName = strtolower($temp) . '_' . strtolower($warehouse) . '_carrier_id';
        $accountColName = strtolower($temp) . '_' . strtolower($warehouse) . '_account_id';

        $carrierId = null; $accountId = null;
        
        if (!$customClient) {
            Log::channel('IncomingOrderProcessing')->info('Client custom config not found. Getting default One.');
            LogIncommingOrderProcessing([
                'req' => 'Client custom config not found. Getting default One.'
            ]);
            $customClient = DB::table("carrier_order_account_assignments")->where('is_default', 1)->first();
            if (!isset($customClient)) {
                Log::channel('IncomingOrderProcessing')->info('Default config not found.');
                LogIncommingOrderProcessing([
                    'req' => 'Default config not found.'
                ]);
            }
        }

        $carrierId = $customClient->$carrierColName;
        $accountId = $customClient->$accountColName;

        // Checking if Carrier and Service Type is set in the summary, than assign
        if (isset($summary->shipment_type) && isset($summary->carrier_id) && $summary->order_source == 'Manual') {
            $order->carrier_id = $summary->carrier_id;
            $order->service_type_id =$summary->shipment_type;
            $order->carrier_account_id = $accountId;
            $order->status = 1;

            if(in_array($summary->shipment_type, [2,3,4,20,21])){
                $order->transit_days = 1;
            }
            else if(in_array($summary->shipment_type, [5,11,22,23])){
                $order->transit_days = 2;
            }
            $order->save();
            return;
        }

        $carrier = isset($carrierId) ? Carrier::where('id', $carrierId)->first() : null;
        
        $code = null;
        if (!isset($carrier)) {
            Log::channel('IncomingOrderProcessing')->info('Carrier not found. Cannot set service type');
            LogIncommingOrderProcessing([
                'req' => 'Carrier not found. Cannot set service type'
            ]);
        } else {
            Log::channel('IncomingOrderProcessing')->info('Carrier found. Service type setting will follow.');
            LogIncommingOrderProcessing([
                'req' => 'Carrier found. Service type setting will follow.'
            ]);
            $upgrades = OrderAutomaticUpgrades::where('client_id', $summary->client_id)->first();
            if ($upgrades) {
                Log::channel('IncomingOrderProcessing')->info('Service type Upgrade found.');
                LogIncommingOrderProcessing([
                    'req' => 'Service type Upgrade found.'
                ]);
                $sst = ShippingServiceType::where('id', $upgrades->service_type_id)->first();
                if (!$sst) {
                    Log::channel('IncomingOrderProcessing')->info('Shipping Service Type Code not found');
                    LogIncommingOrderProcessing([
                        'req' => 'Shipping Service Type Code not found'
                    ]);
                } else {
                    $code = $sst->id;
                }
            } else {
                Log::channel('IncomingOrderProcessing')->info('Service type Upgrade not found. Going for default');
                LogIncommingOrderProcessing([
                    'req' => 'Service type Upgrade not found. Going for default'
                ]);
                $default = EtailerService::where('etailer_service_name', 'Ground')->first();
                if (!$default) {
                    Log::channel('IncomingOrderProcessing')->info('Default Shipping Service not found');
                    LogIncommingOrderProcessing([
                        'req' => 'Default Shipping Service not found'
                    ]);
                } else {
                    Log::channel('IncomingOrderProcessing')->info('Default Shipping for ' . $carrier->company_name);
                    LogIncommingOrderProcessing([
                        'req' => 'Default Shipping for ' . $carrier->company_name
                    ]);
                    Log::channel('IncomingOrderProcessing')->info('Code: ' . (strtolower($carrier->company_name) === 'ups' 
                                            ? $default->ups_service_type_id : $default->fedex_service_type_id));
                    LogIncommingOrderProcessing([
                        'req' => 'Code: ' . (strtolower($carrier->company_name) === 'ups' 
                        ? $default->ups_service_type_id : $default->fedex_service_type_id)
                    ]);
                    $code = strtolower($carrier->company_name) === 'ups' ? $default->ups_service_type_id : $default->fedex_service_type_id;
                }
            }
        }

        $order->carrier_id = $carrierId;
        $order->carrier_account_id = $accountId;
        $order->service_type_id = $code;
        $trDay = $this->checkAndAssignTransitDays($code);
        if ($trDay != -1) {
            $order->transit_days = $trDay;
        }
        $order->status = 1;
        $order->save();
    }

    // Check and assign transit day based on Service Type
    private function checkAndAssignTransitDays($service_id) {

        if ($service_id == 1 || $service_id == 19) {
            Log::channel('IncomingOrderProcessing')->info('Transit Day for Ground (UPS and Fedex) as per WH.');
            LogIncommingOrderProcessing([
                'req' => 'Transit Day for Ground (UPS and Fedex) as per WH.'
            ]);
            return -1;
        }
        
        $sst = ShippingServiceType::where('id', $service_id)->first();
        if (!isset($sst)) {
            Log::channel('IncomingOrderProcessing')->info('ShippingServiceType not found for Id: ' . $service_id . '. TR day as per WH.');
            LogIncommingOrderProcessing([
                'req' => 'ShippingServiceType not found for Id: ' . $service_id . '. TR day as per WH.'
            ]);
        }

        if (in_array($sst->service_name, $this->service_tr_map)) {            
            Log::channel('IncomingOrderProcessing')->info('Transit day for: ' . $sst->service_name . ' : ' . $this->service_tr_map[$sst->service_name]);
            LogIncommingOrderProcessing([
                'req' => 'Transit day for: ' . $sst->service_name . ' : ' . $this->service_tr_map[$sst->service_name]
            ]);
            return $this->service_tr_map[$sst->service_name];
        }
        
        Log::channel('IncomingOrderProcessing')->info('Transit Day for: '.$sst->service_name.' not found in collection. TR day as per WH.');
        LogIncommingOrderProcessing([
            'req' => 'Transit Day for: '.$sst->service_name.' not found in collection. TR day as per WH.'
        ]);
        return -1;
    }

    // Check Address
    private function checkAddressFromUPS($summary) {

        $city = $summary->ship_to_city;
        $state = $summary->ship_to_state;
        $zip = $summary->ship_to_zip;

        if (!(isset($city) && isset($state) && isset($zip))) {
            Log::channel('IncomingOrderProcessing')->info('Basic Adress Validating fields are not present.');
            LogIncommingOrderProcessing([
                'req' => 'Basic Adress Validating fields are not present.'
            ]);
            DB::table('order_history')->insert([
                'mp_order_number' => $summary->channel_order_number,
                'etailer_order_number' => $summary->etailer_order_number,
                'date' => date("Y-m-d H:i:s", strtotime('now')),
                'action' => 'Address Not Validated',
                'details' => 'No Address details found',
                'user' => 'Auto Process',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $summary->order_status = '15';
            $summary->save();
        } else {
            $ups = new UpsController();
            Log::channel('IncomingOrderProcessing')->info('Fetching address validation');
            LogIncommingOrderProcessing([
                'req' => 'Fetching address validation'
            ]);
            $status = $ups->validateAddress($city, $state, $zip);
            if ($status === 'Success') {
                Log::channel('IncomingOrderProcessing')->info('Address is valid');
                LogIncommingOrderProcessing([
                    'req' => 'Address is valid'
                ]);
                $check_history = DB::table('order_history')->where('etailer_order_number',$summary->etailer_order_number)->where('action','Address Validated')->first();
                if($check_history){
                    DB::table('order_history')->where('id',$check_history->id)->update([
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s")
                    ]);
                }else{
                    DB::table('order_history')->insert([
                        'mp_order_number' => $summary->channel_order_number,
                        'etailer_order_number' => $summary->etailer_order_number,
                        'date' => date("Y-m-d H:i:s", strtotime('now')),
                        'action' => 'Address Validated',
                        'details' => '',
                        'user' => 'Auto Process',
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s")
                    ]);
                }
                
            } else {
                Log::channel('IncomingOrderProcessing')->info('Address not valid');
                LogIncommingOrderProcessing([
                    'req' => 'Address not valid'
                ]);
                $check_history = DB::table('order_history')->where('etailer_order_number',$summary->etailer_order_number)->where('action','Address Not Validated')->first();
                if($check_history){
                    DB::table('order_history')->where('id',$check_history->id)->update([
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s")
                    ]);
                }else{
                    DB::table('order_history')->insert([
                        'mp_order_number' => $summary->channel_order_number,
                        'etailer_order_number' => $summary->etailer_order_number,
                        'date' => date("Y-m-d H:i:s", strtotime('now')),
                        'action' => 'Address Not Validated',
                        'details' => '',
                        'user' => 'Auto Process',
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s")
                    ]);
                }
                $summary->order_status = '15';
                $summary->save();

                return "Error";
            }
        }
        return "Success";
    }

    // Check the ETIN
    private function checkETIN($summary, $orderDetail) {

        if ($orderDetail->ETIN_flag == 0) {
            Log::channel('IncomingOrderProcessing')->info('ETIN matched with Product');
            LogIncommingOrderProcessing([
                'req' => 'ETIN matched with Product'
            ]);
            DB::table('order_history')->insert([
                'mp_order_number' => $summary->channel_order_number,
                'etailer_order_number' => $summary->etailer_order_number,
                'date' => date("Y-m-d H:i:s", strtotime('now')),
                'action' => 'ETIN Matched',
                'details' => 'Incoming ETIN "' . $orderDetail->ETIN . '" matched.',
                'user' => 'Auto Process',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
        } else if ($orderDetail->ETIN_flag == 1) {
            Log::channel('IncomingOrderProcessing')->info('ETIN matched with Product Listing ETIN');
            LogIncommingOrderProcessing([
                'req' => 'ETIN matched with Product Listing ETIN'
            ]);
            DB::table('order_history')->insert([
                'mp_order_number' => $summary->channel_order_number,
                'etailer_order_number' => $summary->etailer_order_number,
                'date' => date("Y-m-d H:i:s", strtotime('now')),
                'action' => 'ETIN Matched with Product Listing ETIN.',
                'details' => 'Incoming ETIN "' . $orderDetail->ETIN . '" matched with Product Listing ETIN.',
                'user' => 'Auto Process',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
        } else if ($orderDetail->ETIN_flag == 2) {
            Log::channel('IncomingOrderProcessing')->info('Incoming ETIN matched is ALT ETIN');
            LogIncommingOrderProcessing([
                'req' => 'Incoming ETIN matched is ALT ETIN'
            ]);
            DB::table('order_history')->insert([
                'mp_order_number' => $summary->channel_order_number,
                'etailer_order_number' => $summary->etailer_order_number,
                'date' => date("Y-m-d H:i:s", strtotime('now')),
                'action' => 'ETIN Matched with ALT ETIN.',
                'details' => 'Incoming ETIN "' . $orderDetail->ETIN . '" matched is ALT ETIN "' . $orderDetail->SA_sku . '"',
                'user' => 'Auto Process',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            //$summary->order_status = '9';
            //$summary->save();
        } else if ($orderDetail->ETIN_flag == 3) {

            $checkEtin = DB::table('master_product')->where('ETIN', $orderDetail->ETIN_flag)->get();
			$checkProductListEtin = DB::table('master_product')->whereRaw('FIND_IN_SET(product_listing_ETIN , "'.$orderDetail->ETIN_flag.'")');
			$checkAlternateEtin = DB::table('master_product')->whereRaw('FIND_IN_SET(alternate_ETINs , "'.$orderDetail->ETIN_flag.'")');

            $ETIN_flag = 3;
            if(isset($checkEtin)){
				$ETIN_flag = 0;
			} else if(isset($checkProductListEtin)){
				$ETIN_flag = 1;
			} else if(isset($checkAlternateEtin)){
				$ETIN_flag = 2;
            } 

            if ($ETIN_flag === 0 || $ETIN_flag === 1) {
                Log::channel('IncomingOrderProcessing')
                    ->info($ETIN_flag === 0 ? 'ETIN matched with Product' : 'ETIN matched with Product Listing ETIN');
                LogIncommingOrderProcessing([
                    'req' => $ETIN_flag === 0 ? 'ETIN matched with Product' : 'ETIN matched with Product Listing ETIN'
                ]);
                DB::table('order_history')->insert([
                    'mp_order_number' => $summary->channel_order_number,
                    'etailer_order_number' => $summary->etailer_order_number,
                    'date' => date("Y-m-d H:i:s", strtotime('now')),
                    'action' => $ETIN_flag === 0 ? 'ETIN matched with Product' : 'ETIN matched with Product Listing ETIN',
                    'details' 
                        => $ETIN_flag === 0
                            ? 'Incoming ETIN "' . $orderDetail->ETIN . '" matched.'
                            : 'Incoming ETIN "' . $orderDetail->ETIN . '" matched with Product Listing ETIN.',
                    'user' => 'Auto Process',
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ]);
                $orderDetail->ETIN_flag = $ETIN_flag;
                $orderDetail->save();
            } else if ($ETIN_flag === 2) {
                Log::channel('IncomingOrderProcessing')->info('Incoming ETIN matched is ALT ETIN');
                LogIncommingOrderProcessing([
                    'req' => 'Incoming ETIN matched is ALT ETIN'
                ]);
                DB::table('order_history')->insert([
                    'mp_order_number' => $summary->channel_order_number,
                    'etailer_order_number' => $summary->etailer_order_number,
                    'date' => date("Y-m-d H:i:s", strtotime('now')),
                    'action' => 'ETIN Matched with ALT ETIN.',
                    'details' => 'Incoming ETIN "' . $orderDetail->ETIN . '" matched is ALT ETIN "' . $orderDetail->SA_sku . '"',
                    'user' => 'Auto Process',
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ]);
                //$summary->order_status = '9';
                //$summary->save();
                
                $orderDetail->ETIN_flag = $ETIN_flag;
                $orderDetail->save();
            } else {
                Log::channel('IncomingOrderProcessing')->info('Incoming ETIN ETIN Not found in MPT');
                LogIncommingOrderProcessing([
                    'req' => 'Incoming ETIN ETIN Not found in MPT'
                ]);
                DB::table('order_history')->insert([
                    'mp_order_number' => $summary->channel_order_number,
                    'etailer_order_number' => $summary->etailer_order_number,
                    'date' => date("Y-m-d H:i:s", strtotime('now')),
                    'action' => 'ETIN Not Matched',
                    'details' => 'ETIN Not found in MPT',
                    'user' => 'Auto Process',
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ]);
                $summary->order_status = '8';
                $summary->save();
            }            
        }
    }

    // Check Fulfilled By
    private function checkFulfilledBy($summary, $orderDetail) {

        $fulfilledBy = '';
        if (isset($orderDetail->ETIN_flag) && !in_array($orderDetail->ETIN_flag, [3,4,5])) {
            $fulfilledBy = $this->setFulfilledBy($orderDetail->ETIN);
        } else if ($orderDetail->ETIN_flag == 3) {
            $checkEtin = DB::table('master_product')->where('ETIN', $orderDetail->ETIN_flag)->get();
			$checkProductListEtin = DB::table('master_product')->whereRaw('FIND_IN_SET(product_listing_ETIN , "'.$orderDetail->ETIN_flag.'")');
			$checkAlternateEtin = DB::table('master_product')->whereRaw('FIND_IN_SET(alternate_ETINs , "'.$orderDetail->ETIN_flag.'")');
            if(isset($checkEtin)){
                $fulfilledBy = $this->setFulfilledBy($orderDetail->ETIN);
				$orderDetail->ETIN_flag = 0;
			} else if(isset($checkProductListEtin)){
				$fulfilledBy = $this->setFulfilledBy($orderDetail->ETIN);
				$orderDetail->ETIN_flag = 1;
			} else if(isset($checkAlternateEtin)){
				$fulfilledBy = $this->setFulfilledBy($orderDetail->ETIN);
				$orderDetail->ETIN_flag = 2;
			} else {
				Log::channel('IncomingOrderProcessing')->info('Master Product not found. ETIN: ' . $orderDetail->ETIN);
                LogIncommingOrderProcessing([
                    'req' => 'Master Product not found. ETIN: ' . $orderDetail->ETIN
                ]);
                DB::table('order_history')->insert([
                    'mp_order_number' => $summary->channel_order_number,
                    'etailer_order_number' => $summary->etailer_order_number,
                    'date' => date("Y-m-d H:i:s", strtotime('now')),
                    'action' => 'Error: ETIN not found.',
                    'details' => 'Error: ETIN ' . $orderDetail->ETIN . ' not present in MPT',
                    'user' => 'Auto Process',
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ]);
			}            
        }
        
        Log::channel('IncomingOrderProcessing')->info('Fulfilled By: ' . $fulfilledBy);
        LogIncommingOrderProcessing([
            'req' => 'Fulfilled By: ' . $fulfilledBy
        ]);
        $orderDetail->quantity_fulfilled = $orderDetail->quantity_ordered;
        $orderDetail->fulfilled_by = $fulfilledBy;
        $orderDetail->save();
    }

    private function setFulfilledBy($etin) {
        $mpt = DB::table('master_product')->where('ETIN', $etin)->first();
        $availability =  strtolower(EtailerAvailabilityName($mpt->etailer_availability));
        if ($availability == 'stocked' || $availability == 'special order' || $availability == 'catch all') {
            $fulfilledBy = 'e-tailer';
        } else if ($availability == 'dropshipped') {
            $fulfilledBy = strtolower($mpt->current_supplier);
        }
        return $fulfilledBy;
    }

    // Crate Sub-Order
    private function createSubOrders($summary, $orderDetail) {
        $error = "";
        Log::channel('IncomingOrderProcessing')->info('Creating Sub-Order ETIN: ' . $orderDetail->ETIN);
        LogIncommingOrderProcessing([
            'req' => 'Creating Sub-Order ETIN: ' . $orderDetail->ETIN
        ]);
        $subOrderNumber = "";
        if (isset($orderDetail->ETIN_flag) && !in_array($orderDetail->ETIN_flag, [3,4,5])) {
            $mpt = DB::table('master_product')->where('ETIN', $orderDetail->ETIN)->first();
            $temp = $mpt->product_temperature;            
            if ($temp && strlen($temp) > 0) {
                $temp = strtolower($temp);
                switch(strtolower($orderDetail->fulfilled_by)) {
                    case "e-tailer":
                        if (str_contains($temp, 'frozen')) {
                            $subOrderNumber = $summary->etailer_order_number . '.001';
                        } else if (str_contains($temp, 'dry')) {
                            $subOrderNumber = $summary->etailer_order_number . '.002';
                        } else if (str_contains($temp, 'refrigerated')) {
                            $subOrderNumber = $summary->etailer_order_number . '.003';
                        } else {
                            Log::channel('IncomingOrderProcessing')
                                ->info('Bad Temperature: ' . $temp . ' not in record. ETIN: ' . $orderDetail->ETIN);
                                LogIncommingOrderProcessing([
                                    'req' => 'Bad Temperature: ' . $temp . ' not in record. ETIN: ' . $orderDetail->ETIN
                                ]);
                        }
                        break;
                    case "dot":
                        if (str_contains($temp, 'frozen')) {
                            $subOrderNumber = $summary->etailer_order_number . '.004';
                        } else if (str_contains($temp, 'dry')) {
                            $subOrderNumber = $summary->etailer_order_number . '.005';
                        } else if (str_contains($temp, 'refrigerated')) {
                            $subOrderNumber = $summary->etailer_order_number . '.006';
                        } else {
                            Log::channel('IncomingOrderProcessing')
                                ->info('Bad Temperature: ' . $temp . ' not in record. ETIN: ' . $orderDetail->ETIN);
                            LogIncommingOrderProcessing([
                                'req' => 'Bad Temperature: ' . $temp . ' not in record. ETIN: ' . $orderDetail->ETIN
                            ]);
                            
                        }
                        break;
                    case "kehe":
                        if (str_contains($temp, 'dry')) {
                            $subOrderNumber = $summary->etailer_order_number . '.006';
                        } else {
                            Log::channel('IncomingOrderProcessing')
                                ->info('Bad Temperature: ' . $temp . ' not in record. ETIN: ' . $orderDetail->ETIN);
                            LogIncommingOrderProcessing([
                                'req' => 'Bad Temperature: ' . $temp . ' not in record. ETIN: ' . $orderDetail->ETIN
                            ]);
                        }
                        break;
                    default:
                        Log::channel('IncomingOrderProcessing')
                                ->info('Fulfilled By: ' . $orderDetail->fulfilled_by . ' not in record. ETIN: ' . $orderDetail->ETIN);
                        LogIncommingOrderProcessing([
                            'req' => 'Fulfilled By: ' . $orderDetail->fulfilled_by . ' not in record. ETIN: ' . $orderDetail->ETIN
                        ]);
                        break;
                }
            }
        } else if ($orderDetail->ETIN_flag == 4 || $orderDetail->ETIN_flag == 5) {
            $subOrderNumber = $summary->etailer_order_number . '.' . strtoupper($this->cf_sub_order_number);
            $orderDetail->status = 17;
            Log::channel('IncomingOrderProcessing')
                ->info(
                    $orderDetail->ETIN_flag == 4 
                        ? 'SKU in Exclusion List. ETIN: ' . $orderDetail->ETIN . '. Sub Order: ' . $subOrderNumber
                        : 'Channel is DNE and SKU is not found. ETIN: ' . $orderDetail->ETIN . '. Sub Order: ' . $subOrderNumber);
            LogIncommingOrderProcessing([
                'req' => $orderDetail->ETIN_flag == 4 
                ? 'SKU in Exclusion List. ETIN: ' . $orderDetail->ETIN . '. Sub Order: ' . $subOrderNumber
                : 'Channel is DNE and SKU is not found. ETIN: ' . $orderDetail->ETIN . '. Sub Order: ' . $subOrderNumber
            ]);
            DB::table('order_history')->insert([
                'mp_order_number' => $summary->channel_order_number,
                'etailer_order_number' => $summary->etailer_order_number,
                'date' => date("Y-m-d H:i:s", strtotime('now')),
                'action' => 'Create client fulfilled sub-order',
                'details' => 
                $orderDetail->ETIN_flag == 4 
                    ? 'SKU in Exclusion List. ETIN: ' . $orderDetail->ETIN . '. Sub Order: ' . $subOrderNumber
                    : 'Channel is DNE and SKU is not found. ETIN: ' . $orderDetail->ETIN . '. Sub Order: ' . $subOrderNumber,
                'user' => 'Auto Process',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            $error = 1;
        } else {
            Log::channel('IncomingOrderProcessing')
                    ->info('Master Product not found. ETIN: ' . $orderDetail->ETIN);
            LogIncommingOrderProcessing([
                'req' => 'Master Product not found. ETIN: ' . $orderDetail->ETIN
            ]);
            DB::table('order_history')->insert([
                'mp_order_number' => $summary->channel_order_number,
                'etailer_order_number' => $summary->etailer_order_number,
                'date' => date("Y-m-d H:i:s", strtotime('now')),
                'action' => 'Error: Cannot create sub-order',
                'details' => 'Error: ETIN ' . $orderDetail->ETIN . ' not present in MPT',
                'user' => 'Auto Process',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
        }

        $orderDetail->sub_order_number = $subOrderNumber;
        $orderDetail->save();

        return $error;
    }


    private function getLeastTdWh($transitDays) {
        $wiTrday = $transitDays->transit_days_WI;
        $paTrday = $transitDays->transit_days_PA;
        $nvTrday = $transitDays->transit_days_NV;
        $okcTrday = $transitDays->transit_days_OKC;

        Log::channel('IncomingOrderProcessing')->info('TRs - WI|PA|NV|OKC ' . $wiTrday . '|' . $paTrday . '|' . $nvTrday . '|' . $okcTrday);
        LogIncommingOrderProcessing([
            'req' => 'TRs - WI|PA|NV|OKC ' . $wiTrday . '|' . $paTrday . '|' . $nvTrday . '|' . $okcTrday
        ]);
        $lowest = $wiTrday;
        $lowestWh = 'WI';
        $whs = [];

        if ($paTrday < $lowest) {
            $lowest = $paTrday;
            $lowestWh = 'PA';
        }

        if ($nvTrday < $lowest) {
            $lowest = $nvTrday;
            $lowestWh = 'NV';
        }

        if ($okcTrday < $lowest) {
            $lowest = $okcTrday;
            $lowestWh = 'OKC';
        }

        array_push($whs, $lowestWh);
        if (!in_array('WI', $whs) && $lowest == $wiTrday) {array_push($whs, 'WI');}
        if (!in_array('PA', $whs) && $lowest == $paTrday) {array_push($whs, 'PA');}
        if (!in_array('NV', $whs) && $lowest == $nvTrday) {array_push($whs, 'NV');}
        if (!in_array('OKC', $whs) && $lowest == $okcTrday) {array_push($whs, 'OKC');}

        return $whs;
    }


    private function fetchItems($etin, $wh_list,$quantity_ordereds) {
        Log::channel('IncomingOrderProcessing')->info('fetchItems: etin: '.$etin.' wh_list: '.$wh_list.' quantity_ordereds: '.$quantity_ordereds.'');
        LogIncommingOrderProcessing([
            'req' => 'fetchItems: etin: '.$etin.' wh_list: '.$wh_list.' quantity_ordereds: '.$quantity_ordereds.''
        ]);
        $mp = MasterProduct::where('ETIN', $etin)->first();
		$units_in_pack_child = 0;
		$units_in_pack_parent = 0;
		$is_kit = false;
		$etins = [];
        $OD = new OrderDetail;
        $GetAvailableQty = $OD->GetAvailableQty($etin, $wh_list);
        
		if (isset($mp) && isset($mp->item_form_description) && str_contains(strtolower($mp->item_form_description), 'kit')) {
            $kit_comps = MasterProductKitComponents::leftJoin('master_product', function($join){
                $join->on('master_product.ETIN','=','master_product_kit_components.components_ETIN');
            })
            ->select('master_product_kit_components.*')
            ->where('master_product_kit_components.ETIN', $mp->ETIN)->get();
            
            if($kit_comps && count($kit_comps) > 0){
                foreach($kit_comps as $row_kit_components){
                    array_push($etins, $row_kit_components->components_ETIN);
                }
                $is_kit = true;
            }
        } else if (isset($mp) && isset($mp->parent_ETIN)) {			
			$etin = $mp->parent_ETIN;
			$units_in_pack_child = $mp->unit_in_pack;

			$parent = MasterProduct::where('ETIN', $etin)->first();
			if(!$parent){
				return response()->json([
					'error' => true,
					'msg' => 'Parent product not present into our app '.$etin,
				]);
			}
			$units_in_pack_parent = $parent->unit_in_pack;
		}

        $wh = WareHouse::where('warehouses', $wh_list)->first();

        $AisleMaster = AisleMaster::where('warehouse_id', $wh->id)->pluck('id')->toArray();
        if ($is_kit) {
            $masterShelfSum = 0;
            foreach($etins as $et) {
                $shelfSum = MasterShelf::where('ETIN',$et)->whereIN('aisle_id',$AisleMaster)->whereIN('location_type_id', [1,2])->sum('cur_qty');
                $mp = MasterProduct::where('ETIN', $et)->first();
                if (isset($mp) && isset($mp->parent_ETIN)) {		
                    $etin = $mp->parent_ETIN;
                    $units_in_pack_child = $mp->unit_in_pack;
        
                    $parent = MasterProduct::where('ETIN', $etin)->first();
                    if($parent){
                        $units_in_pack_parent = $parent->unit_in_pack;

                        $masterShelfSum_parent = MasterShelf::where('ETIN',$etin)->whereIN('aisle_id',$AisleMaster)->sum('cur_qty');
                        if (isset($masterShelfSum_parent) && $masterShelfSum_parent > 0 && $units_in_pack_child > 0) {
                            $count = floor(($masterShelfSum_parent * $units_in_pack_parent)/$units_in_pack_child);
                        }
                        if ($masterShelfSum < ($shelfSum + $count)) {
                            $masterShelfSum = $shelfSum + $count;
                        }
                    }                    
                } else {
                    $sum = MasterShelf::whereIn('ETIN',$etins)
                        ->whereIN('aisle_id',$AisleMaster)
                        ->where('cur_qty', '>', 0)
                        ->whereIN('location_type_id', [1,2])
                        ->orderBy('cur_qty', 'asc')
                        ->limit(1)
                        ->sum('cur_qty');
                    $masterShelfSum += !isset($sum) && $sum <= 0 ? 0 : $sum;                        
                }
            }                
        } else {
            $masterShelfSum = MasterShelf::where('ETIN',$etin)->whereIN('aisle_id',$AisleMaster)->whereIN('location_type_id', [1,2])->sum('cur_qty');
            $masterShelf_child_sum = isset($mp)
                 ? MasterShelf::where('ETIN',$mp->ETIN)->whereIN('aisle_id',$AisleMaster)->whereIN('location_type_id', [1,2])->sum('cur_qty')
                 : 0;
            
            $count = 0;
            if (isset($masterShelfSum) && $masterShelfSum >= 0 && $units_in_pack_child > 0) {
                $count = floor(($masterShelfSum * $units_in_pack_parent)/$units_in_pack_child);
                // if($masterShelf_child_sum > 0){
                    $count = $count + $masterShelf_child_sum;
                // }
            }
            
        }

        $return = isset($count) && $count > 0 ? $count : $masterShelfSum;
        Log::channel('IncomingOrderProcessing')->info('fetchItems: Count before return: '.$return.' GetAvailableQty: '.$GetAvailableQty.'');
        $return = $return - $GetAvailableQty;
        Log::channel('IncomingOrderProcessing')->info('fetchItems: Count after return: '.$return.' GetAvailableQty: '.$GetAvailableQty.'');
        LogIncommingOrderProcessing([
            'req' => 'fetchItems: Count after return: '.$return.' GetAvailableQty: '.$GetAvailableQty.''
        ]);
        if($return >= $quantity_ordereds){
            return $return;
        }else{
            return 0;
        }
        return $return;
    }

    // Connect and send files to DOT
    private function connectAndSendFileToDot() {

        $host = "Sftp2.dotfoods.com";
        $user = "Etailer";
        $pwd = "V4dsHTH^MQf+P^_b";

        define('NET_SFTP_LOGGING', SFTP::LOG_COMPLEX);

        $sftp = new SFTP($host);
        
        if (!$sftp->login($user, $pwd)){
            Log::channel('ImportSaInventoryTemplate')->info('SFTP login Failed');
        }

        $elements = $sftp->nlist('.');
        
    }

    // Connect and send files to DOT
    private function connectAndSendFileToKehe() {

        $fileName = "inventory/Inventory_20220622.1611.csv";

        Storage::disk('kehe-ftp')->put('/test/in/Inventory_20220622.1611.csv', public_path($fileName));
  
    }

    private function getTemp($fullfilledBy, $subOrderNumber) {
        switch(strtolower($fullfilledBy)) {
            case "e-tailer":
                if (str_contains($subOrderNumber, '.001')) {
                    return 'Frozen';
                } else if (str_contains($subOrderNumber, '.002')) {
                    return 'Dry';
                } else if (str_contains($subOrderNumber, '.003')) {
                    return 'Refrigerated';
                } 
            case "dot":
                if (str_contains($subOrderNumber, '.004')) {
                    return 'Frozen';
                } else if (str_contains($subOrderNumber, '.005')) {
                    return 'Dry';
                } else if (str_contains($subOrderNumber, '.006')) {
                    return 'Refrigerated';
                }                
            case "kehe":
                if (str_contains($subOrderNumber, '.006')) {
                    return 'Dry';
                } 
                break;
            default:
                break;
        }
    }

    private function checkIfItemsArePresentAndAssignWarehouse($orders, $summary) {

        Log::channel('IncomingOrderProcessing')->info('Checking for WH for Order: ' . $orders[0]->order_number);
        LogIncommingOrderProcessing([
            'req' => 'Checking for WH for Order: ' . $orders[0]->order_number
        ]);
        $zip = $summary->ship_to_zip;
        if (!isset($zip)) {
            Log::channel('IncomingOrderProcessing')->info('Invalid ZIP for Order: ' . $summary->etailer_order_number);
            LogIncommingOrderProcessing([
                'req' => 'Invalid ZIP for Order: ' . $summary->etailer_order_number
            ]);
            DB::table('order_history')->insert([
                'mp_order_number' => $summary->channel_order_number,
                'etailer_order_number' => $summary->etailer_order_number,
                'date' => date("Y-m-d H:i:s", strtotime('now')),
                'action' => 'Error: WH Assignment',
                'details' => 'Error: Invalid ZIP Order Number: ' . $summary->etailer_order_number,
                'user' => 'Auto Process',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $summary->order_status = '7';
            $summary->save();
            return false;
        }

        $zip = substr($zip, 0, 3);
        $transitDays = DB::table('ups_zip_zone_wh')->where('zip_3', $zip)->first();
        if (!isset($transitDays)) {
            Log::channel('IncomingOrderProcessing')->info('Transit Day record not found for ZIP: ' . $zip);
            LogIncommingOrderProcessing([
                'req' => 'Transit Day record not found for ZIP: ' . $zip
            ]);
            DB::table('order_history')->insert([
                'mp_order_number' => $summary->channel_order_number,
                'etailer_order_number' => $summary->etailer_order_number,
                'date' => date("Y-m-d H:i:s", strtotime('now')),
                'action' => 'Error: WH Assignment',
                'details' => 'Error: Transit Day record not found for ZIP: ' . $zip . ', Order Number: ' . $summary->etailer_order_number,
                'user' => 'Auto Process',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $summary->order_status = '7';
            $summary->save();
            return false;
        }
        $tr_day = [];
        $tr_day['WI'] = $transitDays->transit_days_WI;
        $tr_day['PA'] = $transitDays->transit_days_PA;
        $tr_day['NV'] = $transitDays->transit_days_NV;
        $tr_day['OKC'] = $transitDays->transit_days_OKC;

        $assigned_wh = [];
        
        foreach ($orders as $order) {
            
            if ($order->status == 17 || (in_array($order->ETIN_flag, [4, 5]))) {
                continue;
            } 

            Log::channel('IncomingOrderProcessing')->info('Checking for ETIN: ' . $order->ETIN);
            LogIncommingOrderProcessing([
                'req' => 'Checking for ETIN: ' . $order->ETIN
            ]);
            $mp = MasterProduct::where('ETIN', $order->ETIN)->get();
            if (!isset($mp) && count($mp) <= 0) {
                Log::channel('IncomingOrderProcessing')->info('Product not found for ETIN: ' . $order->ETIN);
                LogIncommingOrderProcessing([
                    'req' => 'Product not found for ETIN: ' . $order->ETIN
                ]);
                DB::table('order_history')->insert([
                    'mp_order_number' => $summary->channel_order_number,
                    'etailer_order_number' => $summary->etailer_order_number,
                    'date' => date("Y-m-d H:i:s", strtotime('now')),
                    'action' => 'Error: Invalid Product',
                    'details' => 'Error: Product not found for ETIN: ' . $order->ETIN,
                    'user' => 'Auto Process',
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ]);
                $summary->order_status = '7';
                $summary->save();
                return false;
            }
            
            if (isset($mp[0]->parent_ETIN)) {
                $p_ETIN = $mp[0]->parent_ETIN;
                $mp = MasterProduct::whereRaw('FIND_IN_SET(ETIN , "'.$p_ETIN.'")')->get();
                if (!isset($mp) && count($mp) <= 0) {
                    Log::channel('IncomingOrderProcessing')->info('Parent Product not found for ETIN: ' . $order->ETIN . '. Parent ETIN: ' . $p_ETIN);
                    LogIncommingOrderProcessing([
                        'req' => 'Parent Product not found for ETIN: ' . $order->ETIN . '. Parent ETIN: ' . $p_ETIN
                    ]);
                    DB::table('order_history')->insert([
                        'mp_order_number' => $summary->channel_order_number,
                        'etailer_order_number' => $summary->etailer_order_number,
                        'date' => date("Y-m-d H:i:s", strtotime('now')),
                        'action' => 'Error: Invalid Parent Product',
                        'details' => 'Error: Parent Product not found for ETIN: ' . $order->ETIN . '. Parent ETIN: ' . $p_ETIN,
                        'user' => 'Auto Process',
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s")
                    ]);
                    $summary->order_status = '7';
                    $summary->save();
                    return false;
                }
                Log::channel('IncomingOrderProcessing')->info('Parent Product found for ETIN: ' . $order->ETIN . '. Parent ETIN: ' . $p_ETIN);
                LogIncommingOrderProcessing([
                    'req' => 'Parent Product found for ETIN: ' . $order->ETIN . '. Parent ETIN: ' . $p_ETIN
                ]);
            }
            
            if (isset($mp[0]) && str_contains(strtolower($mp[0]->item_form_description), 'kit')) {
                
                Log::channel('IncomingOrderProcessing')->info('KIT Product found for ETIN: ' . $order->ETIN);
                LogIncommingOrderProcessing([
                    'req' => 'KIT Product found for ETIN: ' . $order->ETIN
                ]);
                $comp_etins = [];
                $kit_comps = MasterProductKitComponents::leftJoin('master_product',function($join){
                    $join->on('master_product.ETIN','=','master_product_kit_components.components_ETIN');
                })
                ->select('master_product_kit_components.*')
                ->where('master_product_kit_components.ETIN', $mp[0]->ETIN)->get();
                
                if($kit_comps && count($kit_comps) > 0) {
                    foreach($kit_comps as $row_kit_components){                        
                        array_push($comp_etins, $row_kit_components->components_ETIN);
                    }
                    $mp = MasterProduct::whereIn('ETIN', $comp_etins);
                    if (!isset($mp) && count($mp) <= 0) {
                        Log::channel('IncomingOrderProcessing')->info('KIT Products not found for ETIN: ' . $order->ETIN . '. KIT Component ETINs: ' . json_encode($comp_etins));
                        LogIncommingOrderProcessing([
                            'req' => 'KIT Products not found for ETIN: ' . $order->ETIN . '. KIT Component ETINs: ' . json_encode($comp_etins)
                        ]);
                        DB::table('order_history')->insert([
                            'mp_order_number' => $summary->channel_order_number,
                            'etailer_order_number' => $summary->etailer_order_number,
                            'date' => date("Y-m-d H:i:s", strtotime('now')),
                            'action' => 'Error: Invalid Kit Product',
                            'details' => 'Error: KIT Products not found for ETIN: ' . $order->ETIN . '. KIT Component ETINs: ' . json_encode($comp_etins),
                            'user' => 'Auto Process',
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => date("Y-m-d H:i:s")
                        ]);
                        $summary->order_status = '7';
                        $summary->save();
                        return false;
                    }
                }
            }
            
            foreach ($mp as $product) {

                if (!isset($product->warehouses_assigned)) {   
                    Log::channel('IncomingOrderProcessing')->info('No Warehouse assigned for ETIN: ' . $product->ETIN);
                    LogIncommingOrderProcessing([
                        'req' => 'No Warehouse assigned for ETIN: ' . $product->ETIN
                    ]);
                    DB::table('order_history')->insert([
                        'mp_order_number' => $summary->channel_order_number,
                        'etailer_order_number' => $summary->etailer_order_number,
                        'date' => date("Y-m-d H:i:s", strtotime('now')),
                        'action' => 'Error: WH Assignment',
                        'details' => 'Error: No Warehouse assigned for ETIN: ' . $product->ETIN,
                        'user' => 'Auto Process',
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s")
                    ]);
                    $summary->order_status = '7';
                    $summary->save();
                    return false;
                }
                
                $warehouses_assigned = explode(',', $product->warehouses_assigned);
                if (count($assigned_wh) <= 0) { 
                    array_push($assigned_wh, ...$warehouses_assigned);
                    Log::channel('IncomingOrderProcessing')->info('Warehouse to be checked: ' . json_encode($assigned_wh));
                    LogIncommingOrderProcessing([
                        'req' => 
                        'Warehouse to be checked: ' . json_encode($assigned_wh)
                    ]);
                } else {
                    $arr = array_replace([], $assigned_wh);
                    $common_wh = array_intersect($arr, $warehouses_assigned);               
                    if (count($common_wh) <= 0) {                        
                        Log::channel('IncomingOrderProcessing')->info('No common warehouse for ETIN: ' . $product->ETIN . '. WH set: ' . json_encode($warehouses_assigned));
                        LogIncommingOrderProcessing([
                            'req' => 
                            'No common warehouse for ETIN: ' . $product->ETIN . '. WH set: ' . json_encode($warehouses_assigned)
                        ]);
                        DB::table('order_history')->insert([
                            'mp_order_number' => $summary->channel_order_number,
                            'etailer_order_number' => $summary->etailer_order_number,
                            'date' => date("Y-m-d H:i:s", strtotime('now')),
                            'action' => 'Error: WH Assignment',
                            'details' => 'Error: No common warehouse for ETIN: ' . $product->ETIN . '. WH set: ' . json_encode($warehouses_assigned),
                            'user' => 'Auto Process',
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => date("Y-m-d H:i:s")
                        ]);
                        $summary->order_status = '7';
                        $summary->save();
                        return;
                        return false;
                    }
                    $assigned_wh = array_replace($assigned_wh, $common_wh);
                }
            }        
        }
        
        if (count($assigned_wh) == 1) {
            $this->selected_wh = $assigned_wh[0];
            $this->selected_td = $tr_day[$assigned_wh[0]];
        } else if (count($assigned_wh) > 1) {
            $this->selected_wh = $assigned_wh[0];
            $this->selected_td = $tr_day[$assigned_wh[0]];
            foreach ($assigned_wh as $as_w) {
                if ($this->selected_td > $tr_day[$as_w]) {
                    $this->selected_wh = $as_w;
                    $this->selected_td = $tr_day[$as_w];
                }
            }
        }
        
        Log::channel('IncomingOrderProcessing')->info('Selected Warehouse: ' . $this->selected_wh);
        Log::channel('IncomingOrderProcessing')->info('Selected Transit Day: ' . $this->selected_td);
        LogIncommingOrderProcessing([
            'req' => 
            'Selected Warehouse: ' . $this->selected_wh
        ]);
        LogIncommingOrderProcessing([
            'req' => 
            'Selected Transit Day: ' . $this->selected_td
        ]);
        return isset($this->selected_wh) && $this->selected_wh != '';
    }

    private function assignWarehouse3($summary, $orderDetail) {

        if ($orderDetail->status == 17 || (in_array($orderDetail->ETIN_flag, [4, 5]))) {
            Log::channel('IncomingOrderProcessing')->info('CF as dummy WH assignment for Client Fulfilled ETIN: ' . $orderDetail->ETIN);
            LogIncommingOrderProcessing([
                'req' => 
                'Selected Transit Day: ' . $this->selected_td
            ]);
            $orderDetail->warehouse = 'CF';
            $orderDetail->status = 17;
            $orderDetail->save();

            DB::table('order_history')->insert([
                'mp_order_number' => $summary->channel_order_number,
                'etailer_order_number' => $summary->etailer_order_number,
                'date' => date("Y-m-d H:i:s", strtotime('now')),
                'action' => 'WH not assigned for CF Product',
                'details' => 'WH not assigned for CF Product ETIN: ' . $orderDetail->ETIN,
                'user' => 'Auto Process',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            return;
        }

        $zip = $summary->ship_to_zip;
        if (!isset($zip)) {
            Log::channel('IncomingOrderProcessing')->info('Invalid ZIP for ETIN: ' . $orderDetail->ETIN);
            LogIncommingOrderProcessing([
                'req' => 
                'Invalid ZIP for ETIN: ' . $orderDetail->ETIN
            ]);
            DB::table('order_history')->insert([
                'mp_order_number' => $summary->channel_order_number,
                'etailer_order_number' => $summary->etailer_order_number,
                'date' => date("Y-m-d H:i:s", strtotime('now')),
                'action' => 'Error: WH Assignment',
                'details' => 'Error: Invalid ZIP Order Number: ' . $summary->etailer_order_number . ' for ETIN ' . $orderDetail->ETIN,
                'user' => 'Auto Process',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $summary->order_status = '7';
            $summary->save();
            return;
        }
        
        Log::channel('IncomingOrderProcessing')->info('Checking for WH assigned.');
        LogIncommingOrderProcessing([
            'req' => 
            'Checking for WH assigned.'
        ]);
        $items = $this->fetchItems($orderDetail->ETIN, $this->selected_wh,$orderDetail->quantity_ordereds);

        if ($items <= 0) {
            Log::channel('IncomingOrderProcessing')->info('ETIN: ' . $orderDetail->ETIN . ' is OOS.');
            LogIncommingOrderProcessing([
                'req' => 
                'ETIN: ' . $orderDetail->ETIN . ' is OOS.'
            ]);
            DB::table('order_history')->insert([
                'mp_order_number' => $summary->channel_order_number,
                'etailer_order_number' => $summary->etailer_order_number,
                'date' => date("Y-m-d H:i:s", strtotime('now')),
                'action' => 'Error: WH Assignment.',
                'details' => 'Error: ETIN: ' . $orderDetail->ETIN . ' is OOS.',
                'user' => 'Auto Process',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            $orderDetail->status = '16';
            if (isset($this->selected_wh) && isset($this->selected_td) && $this->selected_wh !== '') {
                $orderDetail->warehouse = $this->selected_wh;
                $orderDetail->transit_days = $this->selected_td;               
            }  
            $orderDetail->save();

            $summary->order_status = '26';
            $summary->save();
            return;
        }
           
        
        $orderDetail->warehouse = $this->selected_wh;
        $orderDetail->transit_days = $this->selected_td;
        $orderDetail->save();

        Log::channel('IncomingOrderProcessing')->info('WH assigned for ETIN: ' . $orderDetail->ETIN . ' is: ' . $orderDetail->warehouse);
        LogIncommingOrderProcessing([
            'req' => 
            'WH assigned for ETIN: ' . $orderDetail->ETIN . ' is: ' . $orderDetail->warehouse
        ]);
        DB::table('order_history')->insert([
            'mp_order_number' => $summary->channel_order_number,
            'etailer_order_number' => $summary->etailer_order_number,
            'date' => date("Y-m-d H:i:s", strtotime('now')),
            'action' => 'WH Assigned.',
            'details' => 'WH Assigned for ETIN: ' . $orderDetail->ETIN  . ' is: ' . $orderDetail->warehouse,
            'user' => 'Auto Process',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        return;
    }

    private function checkETINAndAssignFlag($orders, $client_id, $channel_id, $order_number) {

        Log::channel('IncomingOrderProcessing')->info('Starting Re-Checking Flags for Order Number: ' . $order_number);
        LogIncommingOrderProcessing([
            'req' => 
            'Starting Re-Checking Flags for Order Number: ' . $order_number
        ]);
        foreach($orders as $order) {
            
            if (in_array($order->ETIN_flag, [0, 1, 2])) { continue; }
            $etin = $order->ETIN;
            $ifmptexsists = DB::table('master_product')
            ->where(function($query) use($etin) {
					$query->where('ETIN', $etin)
						->orWhereRaw('FIND_IN_SET("'.$etin.'", product_listing_ETIN)')
						->orWhereRaw('FIND_IN_SET("'.$etin.'", alternate_ETINs)');
				})->whereRaw('FIND_IN_SET("'.$client_id.'", lobs )')->get();
            
            $checkEtin = DB::table('master_product')->where('ETIN', $etin)->get();
            $checkProductListEtin = DB::table('master_product')->whereRaw('FIND_IN_SET("'.$etin.'", product_listing_ETIN)')->get();
            $checkAlternateEtin = DB::table('master_product')->whereRaw('FIND_IN_SET("'.$etin.'", alternate_ETINs)')->get();

            if(isset($checkEtin) && count($checkEtin) > 0){
                $ETIN_flag = 0;
                $order->ETIN = $checkEtin[0]->ETIN;
			} else if(isset($checkProductListEtin) && count($checkProductListEtin) > 0){
                $ETIN_flag = 1;
                $order->ETIN = $checkProductListEtin[0]->ETIN;
			} else if(isset($checkAlternateEtin) && count($checkAlternateEtin) > 0){
                $ETIN_flag = 2;
                $order->ETIN = $checkAlternateEtin[0]->ETIN;
			} else {
                $ETIN_flag = 3;
			}
            
            $flag = $this->checkAndReturnEtinFlagForExclusionAndDne($client_id, $channel_id, 
            $ifmptexsists->isEmpty() ? null : $ifmptexsists[0]->id, $etin);
            
            $order->ETIN_flag = $flag == -1 ? $ETIN_flag : $flag;
            $order->status = $flag == -1 ? '1' : '17';
            $order->save();
        }
        Log::channel('IncomingOrderProcessing')->info('Re-Checking Flags for Order Number: ' . $order_number . ' complete.');
        LogIncommingOrderProcessing([
            'req' => 
            'Re-Checking Flags for Order Number: ' . $order_number . ' complete.'
        ]);
    }
    
    public function checkAndReturnEtinFlagForExclusionAndDne($client_id, $channel_id, $master_product_id, $sku) {

        $channel = ClientChannelConfiguration::where('id', $channel_id)->first();

		Log::channel('IncomingOrderProcessing')->info('In Check for exclusion');
		LogIncommingOrderProcessing([
            'req' => 
            'In Check for exclusion'
        ]);
		Log::channel('IncomingOrderProcessing')->info('Params: Client|Channel|MP_ID|SKU - ' . $client_id . '|' . $channel->id . '|' . $master_product_id . '|' . $sku);
        LogIncommingOrderProcessing([
            'req' => 
            'Params: Client|Channel|MP_ID|SKU - ' . $client_id . '|' . $channel->id . '|' . $master_product_id . '|' . $sku
        ]);
		$exclusions = null;
		if (!isset($master_product_id) || $master_product_id === '') {
			Log::channel('IncomingOrderProcessing')->info('MP Not found. Checking SKU Exclusion: ' . $sku);
            LogIncommingOrderProcessing([
                'req' => 
                'MP Not found. Checking SKU Exclusion: ' . $sku
            ]);
			$exclusions = SkuOrderExclusion::where('client_id', $client_id)
				->where('channel_id', $channel->id)->where('sku', $sku)
				->get();
		} else {
			Log::channel('IncomingOrderProcessing')->info('MP found. Checking ETIN Exclusion. ' . $master_product_id);
            LogIncommingOrderProcessing([
                'req' => 
                'MP found. Checking ETIN Exclusion. ' . $master_product_id
            ]);
			$exclusions = SkuOrderExclusion::where('client_id', $client_id)
				->where('channel_id', $channel->id)->where('master_product_id', $master_product_id)
				->get();
		}		
		Log::channel('IncomingOrderProcessing')->info('Exclusions: ' . count($exclusions));
		LogIncommingOrderProcessing([
            'req' => 
            'Exclusions: ' . count($exclusions)
        ]);
		// SKU found in exclusion List
		if (isset($exclusions) && count($exclusions) > 0) return 4;
		
		// SKU is not present in exclusion list, checking for DNE
		Log::channel('IncomingOrderProcessing')->info('DNE: ' . $channel->channel);
        LogIncommingOrderProcessing([
            'req' => 
            'DNE: ' . $channel->channel
        ]);
		if ($channel->is_dne === 1 && !isset($master_product_id)) return 5;
		
		Log::channel('IncomingOrderProcessing')->info('Out Check for exclusion');
        LogIncommingOrderProcessing([
            'req' => 
            'Out Check for exclusion'
        ]);
		// Channel is DNE disabled or product found in MPT
		return -1;
	}
}
