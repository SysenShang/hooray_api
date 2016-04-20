<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 9/14/15
 * Time: 11:24 AM
 */

namespace mbandroid\controllers;


use yii\rest\ActiveController;
use yii;

use yii\filters\auth\HttpBasicAuth;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use yii\filters\AccessControl;

use Qiniu\Auth;


class TokenController extends ActiveController
{
    public $modelClass = 'common\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['index'],
            'rules' => [
                // allow authenticated users
                [
                    'allow' => true,
                    'actions' => ['index'],
                    'roles' => ['@'],
                ],
                // everything else is denied
            ],
            'denyCallback' => function () {
                throw new \Exception('您无权访问该页面');
            },
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'],$actions['view']);

        return $actions;
    }

    public function actionIndex()
    {
        $getData = Yii::$app->request->get();

        $accessKey = Yii::$app->params['qiniu']['access_key'];
        $secretKey = Yii::$app->params['qiniu']['secret_key'];

        $auth             = new Auth($accessKey, $secretKey);
        $bucket           = empty($getData['bucket']) ? Yii::$app->params['hooray-ask']['bucket'] : $getData['bucket'];
        $token            = $auth->uploadToken($bucket);
        $data['token']    = $token;
        $str              = 'abcdefghijkmnpqrstuvwxyz23456789';
        $data['filename'] = $bucket . '_' . substr(str_shuffle($str), 0, 10) . "_" . time();
        if (isset($getData['from'])) {
            $data['filename'] .= '_' . $getData['from'];
        }
        if (isset($getData['extname'])) {
            $data['filename'] .= '.' . $getData['extname'];
        }
        $_array['url']    = Yii::$app->params[$bucket]['url'];

        return $data;
    }
}
