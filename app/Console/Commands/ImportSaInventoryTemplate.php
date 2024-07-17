<?php

namespace App\Console\Commands;

use phpseclib3\Net\SFTP;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use phpseclib3\Crypt\PublicKeyLoader;
use App\Client;
use App\OrderDetail;
use App\OrderSummary;
use App\ClientChannelConfiguration;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\Repositories\NotificationRepository;
use App\SkuOrderExclusion;

class ImportSaInventoryTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:import_sa_inventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
	private $customer_email;
	private $customer_phone;
	private $sku;
	private $sa_line_number;
	private $mp_order_number;
	
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
        Log::channel('ImportSaInventoryTemplate')->info('**** Starting Importing Orders ****');

        $fol = 'orders' . DIRECTORY_SEPARATOR;        
        if (!file_exists(public_path($fol))) {
            mkdir(public_path($fol));
        }
        
        $key_path = public_path('inventory/cranium_sftp.ppk');
        $key = PublicKeyLoader::load(file_get_contents($key_path), 'cranium');

        $host = 's-566f8c66397647d1b.server.transfer.us-east-2.amazonaws.com';
        $user = 'StoreAutomator';

        define('NET_SFTP_LOGGING', SFTP::LOG_COMPLEX);
        
        $sftp = new SFTP($host);
        
        if (!$sftp->login($user, $key)){
            Log::channel('ImportSaInventoryTemplate')->info('SFTP login Failed');
        }

        Log::channel('ImportSaInventoryTemplate')->info('Loged in successfully');
        // $sftp->chdir('Orders/FromSA');
		$sftp->chdir('Orders/FromSA');
        
        $elements = $sftp->nlist('.');
		//dd($elements);
        $latestModTime = 0;
        $filePath = "";
        $fileName = "";
		$fileFound = 0;
        foreach ($elements as $element) {
			// if (str_contains(strtolower($element), 'orders.csv') 
            //             && $sftp->is_file('/cranium-sftp-s3/Orders/FromSA/' . $element)) {
            if ( str_contains(strtolower($element), 'orders_')  
                        && $sftp->is_file('/cranium-sftp-s3/Orders/FromSA/' . $element)) {
						
							
							// $time = $sftp->filemtime('/cranium-sftp-s3/Orders/FromSA/' . $element);
							$time = $sftp->filemtime('/cranium-sftp-s3/Orders/FromSA/' . $element);
                if ($time > $latestModTime) {
                    $latestModTime = $time;
					// $filePath = '/cranium-sftp-s3/Orders/FromSA/' . $element;
                    $filePath = '/cranium-sftp-s3/Orders/FromSA/' . $element;
                    $fileName = $element;
					
					$fileFound = 1;
                }
            }
        }
		if($fileFound != 1){				
			Log::channel('ImportSaInventoryTemplate')->info('No UnProcessed CSV File found in Order Folder..');
			$error_log[] = 'No UnProcessed CSV File found in Order Folder..';
			exit;
		}
					
        $downFile = $fol . $fileName;
        $sftp->get($filePath, public_path($downFile));

        $data = Excel::toArray([], public_path($downFile));
		$maincount = $data[0];
		$count = count($maincount);
        $headers = $data[0][0];
		$error_log = null;
		$total_price = 0;
		$client_id = NULL;
		$customer_paid_price = 0;

		for ($j = 1; $j < $count; $j++){
			$values = $data[0][$j];
			$customer_email = '';
			$customer_phone = '';
			$sku = '';
			$sa_line_number = '';
			$mp_order_number = '';
			$is_client_on_hold = false;
			
			
			// Insert into sa_incoming_order_template table
			$rowid = DB::table('sa_incoming_order_template')->insertGetId(['file_path' => $downFile, 'created_at' => date("Y-m-d H:i:s", strtotime('now')), 'updated_at' => date("Y-m-d H:i:s", strtotime('now'))]);
			for ($i = 0; $i < count($headers); $i++) {
				$rowName = $headers[$i];
				$rowVal = $values[$i];
				if($rowVal !=''){
						DB::table('sa_incoming_order_template')->where('id', $rowid)->update([$rowName => $rowVal]);
				}
				if($rowName == 'customer_email'){
					$customer_email = $rowVal;
				} else if($rowName == 'customer_phone'){
					$customer_phone = $rowVal;
				} else if($rowName == 'sku'){
					$sku = $rowVal;
				} else if($rowName == 'sa_line_number'){
					$sa_line_number = $rowVal;
				}
				
			}
				
			$sa_incoming_order_table = DB::table('sa_incoming_order_template')->where('id', $rowid)->get();
			
			if(isset($sa_incoming_order_table[0]->purchase_date)){
				$sa_incoming_order_table[0]->purchase_date = $this->changeTimeFormat($sa_incoming_order_table[0]->purchase_date);
			}
			if(isset($sa_incoming_order_table[0]->estimated_ship_date)){
				$sa_incoming_order_table[0]->estimated_ship_date = $this->changeTimeFormat($sa_incoming_order_table[0]->estimated_ship_date);
			}
			if(isset($sa_incoming_order_table[0]->estimated_delivery_date)){
				$sa_incoming_order_table[0]->estimated_delivery_date = $this->changeTimeFormat($sa_incoming_order_table[0]->estimated_delivery_date);
			}

			// Add User Info into ship_to_customer table
			$this->updateShipToUserTable($rowid, $customer_email, $customer_phone, $sa_incoming_order_table);

			// Find last order number
			$last_order = OrderSummary::orderBy('id', 'desc')->first();
			$last_order_number = !$last_order ? 9999 : $last_order->etailer_order_number;

			Log::channel('ImportSaInventoryTemplate')->info('Last order number: '.$last_order_number);

			// Insert into order_summary table
			
			if(isset($ship_to_customer_exsists[0]->customer_number)){
				$customer_number = $ship_to_customer_exsists[0]->customer_number;
			} else {
				$customer_number = $sa_incoming_order_table[0]->customer_phone;
			}

			/*$find_client = ClientChannelConfiguration::where('channel', $sa_incoming_order_table[0]->marketplace_channel)->first();
			//dd($find_client);
			$client = Client::find($find_client->client_id);

			if($client){
				$client_id = $client->id;
			}*/
			
			Log::channel('ImportSaInventoryTemplate')->info('Channel Name: ' . $sa_incoming_order_table[0]->marketplace_name);
			$find_client = ClientChannelConfiguration::where('channel', $sa_incoming_order_table[0]->marketplace_name)->first();
			if($find_client){
				$client = Client::find($find_client->client_id);
				if($client){
					$client_id = $client->id;
					if ($client->is_enable == 2) {
						$is_client_on_hold = true;
					} 
					// else if ($find_client->isactive == 0) {
					// 	$is_client_on_hold = true;
					// }
					Log::channel('ImportSaInventoryTemplate')->info('Client: ' . $client->company_name . ' is disabled. The order will be om hold.');
				} else {
					Log::channel('ImportSaInventoryTemplate')->info('Channel Name: ' . $sa_incoming_order_table[0]->marketplace_name . ' has invalid Client. Terminating.');
					return -1;
				}
			} else {
				Log::channel('ImportSaInventoryTemplate')->info('Channel Name: ' . $sa_incoming_order_table[0]->marketplace_name . ' is invalid. Terminating.');
				return -1;
			}			

			// $find_client = Client::where('company_name', $sa_incoming_order_table[0]->marketplace_name)->first();
			// if($find_client){
			// 	$client_id = $client->id;
			// }
			// else{
			// 	$find_client = ClientChannelConfiguration::where('channel', $sa_incoming_order_table[0]->marketplace_channel)->first();
			// 	if($find_client){
			// 		$client = Client::find($find_client->client_id);

			// 		if($client){
			// 			$client_id = $client->id;
			// 		}
			// 	}

			// }
			
			if(strtolower($sa_incoming_order_table[0]->shipping_country_code) == 'usa' || strtolower($sa_incoming_order_table[0]->shipping_country_code) == 'united state'){
				$sa_incoming_order_table[0]->shipping_country_code = 'US';
			}

			if($sa_incoming_order_table[0]->marketplace_channel == 'shopify'){
				$mp_order_number = $sa_incoming_order_table[0]->mp_reference_order_number;
			}
			else{
				$mp_order_number = $sa_incoming_order_table[0]->mp_order_number;
			}

			$ifOrderSummaryExsists = 
				OrderSummary::where('channel_order_number', $mp_order_number)
				->where('sa_order_number', $sa_incoming_order_table[0]->sa_order_number)
				->where('order_status', '!=', 24)
				->get();

			if ($ifOrderSummaryExsists->isEmpty() ){
				$order_number = $last_order_number + 1;
				Log::channel('ImportSaInventoryTemplate')->info('Order Insert Number: ' . $order_number);
				DB::table('order_summary')->insert([				
					'etailer_order_number' => $order_number,
					'channel_order_number' => $mp_order_number,
					'sa_order_number' => $sa_incoming_order_table[0]->sa_order_number?$sa_incoming_order_table[0]->sa_order_number:NULL,
					'order_source' => $sa_incoming_order_table[0]->marketplace_name,
					'channel_type' => $sa_incoming_order_table[0]->marketplace_channel,
					'purchase_date' => $sa_incoming_order_table[0]->purchase_date,
					'customer_number' => $customer_number,
					'customer_name' => $sa_incoming_order_table[0]->customer_full_name,
					'customer_email' => $sa_incoming_order_table[0]->customer_email,
					'customer_phone' => $sa_incoming_order_table[0]->customer_phone,
					'ship_to_name' => $sa_incoming_order_table[0]->shipping_full_name,
					'ship_to_address_type' => $sa_incoming_order_table[0]->shipping_address_type,
					'ship_to_address1' => $sa_incoming_order_table[0]->shipping_address1,
					'ship_to_address2' => $sa_incoming_order_table[0]->shipping_address2,
					'ship_to_address3' => $sa_incoming_order_table[0]->shipping_address3,
					'ship_to_city' => $sa_incoming_order_table[0]->shipping_city,
					'ship_to_state' => $sa_incoming_order_table[0]->shipping_state,
					'ship_to_zip' => $sa_incoming_order_table[0]->shipping_postal_code,
					'ship_to_country' => $sa_incoming_order_table[0]->shipping_country_code,
					'ship_to_phone' => $sa_incoming_order_table[0]->shipping_phone?$sa_incoming_order_table[0]->shipping_phone:'8668586380',
					'shipping_method' => $sa_incoming_order_table[0]->shipping_method,
					'delivery_notes' => $sa_incoming_order_table[0]->delivery_notes,
					'customer_shipping_price' => $sa_incoming_order_table[0]->quantity * ($sa_incoming_order_table[0]->unit_price - $sa_incoming_order_table[0]->discount),
					'gift_message' => $sa_incoming_order_table[0]->gift_message,
					'sales_tax' => $sa_incoming_order_table[0]->sales_tax,
					'shipping_tax' => $sa_incoming_order_table[0]->shipping_tax,
					'shipping_discount_type' => $sa_incoming_order_table[0]->shipping_discount_name,
					'shipping_discount_amount' => $sa_incoming_order_table[0]->shipping_discount,
					'channel_estimated_ship_date' => $sa_incoming_order_table[0]->estimated_ship_date,
					'channel_estimated_delivery_date' => $sa_incoming_order_table[0]->estimated_delivery_date,
					'is_amazon_prime' => $sa_incoming_order_table[0]->is_amazon_prime,
					'paypal_transaction_ids' => $sa_incoming_order_table[0]->paypal_transaction_ids,
					'customer_vat' => $sa_incoming_order_table[0]->customer_vat,
					'currency' => $sa_incoming_order_table[0]->currency,
					//'ship_by_date' => $sa_incoming_order_table[0]->shipping_phone,
					//'order_total_price' => '',
					'order_status' => $is_client_on_hold ? '10' : '1',
					//'complete_date' => $sa_incoming_order_table[0]->shipping_phone,
					'created_at' => date("Y-m-d H:i:s", strtotime('now')),
					'updated_at' => date("Y-m-d H:i:s", strtotime('now')),
					'client_id' => $client_id,
					'channel_id' => $find_client->id
				]);

				if ($is_client_on_hold) {
					DB::table('order_history')->insert([
						'mp_order_number' => $sa_incoming_order_table[0]->mp_order_number,
						'etailer_order_number' => $order_number,
						'date' => date("Y-m-d H:i:s", strtotime('now')),
						'action' => 'Order On Hold',
						'details' => 'Due to Client/Channel On Hold, Order status is on Hold now',
						'user' => 'Auto Process',
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s")
					]);
				}

				/* Notify other admins */		
				$note = $last_order_number + 1;
				$note .= ' New Etailer Order Placed';
				$url_id = '';

				$order = OrderSummary::where('etailer_order_number', $last_order_number + 1)->first();
				if($order){
					$url_id = $order->id;
					$url = '/summery_orders/'.$url_id.'/view';
					$type = "New Order";
					$this->NotificationRepository->SendOrderNotification([
						'subject' => $type,
						'body' => $note,
						'order_number' => $order->etailer_order_number
					]);

					UpdateOrderHistory([
						'order_number' => $last_order_number + 1,
						'title' => 'Order Created',
						'detail' => "Order # ".($last_order_number + 1)." Has been Placed from SA Inventory",
						'reference' => 'Cron',
						'extras' => json_encode($order)
					]);
				}

                	
				
			} else {
				
				$neworder = $last_order_number + 1;
				
				$exsisting_order = OrderSummary::where('channel_order_number', $sa_incoming_order_table[0]->mp_order_number)->first();

				if ( ! isset($exsisting_order->etailer_order_number)){
					$previous_price = 0;
				} else {
					$previous_price = $exsisting_order->customer_shipping_price;
				}
				$customer_shipping_price = $previous_price + ($sa_incoming_order_table[0]->quantity * ($sa_incoming_order_table[0]->unit_price - $sa_incoming_order_table[0]->discount));
				
				OrderSummary::where('channel_order_number', $sa_incoming_order_table[0]->mp_order_number)->update([
					'customer_shipping_price' => $customer_shipping_price,
					'order_total_price' => $customer_paid_price,
					'updated_at' => date("Y-m-d H:i:s", strtotime('now')),
					'client_id' => $client_id,
				]);
				
			}
				
			// Insert into order_detail table
			$ifmptexsists = DB::table('master_product')
				->where(function($query) use($sku) {
					$query->where('ETIN', $sku)
						->orWhereRaw('FIND_IN_SET("'.$sku.'", product_listing_ETIN)')
						->orWhereRaw('FIND_IN_SET("'.$sku.'", alternate_ETINs)');
				})->whereRaw('FIND_IN_SET("'.$client_id.'", lobs )')->get();
			
			if($ifmptexsists->isEmpty()){
				Log::channel('ImportSaInventoryTemplate')->info('Product ETIN : '.$sku.' is not exisits in MPT.');
				//continue;
				$etin = $sa_incoming_order_table[0]->sku;
				$etailer_product_name = $sa_incoming_order_table[0]->product_name;
				$etailer_channel_price = $sa_incoming_order_table[0]->unit_price * $sa_incoming_order_table[0]->quantity;
			} else {
				$etin = $ifmptexsists[0]->ETIN;
				$etailer_product_name = $ifmptexsists[0]->product_listing_name ;
				$etailer_channel_price = $ifmptexsists[0]->cost * $sa_incoming_order_table[0]->quantity;
			}
			$checkEtin = DB::table('master_product')->where('ETIN', $sku)->get();
			$checkProductListEtin = DB::table('master_product')->whereRaw('FIND_IN_SET("'.$sku.'", product_listing_ETIN)')->get();
			$checkAlternateEtin = DB::table('master_product')->whereRaw('FIND_IN_SET("'.$sku.'", alternate_ETINs)')->get();

			$flag = $this->checkAndReturnEtinFlagForExclusionAndDne($client, 
					$find_client, $ifmptexsists->isEmpty() ? null : $ifmptexsists[0]->id, $sa_incoming_order_table[0]->sku);
			Log::channel('ImportSaInventoryTemplate')->info('Flag: ' . $flag);

			if(isset($checkEtin)){
				$ETIN_flag = 0;
			} else if(isset($checkProductListEtin)){
				$ETIN_flag = 1;
			} else if(isset($checkAlternateEtin)){
				$ETIN_flag = 2;
			} else {
				$ETIN_flag = 3;
			}
			
			$channel_extended_price = $sa_incoming_order_table[0]->quantity * $sa_incoming_order_table[0]->unit_price;
			$customer_paid_price =  $sa_incoming_order_table[0]->quantity * ($sa_incoming_order_table[0]->unit_price - $sa_incoming_order_table[0]->discount);

			$total_price += $customer_paid_price;
			$last_OrderSummary = OrderSummary::latest('id')->first();
			
			if($last_OrderSummary != null){
				
				if($last_OrderSummary->customer_email == $sa_incoming_order_table[0]->customer_email){
					$orderid = $last_OrderSummary->etailer_order_number;
				} else {
					$orderid = $last_order_number + 1;
				}
			} else {
				$orderid = $last_order_number + 1;
			}

			$orderDetailsCount = OrderDetail::where('order_number', $orderid)->where('ETIN', $etin)->count();

			if($orderDetailsCount == 0){
				$status = '1';
				if ($is_client_on_hold) { $status = '18'; }
				if ($flag != -1) { $status = '17'; }
				Log::channel('ImportSaInventoryTemplate')->info('Inseting Order Details. Order Number: '. $orderid .'.  ETIN: ' . $etin . '. Date: ' . date("Y-m-d H:i:s", strtotime("now")));
				DB::table('order_details')->insert([
					'order_number' => $orderid,
					'ETIN' => $etin,
					'SA_line_number' => $sa_incoming_order_table[0]->mp_line_number,
					'SA_sku' => $sa_incoming_order_table[0]->sku,
					'channel_product_name' => $sa_incoming_order_table[0]->product_name,
					'etailer_product_name' => $etailer_product_name,
					'channel_unit_price' => $sa_incoming_order_table[0]->unit_price,
					'channel_extended_price' => $channel_extended_price,
					'etailer_channel_price' => $etailer_channel_price,
					'discount_name' => $sa_incoming_order_table[0]->discount_name,
					'customer_discount' => $sa_incoming_order_table[0]->discount,
					'customer_paid_price' => $customer_paid_price,
					'quantity_ordered' => $sa_incoming_order_table[0]->quantity,
					'ETIN_flag' => $flag == -1 ? $ETIN_flag : $flag,
					'status' => $status,
					'created_at' => date("Y-m-d H:i:s", strtotime('now')),
				]);
					
				// Insert into sa_order_conformation_template table
				
				DB::table('sa_order_conformation_template')->insert([
					'order_id' => $orderid,
					'sku' => $sa_incoming_order_table[0]->sku,
					'shippedCost' => $customer_paid_price,
					'created_at' => date("Y-m-d H:i:s", strtotime('now')),
					]);
			}			
			
		}
				
		// Move Proccessed Files to Preccessed Folder in FTP
		$this->moveFileToProcessedFolder($sftp, $filePath , $fileName, $downFile);
        return 0;
    }
	
	public function updateShipToUserTable($rowid, $customer_email, $customer_phone, $sa_incoming_order_table){
		
		$ship_to_customer_exsists = DB::table('ship_to_customer')->where('customer_email', $customer_email)->orWhere('customer_phone', $customer_phone)->get();
		
		$last_customer_number = DB::table('ship_to_customer')->max('customer_number');
			
			if(!$last_customer_number){
				$last_customer_number = 9999;
			}
			

		if(!$ship_to_customer_exsists->isEmpty()){
			
			$ifemailexsists = DB::table('ship_to_customer')->where('customer_email', $customer_email)->get();
			$ifphoneexsists = DB::table('ship_to_customer')->where('customer_phone', $customer_phone)->get();
			if(!$ifemailexsists->isEmpty()){
				DB::table('ship_to_customer')->where('customer_email', $sa_incoming_order_table[0]->customer_email)->update([
					//'customer_number' => $last_customer_number + 1,
					'customer_full_name' => $sa_incoming_order_table[0]->customer_full_name,
					//'customer_email' => $sa_incoming_order_table[0]->customer_email,
					'customer_phone' => $sa_incoming_order_table[0]->customer_phone,
					'shipping_full_name' => $sa_incoming_order_table[0]->shipping_full_name,
					'shipping_address_type' => $sa_incoming_order_table[0]->shipping_address_type,
					'shipping_address1' => $sa_incoming_order_table[0]->shipping_address1,
					'shipping_address2' => $sa_incoming_order_table[0]->shipping_address2,
					'shipping_address3' => $sa_incoming_order_table[0]->shipping_address3,
					'shipping_city' => $sa_incoming_order_table[0]->shipping_city,
					'shipping_state' => $sa_incoming_order_table[0]->shipping_state,
					'shipping_postal_code' => $sa_incoming_order_table[0]->shipping_postal_code,
					'shipping_country_code' => $sa_incoming_order_table[0]->shipping_country_code,
					'shipping_phone' => $sa_incoming_order_table[0]->shipping_phone,
					'updated_at' => date("Y-m-d H:i:s", strtotime('now')),
				]);
			
				Log::channel('ImportSaInventoryTemplate')->info('Email id: '.$customer_email.' already exsists in ship_to_customer table');
				$error_log[] = 'Email id: '.$customer_email.' already exsists in ship_to_customer table';
			} elseif(!$ifphoneexsists->isEmpty()){
				DB::table('ship_to_customer')->where('customer_phone', $sa_incoming_order_table[0]->customer_phone)->update([
					//'customer_number' => $last_customer_number + 1,
					'customer_full_name' => $sa_incoming_order_table[0]->customer_full_name,
					'customer_email' => $sa_incoming_order_table[0]->customer_email,
					//'customer_phone' => $sa_incoming_order_table[0]->customer_phone,
					'shipping_full_name' => $sa_incoming_order_table[0]->shipping_full_name,
					'shipping_address_type' => $sa_incoming_order_table[0]->shipping_address_type,
					'shipping_address1' => $sa_incoming_order_table[0]->shipping_address1,
					'shipping_address2' => $sa_incoming_order_table[0]->shipping_address2,
					'shipping_address3' => $sa_incoming_order_table[0]->shipping_address3,
					'shipping_city' => $sa_incoming_order_table[0]->shipping_city,
					'shipping_state' => $sa_incoming_order_table[0]->shipping_state,
					'shipping_postal_code' => $sa_incoming_order_table[0]->shipping_postal_code,
					'shipping_country_code' => $sa_incoming_order_table[0]->shipping_country_code,
					'shipping_phone' => $sa_incoming_order_table[0]->shipping_phone,
					'updated_at' => date("Y-m-d H:i:s", strtotime('now')),
				]);
				Log::channel('ImportSaInventoryTemplate')->info('Phone Number: '.$customer_phone.' already exsists in ship_to_customer table');
				$error_log[] = 'Phone Number: '.$customer_phone.' already exsists in ship_to_customer table';
			}
		} else {
			DB::table('ship_to_customer')->insert([
				
				'customer_number' => $last_customer_number + 1,
				'customer_full_name' => $sa_incoming_order_table[0]->customer_full_name,
				'customer_email' => $sa_incoming_order_table[0]->customer_email,
				'customer_phone' => $sa_incoming_order_table[0]->customer_phone,
				'shipping_full_name' => $sa_incoming_order_table[0]->shipping_full_name,
				'shipping_address_type' => $sa_incoming_order_table[0]->shipping_address_type,
				'shipping_address1' => $sa_incoming_order_table[0]->shipping_address1,
				'shipping_address2' => $sa_incoming_order_table[0]->shipping_address2,
				'shipping_address3' => $sa_incoming_order_table[0]->shipping_address3,
				'shipping_city' => $sa_incoming_order_table[0]->shipping_city,
				'shipping_state' => $sa_incoming_order_table[0]->shipping_state,
				'shipping_postal_code' => $sa_incoming_order_table[0]->shipping_postal_code,
				'shipping_country_code' => $sa_incoming_order_table[0]->shipping_country_code,
				'shipping_phone' => $sa_incoming_order_table[0]->shipping_phone,
				'created_at' => date("Y-m-d H:i:s", strtotime('now')),
			]);
			
		}		
	}
	
	public function moveFileToProcessedFolder($sftp, $filePath, $fileName, $downFile){
		$path = public_path('temp');
		 if (!file_exists($path)) {
			mkdir($path);
		}	
		
		// $needToMovePath = '/cranium-sftp-s3/Orders/FromSA/Processed/'.$fileName;        
		$needToMovePath = '/cranium-sftp-s3/Orders/FromSA/Processed/'.$fileName;        
		$tempFilePath = 'public/temp/'.$fileName;
		
		$sftp->get($filePath, $tempFilePath);
		$sftp->put($needToMovePath, $tempFilePath, SFTP::SOURCE_LOCAL_FILE);
		$sftp->delete($filePath);
		unlink($tempFilePath);
	}
	
	public function changeTimeFormat($time){
		$changedTime = date("Y-m-d H:i:s", strtotime($time));
		return $changedTime;		
	}

	public function checkAndReturnEtinFlagForExclusionAndDne($client_id, $channel, $master_product_id, $sku) {

		Log::channel('ImportSaInventoryTemplate')->info('In Check for exclusion');
		
		Log::channel('ImportSaInventoryTemplate')->info('Params: Client|Channel|MP_ID|SKU - ' . $client_id->id . '|' . $channel->id . '|' . $master_product_id . '|' . $sku);
		$exclusions = null;
		if (!isset($master_product_id) || $master_product_id === '') {
			Log::channel('ImportSaInventoryTemplate')->info('MP Not found. Checking SKU Exclusion: ' . $sku);
			$exclusions = SkuOrderExclusion::where('client_id', $client_id->id)
				->where('channel_id', $channel->id)->where('sku', $sku)
				->get();
		} else {
			Log::channel('ImportSaInventoryTemplate')->info('MP found. Checking ETIN Exclusion. ' . $master_product_id);
			$exclusions = SkuOrderExclusion::where('client_id', $client_id->id)
				->where('channel_id', $channel->id)->where('master_product_id', $master_product_id)
				->get();
		}		
		Log::channel('ImportSaInventoryTemplate')->info('Exclusions: ' . count($exclusions));
		
		// SKU found in exclusion List
		if (isset($exclusions) && count($exclusions) > 0) return 4;
		
		// SKU is not present in exclusion list, checking for DNE
		Log::channel('ImportSaInventoryTemplate')->info('DNE: ' . $channel->channel);
		if ($channel->is_dne === 1 && !isset($master_product_id)) return 5;
		
		Log::channel('ImportSaInventoryTemplate')->info('Out Check for exclusion');

		// Channel is DNE disabled or product found in MPT
		return -1;
	}
}
