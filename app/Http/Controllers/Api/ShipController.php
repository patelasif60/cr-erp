<?php

namespace App\Http\Controllers\Api;

use DB;
use App\User;
use Exception;
use App\Carrier;
use App\OrderDetail;
use App\OrderPackage;
use App\OrderSummary;
use Illuminate\Http\Request;
use App\PackagingcomponentsSetting;
use App\OrderPickAndPack;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderDetail\OrderDetailResource;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SFTP;
use Excel;
use App\Export\SATrackingExport;

class ShipController extends Controller
{

    public function GetBarcodeInfo(Request $request,$barcode)
    {
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        try {
            $bar = explode('-', $barcode);
            $orderNumber = $bar[0];
            if (!isset($bar[1])) {
                return response(
                    [
                        "error" => true,
                        'message' => 'Barcode Format is not corrent'
                    ],
                    400
                );
            }
            $package = $bar[1];
            $orderNumbers = explode(".", $orderNumber);
            $orderNo = $orderNumbers[0];
            $orderSummary = OrderSummary::with('client')->where('etailer_order_number', $orderNo)->first();
            if (!$orderSummary) {
                return response(
                    [
                        "error" => true,
                        'message' => 'Order Not Found.'
                    ],
                    400
                );
            }

            $os = OrderDetail::with('carrier_account', 'carrier_service', 'carrier')->where('sub_order_number', $orderNumber)->get();
            if (count($os) == 0) {
                return response(
                    [
                        "error" => true,
                        'message' => 'Sub Order Not Found.'
                    ],
                    400
                );
            }

            $package_info = OrderPackage::with('packaging_material')->where('order_id', $orderNumber)->where('package_num', $package)->get();
            if (count($package_info) == 0) {
                return response(
                    [
                        "error" => true,
                        'message' => 'No package info found'
                    ],
                    400
                );
            }


            $GetAllPickedItems = OrderPickAndPack::where('sub_order_number', $orderNumber)->where(function($q) {
                $q->whereColumn('pick_qty','<>', 'pack_qty');
                $q->orWhereNull('pack_qty');
            })->get()->toArray();
            if(count($GetAllPickedItems) > 0){
                return response(
                    [
                        "error" => true,
                        'message' => 'Please pack all the items first'
                    ],
                    400
                );
            }

            $toReturn['package_info'] = $package_info;
            $toReturn['order_detail'] = $os;
            $toReturn['order_summery'] = $orderSummary;

            $child_component = OrderPackage::where('order_id', $orderNumber)->where('package_num', $package)->groupBy('box_used')->pluck('box_used');
            $Child_Component = PackagingcomponentsSetting::with('PackagingMaterials', 'ProductTemperature')->whereIN('parent_packaging_material_id', $child_component)->get();
            $toReturn['Child_Component'] = $Child_Component;

            
            UserLogs([
                'user_id' => $user_id,
                'action' => 'Scan',
                'task' => 'Ship',
                'details' => 'Barcode # '.$barcode.' has been scanned to be shipped',
                'type' => 'CWMS',
                'sub_order_number' => $orderNumber,
                'etailer_order_number' => $orderSummary->etailer_order_number,
                'channel_order_number' => $orderSummary->channel_order_number,
                'client_order_number' => $orderSummary->sa_order_number
            ]);

            return response(
                [
                    "error" => false,
                    'data' => $toReturn
                ],
                200
            );
        } catch (Exception $ex) {
            return response(
                [
                    "error" => true,
                    'message' => $ex->getMessage()
                ],
                400
            );
        }
    }

