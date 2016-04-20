<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'JbBm84-7PsNkzkasd26TAeC95KhruE7ua',
            'enableCookieValidation' => true,
            'enableCsrfValidation' => true,
        ],
    ],
];

if (!YII_ENV_TEST && YII_DEBUG) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';
    $config['modules']['debug'] = array('allowedIPs' => ['127.0.0.1','112.64.126.246']);

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
