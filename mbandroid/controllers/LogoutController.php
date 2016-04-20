<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 8/17/15
 * Time: 4:12 PM
 */

namespace mbandroid\controllers;

use common\models\HoorayUser;
use common\models\User;
use common\models\AccessTokens;
use yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\AccessControl;

class LogoutController extends ActiveController
{
    public $modelClass = 'common\models\User';

    /**
     * @inheritdoc
     */
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
            'only' => ['Index'],
            'rules' => [
                // allow authenticated users
                [
                    'allow' => true,
                    'actions' => ['Index'],
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


        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);

        return $actions;
    }

    /**
     * logout
     * @return mixed
     */
    public function actionIndex()
    {
        $kickOut = Yii::$app->request->get('kick-out');
        $userId = Yii::$app->user->id;
        if (isset($kickOut) && $kickOut) {
            AccessTokens::findOne(Yii::$app->request->get('access-token'))->delete();
            $data['status'] = '200';
            $data['msg'] = 'ok';
        } else {
            if (Yii::$app->user->logout()) {
                User::updateUserStatus($userId, 0);
                $redis_user = HoorayUser::findOne(['user_id' => $userId]);
                if ($redis_user) {
                    HoorayUser::updateAll(['status' => 0], ['user_id' => $userId]);
                }
                AccessTokens::findOne(Yii::$app->request->get('access-token'))->delete();
                $data['status'] = '200';
                $data['msg'] = 'ok';
            } else {
                $data['status'] = '2016';
                $data['msg'] = YII::$app->params['u_2016'];
            }
        }

        return $data;
    }

}
