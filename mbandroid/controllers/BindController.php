<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 10/15/15
 * Time: 3:38 PM
 */

namespace mbandroid\controllers;

use common\models\CoinLog;
use common\models\StuCount;
use common\models\StudentInfo;
use common\models\StuLog;
use common\models\StuVerify;
use common\models\User;
use common\models\UserQq;
use common\models\UserWeixin;
use yii;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;


class BindController extends yii\rest\ActiveController
{
    public $modelClass = "common/models/User";

    /**
     * @inheritdoc  测试hotfix
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'only' => ['index'],  // set actions for disable access!
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

        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);

        return $actions;
    }

    public function actionQq()
    {
        $postData = Yii::$app->request->post();
        $expires_time = isset($postData['expires_time']) ? $postData['expires_time'] : '';
        $expires_time = substr($expires_time, 0, 10);
        $openid = isset($postData['openid']) ? $postData['openid'] : '';
        $access_token = isset($postData['access_token']) ? $postData['access_token'] : '';
        $refresh_token = isset($postData['refresh_token']) ? $postData['refresh_token'] : '';
//        $client_id = isset($postData['client_id']) ? $postData['client_id'] : "";
//        $scope = isset($postData['scope']) ? $postData['scope'] : "";
        $figureurl_qq_2 = isset($postData['figureurl_qq_2']) ? $postData['figureurl_qq_2'] : '';
        $gender = isset($postData['gender']) ? $postData['gender'] : '男';
        $date = date('Y-m-d H:i:s');
        $nickname = isset($postData['nickname']) ? $postData['nickname'] : '';

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $userQq = UserQq::findOne(['openid' => $openid]);
            if (!empty($userQq)) {
                $userQq->nickname = $nickname;
                $userQq->gender = $gender;
                $userQq->access_token = $access_token;
                $userQq->refresh_token = $refresh_token;
                $userQq->figureurl = $figureurl_qq_2;
                $userQq->expires_time = $expires_time;
                if (!$userQq->save()) {
                    $transaction->rollBack();
                    return ['status' => '2033', 'msg' => Yii::$app->params['u_2033']];
                }
                StudentInfo::updateAll(['nickname' => $nickname], ['user_id' => $userQq->uid]);
                User::updateAll(['xtype' => 1], ['user_id' => $userQq->uid]);
                $transaction->commit();
                return ['user_id' => $userQq->uid];
            } else {
                $user_id = ukg_getkey('127.0.0.1', 7954);
                $qq = new UserQq();
                $qq->expires_time = $expires_time;
                $qq->openid = $openid;
                $qq->access_token = $access_token;
                $qq->refresh_token = $refresh_token;
                $qq->uid = $user_id;
                $qq->add_time = $date;
                $qq->group_id = "1";
                $qq->nickname = $nickname;
                $qq->gender = $gender;
                $qq->figureurl = $figureurl_qq_2;
                if (!$qq->save()) {
                    $transaction->rollBack();
                    return ['status' => '2027', 'msg' => Yii::$app->params['u_2027']];
                }
                $user = new User();
                $user->user_id = $user_id;
                $user->username = uniqid('xs');
                $user->group_id = '1';
                $user->regdate = $date;
                $user->xtype = '1';
                $user->avatarstatus = '1';
                if (!$user->save()) {
                    $transaction->rollBack();
                    return ['status' => '2028', 'msg' => Yii::$app->params['u_2028']];
                }
                //验证初始化
                $stuVerify = new StuVerify();
                $stuVerify->user_id = $user_id;
                $stuVerify->verify1 = 2;
                if (!$stuVerify->save()) {
                    $transaction->rollBack();
                    return ['status' => '2029', 'msg' => Yii::$app->params['u_2029']];
                }
                //學生信息
                $stuInfo = new StudentInfo();
                $stuInfo->user_id = $user_id;
                $stuInfo->gender = $gender;
                $stuInfo->nickname = $nickname;
                $stuInfo->avatar = $figureurl_qq_2;
                if (!$stuInfo->save()) {
                    $transaction->rollBack();
                    return ['status' => '2030', 'msg' => Yii::$app->params['u_2030']];
                }
                //初始化log
                $log = new StuLog();
                $log->user_id = $user_id;
                $log->lastlogin = $date;
                if (!$log->save()) {
                    $transaction->rollBack();
                    return ['status' => '2031', 'msg' => Yii::$app->params['u_2031']];
                }
                //初始化统计
                $stuCount = new StuCount();
                $stuCount->user_id = $user_id;
                if (!$stuCount->save()) {
                    $transaction->rollBack();
                    return ['status' => '2032', 'msg' => Yii::$app->params['u_2032']];
                }
                $transaction->commit();
//                //注册送积分
//                $CoinLog = new CoinLog();
//                $CoinLog->registerTask($user_id, 1);
                return ['user_id' => $user_id];
            }
        } catch (yii\base\ErrorException $e) {
            $transaction->rollBack();
            return ['status' => '2010', 'msg' => Yii::$app->params['u_2010']];
        }
    }

    public function actionWeixin()
    {
        $postData = Yii::$app->request->post();
        $expires_time = isset($postData['expires_time']) ? $postData['expires_time'] : '';
        $expires_time = substr($expires_time, 0, 10);
        $openid = isset($postData['openid']) ? $postData['openid'] : '';
        $access_token = isset($postData['access_token']) ? $postData['access_token'] : '';
        $refresh_token = isset($postData['refresh_token']) ? $postData['refresh_token'] : '';
        //$client_id     = isset($postData['client_id']) ? $postData['client_id'] : "";
        //$scope         = isset($postData['scope']) ? $postData['scope'] : "";
        $headimgurl = $postData['headimgurl'];
        $gender = isset($postData['sex']) ? $postData['sex'] : '男';
        $gender = $gender == 1 ? '男' : '女';
        $date = time();
        $reg_time = date('Y-m-d H:i:s');
        $nickname = isset($postData['nickname']) ? $postData['nickname'] : '';

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $weixin = UserWeixin::findOne(['openid' => $openid]);
            if (!empty($weixin)) {
                $weixin->expires_in = $expires_time;
                $weixin->access_token = $access_token;
                $weixin->refresh_token = $refresh_token;
                $weixin->add_time = $date;
                $weixin->nickname = $nickname;
                $weixin->sex = $gender;
                $weixin->headimgurl = $headimgurl;
                if (!$weixin->save()) {
                    $transaction->rollBack();
                    return ['status' => '2034', 'msg' => Yii::$app->params['u_2034']];
                }
                StudentInfo::updateAll(['nickname' => $nickname], ['user_id' => $weixin->uid]);
                User::updateAll(['xtype' => 2], ['user_id' => $weixin->uid]);
                $transaction->commit();
                return ['user_id' => $weixin->uid];
            } else {
                $user_id = ukg_getkey('127.0.0.1', 7954);
                $weixin = new UserWeixin();
                $weixin->expires_in = $expires_time;
                $weixin->openid = $openid;
                $weixin->access_token = $access_token;
                $weixin->refresh_token = $refresh_token;
                $weixin->uid = $user_id;
                $weixin->add_time = $date;
                $weixin->group_id = 1;
                $weixin->nickname = $nickname;
                $weixin->sex = $gender;
                $weixin->headimgurl = $headimgurl;
                if (!$weixin->save()) {
                    $transaction->rollBack();
                    return ['status' => '2035', 'msg' => Yii::$app->params['u_2035']];
                }

                $user = new User();
                $user->user_id = $user_id;
                $user->username = uniqid('xs');;
                $user->group_id = 1;
                $user->regdate = $reg_time;
                $user->xtype = 2;
                $user->avatarstatus = 1;
                if (!$user->save()) {
                    $transaction->rollBack();
                    return ['status' => '2028', 'msg' => Yii::$app->params['u_2028']];
                }

                //验证初始化
                $stuVerify = new StuVerify();
                $stuVerify->user_id = $user_id;
                $stuVerify->verify1 = 2;
                if (!$stuVerify->save()) {
                    $transaction->rollBack();
                    return ['status' => '2029', 'msg' => Yii::$app->params['u_2029']];
                }

                //學生信息
                $stuInfo = new StudentInfo();
                $stuInfo->user_id = $user_id;
                $stuInfo->gender = $gender;
                $stuInfo->nickname = $nickname;
                $stuInfo->avatar = $headimgurl;
                if (!$stuInfo->save()) {
                    $transaction->rollBack();
                    return ['status' => '2030', 'msg' => Yii::$app->params['u_2030']];
                }

                //初始化log
                $log = new StuLog();
                $log->user_id = $user_id;
                $log->lastlogin = $reg_time;
                if (!$log->save()) {
                    $transaction->rollBack();
                    return ['status' => '2031', 'msg' => Yii::$app->params['u_2031']];
                }

                //初始化统计
                $stuCount = new StuCount();
                $stuCount->user_id = $user_id;
                if (!$stuCount->save()) {
                    $transaction->rollBack();
                    return ['status' => '2032', 'msg' => Yii::$app->params['u_2032']];
                }
                $transaction->commit();
                return ['user_id' => $user_id];
            }
        } catch (yii\base\ErrorException $e) {
            $transaction->rollBack();
            return ['status' => '2010', 'msg' => Yii::$app->params['u_2010']];
        }
    }
}
