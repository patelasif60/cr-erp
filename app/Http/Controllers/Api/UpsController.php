<?php

namespace App\Http\Controllers\Api;

use Exception;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use GuzzleHttp\Exception\GuzzleException;

class UpsController extends Controller {

    public function validateAddress($city, $state, $code) {
        
        $client = new Client();
        $body = [
            'AccessRequest' => [
                "AccessLicenseNumber" => Config::get('ups.CREDENTIALS.AccessLicenseNumber'),
                "UserId" =>  Config::get('ups.CREDENTIALS.Username'),
                "Password" => Config::get('ups.CREDENTIALS.Password')
            ],
            "AddressValidationRequest" => [
                "Request" => [
                    "TransactionReference" => [
                        "CustomerContext" => "Test Context"
                    ],
                    "RequestAction" => "AV"
                ],
                "Address" => [
                    "City" => "$city",
                    "StateProvinceCode" => "$state",
                    "PostalCode" => "$code"
                ]
            ]
        ];
        $header = [
            "Access-Control-Allow-Headers" => "Origin, X-Requested-With, Content-Type, Accept",
            "Access-Control-Allow-Methods" =>  Config::get('ups.HTTP_METHOD.AV'),
            "Access-Control-Allow-Origin" => "*",
            "Content-Type" => "application/json"
        ];

        $response = $client->request( Config::get('ups.HTTP_METHOD.AV'), 
                                Config::get('ups.URL.URL_AV'), ['headers' => $header, 'json' => $body]);
        $json = json_decode($response->getBody());                
        
        if (!isset($json->AddressValidationResponse)) {
            return "Failure";
        }
        $addressValidationResponse = $json->AddressValidationResponse;
        
        if (!isset($addressValidationResponse->Response)) {
            return "Failure";
        }
        $responseJson = $addressValidationResponse->Response;

        if (!isset($responseJson->ResponseStatusDescription)) {
            return "Failure";
        }
        $responseStatusDescription = $responseJson->ResponseStatusDescription;

        if (strtolower($responseStatusDescription) === 'failure') {
            return "Failure";
        }
        
        if (!isset($addressValidationResponse->AddressValidationResult)) {
            return "Failure";
        }

        $addressValidationResults = $addressValidationResponse->AddressValidationResult;
        try {
            if (isset($addressValidationResults->Quality) && $addressValidationResults->Quality == 1.0) {
               return "Success";
            }
        } catch (Exception $ex) {

        }
        
        $isOne = false;
        foreach($addressValidationResults as $result) {
            if (isset($result->Quality) && $result->Quality == 1.0) {
                $isOne = true;
                break;
            }
        }

        return $isOne ? "Success" : "Failure";
    }

    public function validateAddressApi(Request $request) {
        
        $city = $request->all()['city'];
        $state = $request->all()['state'];
        $code = $request->all()['code'];
        
        $response = $this->validateAddress($city, $state, $code);

        return response(['data' => $response]);
    }
}