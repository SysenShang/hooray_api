<?php
return [
    'external_log_path' => '/home/www/logs/hooray_api',
    'wechatPayment' => array(
        'appId' => 'wx52835b4b2202ba57',
        'merchantId' => '1243713602',
        'appKey' => 'PjpfP3H2dQw0KnQazyNK9YfX8iDQaxZl',
        'appSecret' => 'ed877d7879670834b6110b97b974262a',
        'notifyUrl' => 'http://mbandroid.hihooray.net/v1/trade/notify',
        'tradeType' => 'APP'
    ),
    'wechatPaymentWeb' => array(
        'appId' => 'wx426b3015555a46be',
        'merchantId' => '1225312702',
        'appKey' => 'e10adc3949ba59abbe56e057f20f883e',
        'appSecret' => '01c6d59a3f9024db6336662ac95c8e74',
        'notifyUrl' => 'http://mbandroid.hihooray.net/v1/trade/notify',
        'tradeType' => 'NATIVE'
    ),
    'alipayExpressGateway' => array(
        'partner' => '2088811197669185',
        'key' => '6vxwog7iwk64p2j5mt7l70uy82sc2bgl',
        'seller_email' => 'service@hihooray.com',
        'return_url' => 'http://pay.hihooray.com/pay/create',
        'notify_url' => 'http://pay.hihooray.com/pay/notify',
        'subject' => 'hihooray charge coins'
    )
];