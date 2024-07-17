<?php

use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'DotRLGLImport' => [
            'driver' => 'daily',
            'path' => storage_path('logs/DotRLGLImport/DotRLGLImport.log'),
            'level' => 'debug'
        ],

        
        'UpdateSAInventoryTemplateFromIPC' => [
            'driver' => 'daily',
            'path' => storage_path('logs/UpdateSAInventoryTemplateFromIPC/UpdateSAInventoryTemplateFromIPC.log'),
            'level' => 'debug'
        ],
        'UpdateSAInventoryTemplateFromDotRLGL' => [
            'driver' => 'daily',
            'path' => storage_path('logs/UpdateSAInventoryTemplateFromDotRLGL/UpdateSAInventoryTemplateFromDotRLGL.log'),
            'level' => 'debug'
        ],
        'SendSAInventoryTemplate' => [
            'driver' => 'daily',
            'path' => storage_path('logs/SendSAInventoryTemplate/SendSAInventoryTemplate.log'),
            'level' => 'debug'
        ],
        'pricegroup' => [
            'driver' => 'daily',
            'path' => storage_path('logs/pricegroup/pricegroup.log'),
            'level' => 'debug'
        ],
        'WMS' => [
            'driver' => 'daily',
            'path' => storage_path('logs/WMS_API/WMS.log'),
            'level' => 'debug'
        ],
        'OrderAssignmentController' => [
            'driver' => 'daily',
            'path' => storage_path('logs/order_assign/order_assign.log'),
            'level' => 'debug'
        ],
        'IncomingOrderProcessing' => [
            'driver' => 'daily',
            'path' => storage_path('logs/incoming_order/incoming_order.log'),
            'level' => 'debug'
        ],
		'ImportSaInventoryTemplate' => [
            'driver' => 'daily',
            'path' => storage_path('logs/ImportSaInventoryTemplate/ImportSaInventoryTemplate.log'),
            'level' => 'debug',
        ],
		'ExportUpdatedMasterProducts' => [
            'driver' => 'daily',
            'path' => storage_path('logs/ExportUpdatedMasterProducts/ExportUpdatedMasterProducts.log'),
            'level' => 'debug',
        ],
		'ExportChannelInclusionTemplate' => [
            'driver' => 'daily',
            'path' => storage_path('logs/ExportUpdatedMasterProducts/ExportChannelInclusionTemplate.log'),
            'level' => 'debug',
        ],
        'ReleaseHold' => [
            'driver' => 'daily',
            'path' => storage_path('logs/release_hold/release_hold.log'),
            'level' => 'debug',
        ],
        'Inventory' => [
            'driver' => 'daily',
            'path' => storage_path('logs/inventory/inventory.log'),
            'level' => 'debug',
        ],
    ],

];
