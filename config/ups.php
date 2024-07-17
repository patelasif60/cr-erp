<?php

return [
    'URL' =>  [
        'URL_AV' => 'https://onlinetools.ups.com/rest/AV',
        'URL_TRACK' => 'https://onlinetools.ups.com/track/v1/details/',
        'URL_TRANSIT' => 'https://onlinetools.ups.com/ship/v1/shipments/transittimes',
        'URL_SHIPPING' => 'https://onlinetools.ups.com/ship/v1/shipments'
    ],
    'HTTP_METHOD' => [
        'AV' => 'POST',
        'TRACK' => 'GET',
        'TRANSIT' => 'POST',
        'SHIPPING' => 'POST'
    ],
    'CREDENTIALS' => [
        'Username' => 'DevOpsAPI',
        'Password' => 'ETaiLER2!#23',
        'AccessLicenseNumber' => 'FDBAF49389F87F16'
    ]
];