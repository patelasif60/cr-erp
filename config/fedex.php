<?php

return [
    'BASE_URL' => env('APP_ENV') === 'production' ? 'https://apis.fedex.com' : env('FEDEX_BASE_URL'),
    'URL' =>  [
        'URL_AUTH' => '/oauth/token',
        'URL_AV' => '/country/v1/postal/validate',
        'URL_TRACK' => '/track/v1/trackingnumbers',
        'URL_SHIPPING' => '/ship/v1/shipments',
        'URL_TRANSIT' => '/availability/v1/transittimes'
    ],
    'CREDENTIALS' => [
        'CLIENT_ID' => env('APP_ENV') === 'production' ? 'l7d6f430e200c540529013ddc07eda9d20' : env('FEDEX_CID'),
        'CLIENT_SECRET' => env('APP_ENV') === 'production' ? '7d1063b7-73e2-4f85-9094-6ebdbbbbb8ca' : env('FEDEX_SEC'),
        'SHIPPING_ACCOUNT_NUMBER' => env('APP_ENV') === 'production' ? '252791345' : env('FEDEX_ACC_ID')
    ]
];
