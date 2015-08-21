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
        'host' => '',
        'machine' => '',
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