    public function CreateLabel(Request $request)
    {
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $barcode = $request->barcode;
        $weight = $request->weight;
        $dry_ice_pallet_Lb = $request->dry_ice_pallet_Lb;
        $dry_ice_block_Lb = $request->dry_ice_block_Lb;

        $total_ice_weight = $dry_ice_pallet_Lb + $dry_ice_block_Lb;
        list($orderNumber, $package) = explode('-', $barcode);
        $orderNumbers = explode(".", $orderNumber);
        $orderNo = $orderNumbers[0];

        if($total_ice_weight > $weight){
            return response(
                [
                    "error" => true,
                    'message' => 'Ice weight should not be higher than package weight'
                ],
                400
            );
        }

        $orderSummary = OrderSummary::with('client')->where('etailer_order_number', $orderNo)->first();
        if (!$orderSummary) {
            return response(
                [
                    "error" => true,
                    'message' => 'Order Not Found.'
                ],
                400
            );
        }

        $order_detail = OrderDetail::where('sub_order_number', $orderNumber)->get();
        if (count($order_detail) == 0) {
            return response(
                [
                    "error" => true,
                    'message' => 'Sub Order Not Found.'
                ],
                400
            );
        }

        $package_info = OrderPackage::with('packaging_material')->where('order_id', $orderNumber)->where('package_num', $package)->first();
        if (!$package_info) {
            return response(
                [
                    "error" => true,
                    'message' => 'No package info found'
                ],
                400
            );
        }

        $packaging_material = $package_info->packaging_material;
        $external_length = $packaging_material->external_length;
        $external_width = $packaging_material->external_width;
        $external_height = $packaging_material->external_height;

        

        

        $curl = curl_init();

        $first_order_detail = $order_detail[0];

        if (!isset($first_order_detail->carrier_account->account_number)) {
            return response(
                [
                    "error" => true,
                    'message' => 'Carrier account is empty in this Sub Order'
                ],
                400
            );
        }

        // if (!isset($first_order_detail->carrier_service->api_code)) {
        //     return response(
        //         [
        //             "error" => true,
        //             'message' => 'Service is empty in this Sub Order'
        //         ],
        //         400
        //     );
        // }

        $carrier = Carrier::where('id', $first_order_detail->carrier_id)->first();
        $carrier_name = "ups";
        if (isset($carrier)) {
            $carrier_name = strtolower($carrier->company_name);
        }

        $account_rules = $first_order_detail->carrier_account->account_rules;

        $label_image = '';
        $tracking_number = '';
        $response = '';

        if ($carrier_name === 'ups') {
            $req = '{
                "ShipmentRequest": {
                    "Shipment": {
                        "Shipper": {
                            "Name": "' . $orderSummary->client->company_name . '",
                            "Phone": {
                                "Number": "' . $first_order_detail->warehouse_info->phone_number . '"
                            },
                            "ShipperNumber": "' . $first_order_detail->carrier_account->account_number . '",
                            "Address": {
                                "AddressLine": "' . $first_order_detail->warehouse_info->address . '",
                                "City": "' . $first_order_detail->warehouse_info->city . '",
                                "StateProvinceCode": "' . $first_order_detail->warehouse_info->state . '",
                                "PostalCode": "' . $first_order_detail->warehouse_info->zipcode . '",
                                "CountryCode": "' . $first_order_detail->warehouse_info->country . '"
                            }
                        },
                        "ShipTo": {
                            "Name": "' . $orderSummary->ship_to_name . '",
                            "Phone": {
                                "Number": "' . $orderSummary->ship_to_phone . '"
                            },
                            "Address": {
                                "AddressLine": "' . $orderSummary->ship_to_address1 . ' '.($orderSummary->ship_to_address2 != '' ? ' ,'.$orderSummary->ship_to_address2 : '').'",
                                "City": "' . $orderSummary->ship_to_city . '",
                                "StateProvinceCode": "' . $orderSummary->ship_to_state . '",
                                "PostalCode": "' . $orderSummary->ship_to_zip . '",
                                "CountryCode": "' . $orderSummary->ship_to_country . '"
                            },
                            "AttentionName": "' . $orderSummary->ship_to_name . '"
                        },
                        "ShipFrom": {
                            "Name": "' . $orderSummary->client->company_name . '",
                            "Phone": {
                                "Number": "' . $first_order_detail->warehouse_info->phone_number . '"
                            },
                            "Address": {
                                "AddressLine": "' . $first_order_detail->warehouse_info->address . '",
                                "City": "' . $first_order_detail->warehouse_info->city . '",
                                "StateProvinceCode": "' . $first_order_detail->warehouse_info->state . '",
                                "PostalCode": "' . $first_order_detail->warehouse_info->zipcode . '",
                                "CountryCode": "' . $first_order_detail->warehouse_info->country . '"
                            },
                            "AttentionName": "' . $orderSummary->client->company_name . '"
                        },
                        "PaymentInformation": {
                            "ShipmentCharge": {
                                "Type": "01",
                                "BillShipper": {
                                    "AccountNumber": "' . $first_order_detail->carrier_account->account_number . '"
                                }
                                
                            }
                        },
                        "Service": {
                            "Code": "' . $first_order_detail->carrier_service->api_code . '",
                            "Description": "' . $first_order_detail->carrier_service->service_name . '"
                        },
                        "Package": [
                            {
                                "Description": "Customer Supplied Package",
                                "Dimensions":{
                                    "Length": "'.$external_length.'",
                                    "Width": "'.$external_width.'",
                                    "Height": "'.$external_height.'",
                                    "UnitOfMeasurement":{
                                        "Description":"Inches",
                                        "Code":"IN"
                                    }
                                },
                                "Packaging": {
                                    "Code": "02"
                                },
                                "PackageWeight": {
                                    "UnitOfMeasurement": {
                                        "Code": "LBS"
                                    },
                                    "Weight": "' . $weight . '"
                                },
                                "ReferenceNumber": [{
                                    "Code": "PO",
                                    "Value": "' . $orderNumber . '"
                                },
                                {
                                    "Code": "PO",
                                    "Value": "' . $orderSummary->order_source . '"
                                }]';
            if ($account_rules == 'Shipper Release') {
                $req .= ',
                                        "PackageServiceOptions": {
                                            "ShipperReleaseIndicator":""
                                        }';
            }

            
            if(in_array($first_order_detail->carrier_service->api_code, ['01', '13', '02', '14', '59']) && $total_ice_weight > 0){
                $req.=',
                "PackageServiceOptions": {
                    "DryIce": {
                        "RegulationSet": "CFR",
                        "DryIceWeight": {
                            "UnitOfMeasurement": {
                                "Code": "LBS"
                            },
                            "Weight": "'.$total_ice_weight.'"
                        }
                    }
                }';
            }
            $req .= '}
                        ],
                        "ItemizedChargesRequestedIndicator": "",
                        "RatingMethodRequestedIndicator": "",
                        "TaxInformationIndicator": "",
                        "ShipmentRatingOptions": {
                            "NegotiatedRatesIndicator": ""
                        }
                    },
                    "LabelSpecification": {
                        "LabelImageFormat": {
                            "Code": "PNG"
                        }
                    }
                }
            }';

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://onlinetools.ups.com/ship/v1/shipments?additionaladdressvalidation=city',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $req,
                CURLOPT_HTTPHEADER => array(
                    'Username: ' . config('ups.CREDENTIALS.Username') . '',
                    'Password: ' . config('ups.CREDENTIALS.Password') . '',
                    'AccessLicenseNumber: ' . config('ups.CREDENTIALS.AccessLicenseNumber') . '',
                    'Content-Type: application/json',
                    'Accept: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($response, true);
            DeveloperLog([
                'reference' => 'UPS',
                'ref_request' => $req,
                'ref_response' => json_encode($result)
            ]);  
            if (isset($result['response']['errors'])) {
                if($result['response']['errors'][0]['message'] == 'The selected service is not available from the origin to the destination.'){
                    if($first_order_detail->carrier_service->api_code == 13){
                        foreach($order_detail as $row){
                            $row->service_type_id = 3;
                            $row->save();
                        }
                    }
                }
                return response(
                    [
                        "error" => true,
                        'message' => $result['response']['errors'][0]['message'].' Shipment Type updated. Please re-run the create label process.'
                    ],
                    400
                );
            }
            if (isset($result['ShipmentResponse']['ShipmentResults']['PackageResults']['ShippingLabel']['GraphicImage'])) {
                $label_image = $result['ShipmentResponse']['ShipmentResults']['PackageResults']['ShippingLabel']['GraphicImage'];
            }

            if (isset($result['ShipmentResponse']['ShipmentResults']['PackageResults']['TrackingNumber'])) {
                $tracking_number = $result['ShipmentResponse']['ShipmentResults']['PackageResults']['TrackingNumber'];
            }
        } else {

            $receiver_info = [
                "name" => $orderSummary->ship_to_name,
                "phone" => $orderSummary->ship_to_phone,
                "company_name" => $orderSummary->ship_to_name,
                "address" => $orderSummary->ship_to_address1.($orderSummary->ship_to_address2 != '' ? ' ,'.$orderSummary->ship_to_address2 : '' ),
                "city" => $orderSummary->ship_to_city,
                "code" => $orderSummary->ship_to_state,
                "postal_code" => $orderSummary->ship_to_zip,
                "country" => $orderSummary->ship_to_country
            ];

            $shipper_info = [
                "name" => $orderSummary->client->company_name,
                "phone" => $first_order_detail->warehouse_info->phone_number,
                "company_name" => $orderSummary->client->company_name,
                "address" => $first_order_detail->warehouse_info->address,
                "city" => $first_order_detail->warehouse_info->city,
                "code" => $first_order_detail->warehouse_info->state,
                "postal_code" => $first_order_detail->warehouse_info->zipcode,
                "country" => $first_order_detail->warehouse_info->country
            ];

            $fedex = new FedexController();
            $responseFromApi = $fedex->generateLabel($shipper_info, $receiver_info,$package_info);
             
            if (!isset($responseFromApi['error'])) {

                $tracking_number = $responseFromApi['trackingNumber'];
                
                $label_image = $responseFromApi['label'];
                $label_image = $this->saveBase64ToPdf($label_image, $tracking_number);

                $response = $responseFromApi['response'];
            } else {
                $response = $responseFromApi;
            }
        }

        if ($tracking_number !== '' && $label_image !== '') {

            OrderPackage::with('packaging_material')->where('order_id', $orderNumber)->where('package_num', $package)->update([
                'etailer_weight' => $weight,
                'dry_ice_block_Lb' => $dry_ice_block_Lb,
                'dry_ice_pallet_Lb' => $dry_ice_pallet_Lb,
                'tracking_number' => $tracking_number,
                'shipping_response' => $response,
                'label_image' => $label_image,
                'shipping_label_creation_time' => date('Y-m-d H:i:s')
            ]);

            // $all_items = OrderPackage::with('packaging_material')->where('order_id', $orderNumber)->where('package_num', $package)->get();
            // if ($all_items) {
            //     foreach ($all_items as $row_items) {
            //         OrderDetail::where('ETIN', $row_items->ETIN)->whereIN('status',[1,2,3,4])->where('sub_order_number', $orderNumber)->update([
            //             'status' => 6
            //         ]);
            //         OrderDetail::where('ETIN', $row_items->ETIN)->whereIN('status',[9,10,11,12])->where('sub_order_number', $orderNumber)->update([
            //             'status' => 13
            //         ]);
            //     }
            //     $this->changeOrderSummaryStatusShipped($orderNumber);
            // }

            UserLogs([
                'user_id' => $user_id,
                'action' => 'Click',
                'task' => 'Ship',
                'details' => 'Label Created for Barcode# '.$barcode.'',
                'type' => 'CWMS',
                'sub_order_number' => $orderNumber,
                'etailer_order_number' => $orderSummary->etailer_order_number,
                'channel_order_number' => $orderSummary->channel_order_number,
                'client_order_number' => $orderSummary->sa_order_number
            ]);

            UpdateOrderHistory([
                'order_number' => $orderSummary->etailer_order_number,
                'sub_order_number' => $first_order_detail->sub_order_number,
                'detail' => 'Order #: '.$first_order_detail->sub_order_number.' Label created',
                'title' => 'Label Created',
                'user_id' => $user_id,
                'reference' => 'API',
                'extras' => json_encode($first_order_detail)
            ]);

            return response(
                [
                    "error" => false,
                    'data' => [
                        'tracking_number' => $tracking_number,
                        'shipping_response' => $response,
                        'label_image' => $label_image,
                    ],
                    'message' => 'Label created successfully'
                ],
                200
            );
        } else {
            return response(
                [
                    "error" => true,
                    'message' => 'Label creation unsuccessful',
                    'response' => $response
                ],
                500
            );
        }
    }

    private function changeOrderSummaryStatusShipped($subOrderNumber,$user_id,$package_info,$packages)
    {

        $order_info = OrderDetail::select('*')->where('sub_order_number', $subOrderNumber)->first();
        if ($order_info) {
            $total_orders = OrderDetail::where('order_number', $order_info->order_number)->whereNotIn('status', [17])->count();
            $shipped_orders = OrderDetail::where('order_number', $order_info->order_number)->whereIn('status', [6,13])->count();
            if ($total_orders == $shipped_orders) {
                OrderSummary::where('etailer_order_number', $order_info->order_number)->update([
                    'order_status' => 17
                ]);
                UpdateOrderHistory([
                    'order_number' => $order_info->order_number,
                    'sub_order_number' => $subOrderNumber,
                    'detail' => 'Order #: '.$order_info->order_number.' Has been Shipped',
                    'title' => 'Order Status Changed',
                    'user_id' => $user_id,
                    'reference' => 'API',
                    'extras' => json_encode($order_info)
                ]);
            } else {
                OrderSummary::where('etailer_order_number', $order_info->order_number)->update([
                    'order_status' => 18
                ]);
                UpdateOrderHistory([
                    'order_number' => $order_info->order_number,
                    'sub_order_number' => $subOrderNumber,
                    'detail' => 'Order #: '.$order_info->order_number.' Has been Partially Shipped',
                    'title' => 'Order Status Changed',
                    'user_id' => $user_id,
                    'reference' => 'API',
                    'extras' => json_encode($order_info)
                ]);
            }

            $this->SendTextTOSA($order_info,$packages);
        }
    }

    public function saveBase64ToPdf($string, $tracking_number) {

        if (!file_exists(public_path('fedex_label'))) {
            mkdir(public_path('fedex_label'));
        }

        $file_data = base64_decode($string);
        $file_path = 'fedex_label' . DIRECTORY_SEPARATOR . $tracking_number . '.pdf';

        file_put_contents(public_path($file_path), $file_data);

        return $file_path;
    }

    public function CompleteShipment(Request $request){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $barcode = $request->barcode;
        list($orderNumber, $package) = explode('-', $barcode);
        $orderNumbers = explode(".", $orderNumber);
        $orderNo = $orderNumbers[0];

        $all_items = OrderPackage::where('order_id', $orderNumber)->where('package_num', $package)->get();
        if ($all_items) {
            foreach ($all_items as $row_items) {
                OrderDetail::where(function($q) use($row_items){
                    $q->Where('ETIN', $row_items->ETIN);
                    $q->OrWhere('ETIN', $row_items->Order_ETIN);
                })->whereIN('status',[1,2,3,4])->where('sub_order_number', $orderNumber)->update([
                    'status' => 6
                ]);
                OrderDetail::where(function($q)  use($row_items){
                    $q->Where('ETIN', $row_items->ETIN);
                    $q->OrWhere('ETIN', $row_items->Order_ETIN);
                })->whereIN('status',[9,10,11,12])->where('sub_order_number', $orderNumber)->update([
                    'status' => 13
                ]);

                $date = date('Y-m-d');
                $day = '';

                $today = date('l', strtotime($date));
                if($today == 'Monday'){
                    $day = 1;
                }
                elseif($today == 'Tuesday'){
                    $day = 2;
                }
                elseif($today == 'Wednesday'){
                    $day = 3;
                }
                elseif($today == 'Thursday'){
                    $day = 4;
                }
                elseif($today == 'Friday'){
                    $day = 5;
                }

                $row_items->ship_date = $date;
                $row_items->ship_day = $day;
                $row_items->save();
            }
            $this->changeOrderSummaryStatusShipped($orderNumber,$user_id,$all_items[0],$all_items);
        }
        UpdateOrderHistory([
            'order_number' => $orderNo,
            'sub_order_number' => $orderNumber,
            'detail' => 'Sub Order #: '.$orderNumber.' with Package #: '.$package.' Has been Shipped',
            'title' => 'Sub Order Status Changed',
            'user_id' => $user_id,
            'reference' => 'API'
        ]);
        
        $air_shipment = 0;
        $od = OrderDetail::where('sub_order_number', $orderNumber)->first();
        if(isset($od->carrier_service->service_name)){
            $pos = strpos(strtolower($od->carrier_service->service_name),'air');
            if ($pos !== false) {
                $air_shipment = 1;
                OrderDetail::where('sub_order_number', $orderNumber)->update(['air_shipment' => $air_shipment]);
            }
        }
        $orderSummary = $od->orderSummary;
        
        UserLogs([
            'user_id' => $user_id,
            'action' => 'Click',
            'task' => 'Ship',
            'details' => $barcode.' has been shipped',
            'type' => 'CWMS',
            'sub_order_number' => $orderNumber,
            'etailer_order_number' => $orderSummary->etailer_order_number,
            'channel_order_number' => $orderSummary->channel_order_number,
            'client_order_number' => $orderSummary->sa_order_number
        ]);

        return response(
            [
                "error" => false,
                'message' => 'Success',
                'is_hot_route' => $od->hot_route == 1 ? 'Yes' : 'No',
                'air_shipment' => $air_shipment == 1 ? 'Yes' : 'No'
            ],
            200
        );

    }

    public function SendTextTOSA($order_info,$package){
        $orderSummary = $order_info->orderSummary;
        if($orderSummary->order_type_id == 1){
            $file = 'sa-tracking-import-'.str_replace('.','-',$order_info->sub_order_number).'-'.$package[0]->package_num.'_'.date('Y-m-d-H-i-s').'_processed.csv';
            $file_with_fol = 'orders_text/'.$file;
            // $myfile = fopen($file_with_fol, "w") or die("Unable to open file!");
            // $txt = "Order Number: ".$order_info->order_number."\nSub Order Number: ".$order_info->sub_order_number."\nPackage Number: ".$package->package_num."\nSA Number: ".$orderSummary->sa_order_number."\nChannel Order Number: ".$orderSummary->channel_order_number."\nTracking Number:".$package->tracking_number."\n\nShipping name: ".$orderSummary->ship_to_name."\nShipping address: ".$orderSummary->ship_to_address1."\nShipping city: ".$orderSummary->ship_to_city."\nZip: ".$orderSummary->ship_to_zip."\nShipping phone: ".$orderSummary->ship_to_phone."";
            // fwrite($myfile, $txt);
            // fclose($myfile);
            Excel::store(new SATrackingExport($order_info,$package,$orderSummary), $file_with_fol,'real_public');
            $key_path = public_path('inventory/cranium_sftp.ppk');
            $local_path = public_path('orders_text/'.$file);
            $key = PublicKeyLoader::load(file_get_contents($key_path),'cranium');
            $host = 's-566f8c66397647d1b.server.transfer.us-east-2.amazonaws.com';
            $user = 'StoreAutomator';
            define('NET_SFTP_LOGGING', SFTP::LOG_COMPLEX);
            $sftp = new SFTP($host);
            if (!$sftp->login($user, $key)){
                dump('connote login')    ;
            }
            $sftp->chdir('Orders');
            $sftp->chdir('ToSA');
            //$sftp->chdir('ProcessedTrackings');
            if(!$sftp->put($file, $local_path,SFTP::SOURCE_LOCAL_FILE)){
                dump('something went wrong');
            }
            @unlink($local_path);
        }
        
        
    }

    public function SendTextTOSAOLD($order_info,$package){
        $orderSummary = $order_info->orderSummary;
        if($orderSummary->order_type_id == 1){
            $file = 'Order_Package_'.$order_info->sub_order_number.'_'.$package->package_num.'_'.date('YmdHi').'.text';
            $file_with_fol = 'orders_text/'.$file;
            $myfile = fopen($file_with_fol, "w") or die("Unable to open file!");
            $txt = "Order Number: ".$order_info->order_number."\nSub Order Number: ".$order_info->sub_order_number."\nPackage Number: ".$package->package_num."\nSA Number: ".$orderSummary->sa_order_number."\nChannel Order Number: ".$orderSummary->channel_order_number."\nTracking Number:".$package->tracking_number."\n\nShipping name: ".$orderSummary->ship_to_name."\nShipping address: ".$orderSummary->ship_to_address1."\nShipping city: ".$orderSummary->ship_to_city."\nZip: ".$orderSummary->ship_to_zip."\nShipping phone: ".$orderSummary->ship_to_phone."";
            fwrite($myfile, $txt);
            fclose($myfile);
            $key_path = public_path('inventory/cranium_sftp.ppk');
            $local_path = public_path('orders_text/'.$file);
            $key = PublicKeyLoader::load(file_get_contents($key_path),'cranium');
            $host = 's-566f8c66397647d1b.server.transfer.us-east-2.amazonaws.com';
            $user = 'StoreAutomator';
            define('NET_SFTP_LOGGING', SFTP::LOG_COMPLEX);
            $sftp = new SFTP($host);
            if (!$sftp->login($user, $key)){
                dump('connote login')    ;
            }
            $sftp->chdir('Orders');
            $sftp->chdir('ToSA');
            //$sftp->chdir('ProcessedTrackings');
            if(!$sftp->put($file, $local_path,SFTP::SOURCE_LOCAL_FILE)){
                dump('something went wrong');
            }
            @unlink($local_path);
        }
        
        
    }

}
