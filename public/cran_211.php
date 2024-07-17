<?php

ini_set('max_execution_time', '6000');
$data = array();
$each_column = array();
$each_row = array();
$curlHandler = curl_init();

$userName = '3159oRYRi3s8';
$password = 'cp4CHl6Dwr9p7D6';

// curl_setopt_array($curlHandler, [
//     CURLOPT_URL => 'https://api.cartrover.com/v1/wms/merchant/list',
//     CURLOPT_RETURNTRANSFER => true,

//     CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
//     CURLOPT_USERPWD => $userName . ':' . $password,
// ]);

// $response = json_decode(curl_exec($curlHandler));
// curl_close($curlHandler);

$array = [
    '857806008880'
];

foreach($array as $row){

    $headers = array(
        "X-Merchant-Pk:4810",
     );

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.cartrover.com/v1/merchant/product/'.$row,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_USERPWD => $userName . ':' . $password,
    ]);

    $resp = json_decode(curl_exec($ch));
    echo($resp->response->sku." || ");

    if(count($resp->response->aliases) > 0){
        foreach($resp->response->aliases as $aliase){
            $each_row['site'] = 'Zocal Inc';
            $each_row['sku'] = $resp->response->sku;
            $each_row['alias_name'] = $aliase->code_type;
            $each_row['alias_value'] = "'".$aliase->code;

            $data[] = $each_row;  
        }      
    }    
}

$fp = fopen('ZocalInc_CartRoverAliasSKUs.csv', 'w');
foreach ($data as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);
?>