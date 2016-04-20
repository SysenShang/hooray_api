<?php
/**
 * Created by Aptana.
 * User: Kevin gates
 * Date: 10/20/15
 * Time: 11:19 PM
 */
namespace mbandroid\modules\v1\controllers;

use common\components\CTask;
use common\models\StuVerify;
use common\models\TchVerify;
use common\models\TchCount;
use common\models\StuCount;
use common\models\Checkin;

use common\models\CoinLog;
use yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;


use common\models\F;

class CheckinsController extends ActiveController
{
    public $modelClass = 'common\models\Checkin';

    /**
     * @behaviors
     */
    public function behaviors()
    {
        $behaviors                  = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];

        $behaviors['verbFilter'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create' => ['post'],
                'info' => ['get'],
                'status' => ['get'],
            ],
        ];
        $behaviors['access']     = [
            'class' => AccessControl::className(),
            'only' => ['Create', 'Info', 'Status'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['Create', 'Info', 'Status'],
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
     * create or update checkin
     * @param $user_id *
     */
    public function actionCreate()
    {
        $uid            = Yii::$app->user->getId();
        $checkin        = Checkin::findIdentity($uid);
        $todayBegin     = strtotime(date('Y-m-d') . " 00:00:00");
        $yesterdayBegin = strtotime(date("Y-m-d", strtotime("-1 day")) . " 00:00:00");
        $yesterdayEnd   = strtotime(date("Y-m-d", strtotime("-1 day")) . " 23:59:59");
        $signed_days    = 1;
        $group_id       = Yii::$app->user->identity->group_id;
        $check_in_coins = 50;
        if (null === $checkin) {
            $checkin                   = new Checkin();
            $checkin->user_id          = $uid;
            $checkin->signed_date_time = time();
            $checkin->signed_days      = $signed_days;
            $checkin->created_at       = date("Y-m-d H:i:s");
            $checkin->updated_at       = date("Y-m-d H:i:s");

            if ($checkin->save()) {
                CTask::done($uid, $group_id, 'checkin');
                if ($group_id == 2) {
                    $teacher_count       = TchCount::findOne(['user_id' => $uid]);
                    $teacher_count->coin = $teacher_count->coin + $check_in_coins;
                    $teacher_count->save();
                    $remark = "用户签到[老师]赠送哇哇豆.($check_in_coins)";
                } else {
                    $student_count       = StuCount::findOne(['user_id' => $uid]);
                    $student_count->coin = $student_count->coin + $check_in_coins;
                    $student_count->save();
                    $remark = "用户签到[学生]赠送哇哇豆.($check_in_coins)";
                }
                // insert coin log
                $coinLog             = new CoinLog();
                $coinLog->user_id    = $uid;
                $coinLog->order_id   = F::generateOrderSn('');
                $coinLog->order_type = 10;
                $coinLog->nums       = 50;
                $coinLog->type       = 1;
                $coinLog->remark     = $remark;
                $coinLog->detail     = "";
                $coinLog->status     = 2;
                $coinLog->createtime = date("Y-m-d H:i:s");
                $coinLog->save();
                return ['status' => '200'];
            }
        } else {
            // current day sign once.
            if ($checkin->signed_date_time < $todayBegin) {
                // continuous checkin
                if ($checkin->signed_date_time > $yesterdayBegin && $checkin->signed_date_time < $yesterdayEnd) {
                    $signed_days = $checkin->signed_days + 1;
                }
                $checkin->signed_date_time = time();
                $checkin->signed_days      = $signed_days;
                $checkin->updated_at       = date("Y-m-d H:i:s");
                if ($checkin->save()) {
                    CTask::done($uid, $group_id, 'checkin');
                    if ($group_id == 2) {
                        $teacher_count       = TchCount::findOne(['user_id' => $uid]);
                        $teacher_count->coin = $teacher_count->coin + $check_in_coins;
                        $teacher_count->save();
                        $remark = "用户签到[老师]赠送哇哇豆.($check_in_coins)";
                        //    continuous check-in 5 days
                        if ($signed_days == 5) {
                            $teacher_verify              = TchVerify::findOne(['user_id' => $uid]);
                            $teacher_verify->is_check_in = true;
                            $teacher_verify->save();
                        }
                    } else {
                        $student_count       = StuCount::findOne(['user_id' => $uid]);
                        $student_count->coin = $student_count->coin + $check_in_coins;
                        $student_count->save();
                        $remark = "用户签到[学生]赠送哇哇豆.($check_in_coins)";
                        //    continuous check-in 5 days
                        if ($checkin->signed_days == 5) {
                            $student_verify              = StuVerify::findOne(['user_id' => $uid]);
                            $student_verify->is_check_in = true;
                            $student_verify->save();
                        }
                    }
                    // insert coin log
                    $coinLog             = new CoinLog();
                    $coinLog->user_id    = $uid;
                    $coinLog->order_id   = F::generateOrderSn('');
                    $coinLog->order_type = 10;
                    $coinLog->nums       = 50;
                    $coinLog->type       = 1;
                    $coinLog->remark     = $remark;
                    $coinLog->detail     = "";
                    $coinLog->status     = 2;
                    $coinLog->createtime = date("Y-m-d H:i:s");
                    $coinLog->save();
                    return ['status' => '200'];
                }
            } else {
                return "signed_already";
            }
        }

        F::close_session();
    }

    /**
     * get one checkin information
     * @param $user_id
     */
    public function actionInfo()
    {
        $id      = Yii::$app->user->getId();
        $checkin = Checkin::findIdentity($id);
        F::close_session();

        return $checkin;
    }

    /**
     * get one checkin Status
     * @param $user_id
     */
    public function actionStatus()
    {
        $id         = Yii::$app->user->getId();
        $todayBegin = strtotime(date('Y-m-d') . " 00:00:00");
        $checkin    = Checkin::findIdentity($id);
        if (empty($checkin)) {
            return "false";
        } else {
            if ($checkin->signed_date_time < $todayBegin) {
                return "false";
            } else {
                return "true";
            }
        }

        F::close_session();
    }
}
