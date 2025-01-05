#Laravel Custom Log: 
--------------------
1. config > loggin.php : 
--------------------------
    'channels' => [
        'debug'=> [
            'driver' => 'single',
            'path' => storage_path('logs/debug.log'),
            'level' => 'debug'
        ],
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
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

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],


         //custom log channel for Robi :---------------------Main Part----------------------------------

        'Robi' => [  //this is the channel name
            'driver' => 'single',
            'path' => storage_path('logs/robi.log'),
            'level' => 'warning', // Log::level_name => example: Log::warning. 
            // 'level' => 'PaymentError',
        ],
    ],

];
================================================
2. write the custom log : 
------------------------
//=================================== write the custom log ==============================
Route::get('/check_log', function () {
Log::channel('Robi')->info('This is a custom log message for Nagad channel.');
return "Log write is done " ;
});

Route::get('/check_log', function () {
Log::channel('Robi')->warning('This is a custom log message for Nagad channel.');
return "Log write is done " ;
});

