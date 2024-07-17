<?php

namespace App\Http\Controllers\Api;

use App\FedexAuthToken;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class FedexController extends Controller
{

    private $token = '';

    public function validateAddress($state, $code, $date)
    {

        $this->getToken();
        if ($this->token === '') {
            return 'ErrorToken Expired';
        }

        $client = new Client();
        $body = [
            "carrierCode" => "FDXE",
            "countryCode" => "US",
            "stateOrProvinceCode" => "$state",
            "postalCode" => "$code",
            "shipDate" => "$date"
        ];
        $header = [
            "Content-Type" => "application/json",
            "Authorization" => "Bearer " . $this->token
        ];

        try {
            $response = $client->request(
                'POST',
                Config::get('fedex.BASE_URL') . Config::get('fedex.URL.URL_AV'),
                ['headers' => $header, 'json' => $body]
            );
        } catch (ClientException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() == 400) {
                return 'Failure';
            }
        }

        $json = json_decode($response->getBody());
        if (isset($json->output)) {
            return 'Success';
        }
        return 'Failure';
    }

    public function validateAddressApi(Request $request)
    {

        $state = $request->all()['state'];
        $code = $request->all()['code'];
        $date = $request->all()['date'];

        $response = $this->validateAddress($state, $code, $date);

        return response(['data' => $response]);
    }

    public function generateLabel($shipper_info, $receiver_info,$package_info)
    {
        $packaging_material = $package_info->packaging_material;
        $external_length = $packaging_material->external_length;
        $external_width = $packaging_material->external_width;
        $external_height = $packaging_material->external_height;

        $result = $this->getToken();
        if (!$result) {
            return ['error' => "Error: Cannot proceed. Error generating token."];
        }

        $client = new Client();

        $body = [
            "labelResponseOptions" => "LABEL",
            "requestedShipment" => [
                "shipper" => [
                    "contact" => [
                        "personName" => $shipper_info['name'],
                        "phoneNumber" => $shipper_info['phone'],
                        "companyName" => $shipper_info['company_name']
                    ],
                    "address" => [
                        "streetLines" => [$shipper_info['address']],
                        "city" => $shipper_info['city'],
                        "stateOrProvinceCode" => $shipper_info['code'],
                        "postalCode" => $shipper_info['postal_code'],
                        "countryCode" => $shipper_info['country']
                    ]
                ],
                "recipients" => [
                    [
                        "contact" => [
                            "personName" => $receiver_info['name'],
                            "phoneNumber" => $receiver_info['phone'],
                            "companyName" => $receiver_info['company_name']
                        ],
                        "address" => [
                            "streetLines" => [$receiver_info['address']],
                            "city" => $receiver_info['city'],
                            "stateOrProvinceCode" => $receiver_info['code'],
                            "postalCode" => $receiver_info['postal_code'],
                            "countryCode" => $receiver_info['country']
                        ]
                    ]
                ],
                "serviceType" => "STANDARD_OVERNIGHT",
                "packagingType" => "FEDEX_SMALL_BOX",
                "pickupType" => "USE_SCHEDULED_PICKUP",
                "blockInsightVisibility" => "false",
                "shippingChargesPayment" => [
                    "paymentType" => "SENDER"
                ],
                "shipmentSpecialServices" => [
                    "specialServiceTypes" => [
                        "FEDEX_ONE_RATE"
                    ]
                ],
                "labelSpecification" => [
                    "imageType" => "PDF",
                    "labelStockType" => "PAPER_85X11_TOP_HALF_LABEL"
                ],
                "requestedPackageLineItems" => [
                    json_decode('{"dimensions": {
                        "length": '.$external_length.',
                        "width": '.$external_width.',
                        "height": '.$external_height.',
                        "units": "IN"
                      }}')
                ]
            ],
            "accountNumber" => [
                "value" => Config::get('fedex.CREDENTIALS.SHIPPING_ACCOUNT_NUMBER')
            ]
        ];

        

        $header = [
            "Content-Type" => "application/json",
            "Authorization" => 'Bearer ' . $this->token
        ];

        $response = null;
        try {
            $response = $client->request(
                "POST",
                Config::get('fedex.BASE_URL') . Config::get('fedex.URL.URL_SHIPPING'),
                ['headers' => $header, 'json' => $body]
            );
        } catch (ClientException $e) {

            DeveloperLog([
                'reference' => 'Fedex Error',
                'ref_request' => json_encode([
                    'shipper_info' => $shipper_info,
                    'receiver_info' => $receiver_info,
                    'package_info' => $package_info
                ]),
                'ref_response' => json_encode($e)
            ]);

            if ($e->hasResponse() && $e->getResponse()){
                DeveloperLog([
                    'reference' => 'Fedex Error',
                    'ref_request' => json_encode([
                        'shipper_info' => $shipper_info,
                        'receiver_info' => $receiver_info,
                        'package_info' => $package_info
                    ]),
                    'ref_response' => json_encode(json_decode($e->getMessage()))
                ]);
            }
            
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() == 400) {
                return [
                    'error' => 'Error: Exception Failure. Msg: ' . $e->getMessage(),
                    'body' => $body
                ];
            }
        }catch (Exception $ex) {
            DeveloperLog([
                'reference' => 'Fedex Error 1',
                'ref_request' => json_encode([
                    'shipper_info' => $shipper_info,
                    'receiver_info' => $receiver_info,
                    'package_info' => $package_info
                ]),
                'ref_response' => json_encode($ex->getMessage())
            ]);
        }

        // if (!isset($response)) {
        //     return ['error' => 'Failure'];
        // }

        $json = json_decode($response->getBody());
        // dd($json);
        DeveloperLog([
            'reference' => 'Fedex',
            'ref_request' => json_encode([
                'shipper_info' => $shipper_info,
                'receiver_info' => $receiver_info,
                'package_info' => $package_info
            ]),
            'ref_response' => json_encode($json)
        ]);
        if (!isset($json->output)) {
            return ['error' => 'Error: No output property found.'];
        }

        $output = $json->output;
        if (!isset($output->transactionShipments)) {
            return ['error' => 'Error: No transaction shipments found.'];
        }

        $transactionShipments = $output->transactionShipments;
        if (!isset($transactionShipments) || count($transactionShipments) <= 0) {
            return ['error' => 'Error: No Tracking shipments value found.'];
        }

        $transactionShipment = $transactionShipments[0];
        if (!isset($transactionShipment->pieceResponses)) {
            return ['error' => 'Error: No Piece responses found.'];
        }

        $pieceResponses = $transactionShipment->pieceResponses;
        if (!isset($pieceResponses) || count($pieceResponses) <= 0) {
            return ['error' => 'Error: No Piece responses value found.'];
        }

        $pieceResponse = $pieceResponses[0];
        if (!isset($pieceResponse->packageDocuments)) {
            return ['error' => 'Error: No package documents found.'];
        }

        $packageDocuments = $pieceResponse->packageDocuments;
        if (!isset($packageDocuments) || count($packageDocuments) <= 0) {
            return ['error' => 'Error: No package documents value found.'];
        }

        $packageDocument = $packageDocuments[0];
        if (!isset($packageDocument->encodedLabel)) {
            return ['error' => "Error: No encoded label found."];
        }

        $label = $packageDocument->encodedLabel;
        $trackingNumber = $pieceResponse->masterTrackingNumber;

        return ['trackingNumber' => $trackingNumber, 'label' => $label, 'response' => json_encode($json)];
    }

    public function generateLabelApi(Request $request)
    {
        $shipper_info = $request->shipper_info;
        $receiver_info = $request->receiver_info;
        // return response(['a' => $shipper_info, 'b' => $receiver_info, 'c' => $shipping_date]);
        return response(['data' => $this->generateLabel($shipper_info, $receiver_info)]);
    }

    private function generateToken()
    {
        $client = new Client();
        $form_params = [
            'grant_type' => 'client_credentials',
            'client_id' => Config::get('fedex.CREDENTIALS.CLIENT_ID'),
            'client_secret' => Config::get('fedex.CREDENTIALS.CLIENT_SECRET')
        ];
        $header = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $response = $client->request(
            'POST',
            Config::get('fedex.BASE_URL') . Config::get('fedex.URL.URL_AUTH'),
            ['headers' => $header, 'form_params' => $form_params]
        );

        $json = json_decode($response->getBody());
        if (isset($json->access_token)) {
            $this->token = $json->access_token;
        }
    }

    public function generateTokenApi()
    {
        $this->getToken();
        return response(['data' => $this->token]);
    }

    private function getToken()
    {
        $dbToken = FedexAuthToken::first();
        if (!isset($dbToken)) {
            $this->generateToken();
            if ($this->token !== '') {
                $expiry = Carbon::now()->addSeconds(3600);
                FedexAuthToken::create([
                    'token' => $this->token,
                    'expiry_date_time' => $expiry
                ]);
                return true;
            }
            return false;
        }
        if (Carbon::now()->greaterThan($dbToken->expiry_date_time)) {
            $this->generateToken();
            if ($this->token !== '') {
                $expiry = Carbon::now()->addSeconds(3600);
                FedexAuthToken::where('id', 1)->update(['expiry_date_time' => $expiry, 'token' => $this->token]);
                return true;
            }
            return false;
        } else {
            $this->token = $dbToken->token;
        }
        return true;
    }

    public function createShipping()
    {
    }

    public function getTransitDaysApi(Request $request)
    {
        $shipper_info = $request->shipper_info;
        $receiver_info = $request->receiver_info;
        $wt_val = $request->wt_val;
        return response(['data' => $this->getTransitDays($shipper_info, $receiver_info, $wt_val)]);
    }

    public function getTransitDays($shipper_info, $receiver_info, $wt_val)
    {

        $result = $this->getToken();
        if (!$result) {
            return "Error: Cannot proceed. Error generating token.";
        }

        $client = new Client();

        $body = [
            "requestedShipment" => [
                "shipper" => [
                    "address" => [
                        "postalCode" => $shipper_info['postal_code'],
                        "countryCode" => $shipper_info['country_code']
                    ]
                ],
                "recipients" => [
                    [
                        "address" => [
                            "postalCode" => $receiver_info['postal_code'],
                            "countryCode" => $receiver_info['country_code']
                        ]
                    ]
                ],
                "packagingType" => "YOUR_PACKAGING",
                "requestedPackageLineItems" => [
                    [
                        "weight" => [
                            "units" => "LB",
                            "value" => $wt_val
                        ]
                    ]
                ]
            ],
            "carrierCodes" => [
                "FDXG"
            ]
        ];

        // return $body;

        $header = [
            "Content-Type" => "application/json",
            "Authorization" => 'Bearer ' . $this->token
        ];

        $response = null;
        try {
            $response = $client->request(
                "POST",
                Config::get('fedex.BASE_URL') . Config::get('fedex.URL.URL_TRANSIT'),
                ['headers' => $header, 'json' => $body]
            );
        } catch (ClientException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() == 400) {
                return 'Error: Exception Failure. Msg: ' . $e->getMessage();
            }
        }

        if (!isset($response)) {
            return 'Failure';
        }

        $json = json_decode($response->getBody());
        if (!isset($json->output)) {
            return 'Error: No output property found.';
        }

        $output = $json->output;
        if (!isset($output->transitTimes)) {
            return 'Error: No Transit Times found.';
        }

        $transitTimes = $output->transitTimes;
        if (!isset($transitTimes) || count($transitTimes) <= 0) {
            return 'Error: No Transit times value found.';
        }

        $transitTime = $transitTimes[0];
        if (!isset($transitTime->transitTimeDetails)) {
            return 'Error: No Transit Time details found.';
        }

        $transitTimeDetails = $transitTime->transitTimeDetails;
        if (!isset($transitTimeDetails) || count($transitTimeDetails) <= 0) {
            return 'Error: No Transit time details value found.';
        }

        $transitTimeDetail = $transitTimeDetails[0];
        if (!isset($transitTimeDetail->commit)) {
            return "Error: No commit value found";
        }

        $commit = $transitTimeDetail->commit;
        if (!isset($commit->transitDays)) {
            return "Error: No Transit day details found in commit";
        }

        $transitDays = $commit->transitDays;
        if (!isset($transitDays->minimumTransitTime)) {
            return "Error: No Transit day record found";
        }

        $transit_day = $this->getTransitDaysValue($transitDays->minimumTransitTime);

        return $transit_day;
    }

    private function getTransitDaysValue($transit_day)
    {
        switch ($transit_day) {
            case "EIGHT_DAYS":
                return 8;
            case "EIGHTEEN_DAYS":
                return 18;
            case "ELEVEN_DAYS":
                return 11;
            case "FIFTEEN_DAYS":
                return 15;
            case "FIVE_DAYS":
                return 5;
            case "FOUR_DAYS":
                return 4;
            case "FOURTEEN_DAYS":
                return 14;
            case "NINE_DAYS":
                return 9;
            case "NINETEEN_DAYS":
                return 19;
            case "ONE_DAY":
                return 1;
            case "SEVEN_DAYS":
                return 7;
            case "SEVENTEEN_DAYS":
                return 17;
            case "SIX_DAYS":
                return 6;
            case "SIXTEEN_DAYS":
                return 16;
            case "TEN_DAYS":
                return 10;
            case "THIRTEEN_DAYS":
                return 13;
            case "THREE_DAYS":
                return 3;
            case "TWELVE_DAYS":
                return 12;
            case "TWENTY_DAYS":
                return 20;
            case "TWO_DAYS":
                return 2;
            case "SMARTPOST_TRANSIT_DAYS":
            case "UNKNOWN":
            default:
                return -1;
        }
    }
}
