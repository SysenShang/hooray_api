<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=neihihooray.mysql.rds.aliyuncs.com;dbname=hooray',
            'username' => 'dbd4q6459q5i622y',
            'password' => 'UZ_H6Zm4yYvfUA',
            'charset' => 'utf8mb4',
        ],
//        'mailer' => [
//            'class' => 'yii\swiftmailer\Mailer',
//            'viewPath' => '@common/mail',
//            // send all mails to a file by default. You have to set
//            // 'useFileTransport' to false and configure a transport
//            // for the mailer to send real emails.
//            'useFileTransport' => true,
//        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '10.132.53.70',
            'port' => 6379,
            "password" =>"6eX7ppVw",
            "database" => 0,
        ],
        'mail' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.exmail.qq.com',
                'username' => 'edm@hihooray.com',
                'password' => 'xK9jh2ke',
                'port' => '465',
                'encryption' => 'ssl',
            ],
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\EmailTarget',
                    'categories' => '!exception.CHttpException.*',
                    'mailer' => 'mail',
                    'levels' => ['error','warning'],
                    'message' => [
                        'from' => ['edm@hihooray.com'],
                        'to' => ['noreply@hihooray.com'],
                        'subject' => 'Website errors [' . YII_ENV . ']',
                    ],
                ],
            ],
        ],
    ],
];
