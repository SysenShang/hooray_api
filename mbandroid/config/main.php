<?php
$params = array_merge(require(__DIR__ . '/../../common/config/params.php'), require(__DIR__ . '/../../common/config/params-local.php'), require(__DIR__ . '/params.php'), require(__DIR__ . '/params-local.php'));

return [
    'id' => 'app-mbandroid',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'mbandroid\controllers',
    'modules' => [
        'debug' => [
            'class' => 'yii\debug\Module',
            'allowedIPs' => ['*']
        ],
        'oauth2' => [
            'class' => 'filsh\yii2\oauth2server\Module',
            'tokenParamName' => 'accessToken',
            'tokenAccessLifetime' => 3600 * 24 * 30,
            'storageMap' => [
                'user_credentials' => 'common\models\User',
            ],
            'grantTypes' => [
                'user_credentials' => [
                    'class' => 'OAuth2\GrantType\UserCredentials',
                ],
                'refresh_token' => [
                    'class' => 'OAuth2\GrantType\RefreshToken',
                    'always_issue_new_refresh_token' => true
                ]
            ]
        ],
        'v1' => [
            'basePath' => '@mbandroid/modules/v1',
            'class' => 'mbandroid\modules\v1\Module',   // here is our v1 modules
        ]
    ],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => null,
            'enableSession' => true,
            'identityCookie' => ['name' => '_hooray', 'httpOnly' => true, 'domain' => '.' . DOMAIN],
            //YII2 配置 SSO (Single Sign-on) 实现单登陆
        ],
        'urlManager' => [
            'enablePrettyUrl' => true, // 启用美化URL
            'enableStrictParsing' => false, // 是否执行严格的url解析
            'showScriptName' => false, // 在URL路径中是否显示脚本入口文件隐藏index.php
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/user',
                        'v1/login',
                        'v1/access',
                        'v1/ask',
                        'v1/subjects',
                        'v1/micro',
                        'v1/micro-comment',
                        'v1/micro-order',
                        'v1/star',
                        'v1/order-ask',
                        'v1/order-ask-scrap',
                        'v1/reg-stu',
                        'v1/reg-tch',
                        'v1/back',
                        'v1/bind',
                        'v1/answer',
                        'v1/adver',
                        'v1/adver-list',
                        'v1/adver-home',
                        'v1/adver-new-online',
                        'v1/top-adver',
                        'v1/follwer',
                        'v1/check-coin',
                        'v1/my-ask-answer',
                        'v1/ask-confrim',
                    ],
                    'pluralize' => false
                ],
            ],
        ],
        //'cache' => [
        //    'class' => 'yii\redis\Cache',
        //],
        'session' => [
            'class' => 'yii\redis\Session',
            'cookieParams' => ['domain' => '.' . DOMAIN, 'httponly' => true, 'lifetime' => 3600 * 24 * 30 * 12],
            //YII2 配置 SSO (Single Sign-on) 实现单登陆
            'timeout' => 3600 * 24 * 30 * 12,
            'useCookies' => true,
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $postData = Yii::$app->request->post();
                if (strpos($_SERVER['REQUEST_URI'], '/v1/trade/notify') !== false && isset($postData['result_code'])) {
                    Yii::$app->response->format = yii\web\Response::FORMAT_RAW;
                } elseif (strpos($_SERVER['REQUEST_URI'], '/gii') !== false || strpos($_SERVER['REQUEST_URI'], '/debug') !== false) {
                    Yii::$app->response->format = yii\web\Response::FORMAT_HTML;
                } else {
                    $response = $event->sender;
                    if ($response->data !== null) {
                        if (isset($response->data['status']) && !isset($response->data['msg'])) {
                            $ps = ['6' => 'q', '2' => 'u', '7' => 's', '8' => 'm', '9' => 'mc'];
                            $fs = substr($response->data['status'], 0, 1);
                            if (isset($ps[$fs])) {
                                $prefix = $ps[$fs];
                                if (isset(Yii::$app->params[$prefix . '_' . $response->data['status']])) {
                                    $response->data['msg'] = Yii::$app->params[$prefix . '_' . $response->data['status']];
                                }
                            }
                        }
                        $re_code = ['400', '401', '403', '404', '500', '501', '502'];//重写常见的错误信息
                        if (isset($response->statusCode) && in_array("$response->statusCode", $re_code) && empty($response->data['msg'])) {
                            $response->data['msg'] = yii::$app->params['r_' . $response->statusCode];
                        }
                        //解决 oauth/token  client_id输入错误id http返回200 code返回400错误,友好提示
                        if (isset($response->data['status']) && in_array($response->data['status'], $re_code)) {
                            $response->data['msg'] = yii::$app->params['r_' . $response->data['status']];
                        }
                        //$response->data['status']<100   解决有的返回字段里面包含了status 标示状态 和 yii2 http返回的冲突
                        if ($response->statusCode === 200 && is_array($response->data)) {
                            array_walk_recursive($response->data, function (&$item) {
                                if (is_object($item)) {
                                    //
                                } elseif (preg_match('/^-?\d+$/', $item)) {
                                    $item = (string)$item;
                                } elseif (null === $item) {
                                    $item = '';
                                }
                            });
                        }
                        $response->data = [
                            'code' => isset($response->data['status']) ? ($response->data['status'] < 100 ? "$response->statusCode" : $response->data['status']) : "$response->statusCode",
                            'msg' => isset($response->data['msg']) ? $response->data['msg'] : (isset($response->data['message']) ? $response->data['message'] : "ok"),
                            'data' => (isset($response->statusCode) == 200 && (isset($response->data['status']) != 200 || $response->data['status'] < 100)) ? $response->data : "",
                        ];
                        $response->data['code'] = (string)$response->data['code'];
                        $response->statusCode = 200;
                    }
                }
            }
        ],
        'request' => [
            'parsers' => [
                'text/xml' => 'bobchengbin\Yii2XmlRequestParser\XmlRequestParser',
                'application/xml' => 'bobchengbin\Yii2XmlRequestParser\XmlRequestParser',
                'application/json' => 'yii\web\JsonParser',
                'text/json' => 'yii\web\JsonParser',
            ],
        ],
        'wechatpayment' => [
            'class' => 'common\components\WechatPayment',
        ],
        //        'authClientCollection' => [
        //            'class' => 'yii\authclient\Collection',
        //            'clients' => [
        //                'pcClient' => [
        //                    'class' => 'common\components\PcClient',
        //                    // Facebook 登录表单会以 '弹窗' 模式出现
        ////                    'authUrl' => 'https://www.facebook.com/dialog/oauth?display=popup',
        //                    'clientId' => 'testclient',
        //                    'clientSecret' => 'testpass',
        //                ],
        //            ],
        //        ],

        //        'errorHandler' => [
        //            'errorAction' => 'site/error',
        //        ],
    ],
    'params' => $params,
];
