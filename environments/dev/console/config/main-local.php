<?php
return [
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],

    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=192.168.0.111;dbname=hooray_exam',
            'username' => 'root',
            'password' => 'db3a88',
            'charset' => 'utf8',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '192.168.0.111',
            'port' => 6379,
            'database' => 3,
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
];
