<?php
return [
    'log' => [
        'type' => 'graylog2',
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
            'driver' => 'udp',
            'host' => 'localhost',
            'port' => 12201,
        ],
    ],
];