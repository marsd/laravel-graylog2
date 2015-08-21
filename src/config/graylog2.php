<?php
return [
    'log' => [
        'inputs' => [
            'do' => true,
            'except' => [
                'password', 'password_confirmation'
            ],
        ],
    ],

    'app' => [
        'machine' => env('GRAYLOG_APP_MACHINE', 'localhost'),
        'host' => env('GRAYLOG_APP_HOST', 'localhost.private'),
        'version' => '',
    ],

    'connections' => [
        'udp' => [
            'driver'    => 'udp',
            'host'      => 'localhost',
            'database'  => 12201,
        ],
    ],
];