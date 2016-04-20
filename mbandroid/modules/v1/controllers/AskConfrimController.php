<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 10/23/15
 * Time: 11:03 AM
 */

namespace mbandroid\modules\v1\controllers;

use common\components\JPushNotice;
use common\models\AskOrder;
use common\models\CoinLog;
use common\models\CreditRule;
use common\models\Question;
use common\models\SysCoinLog;
use common\models\TchCount;
use yii;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;


class AskConfrimController extends ActiveController
{
    public $modelClass = 'common\models\Question';

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
        $behaviors['verbFilter'] = [
            'class' => VerbFilter::className(),
//                'actions' => $this->verbs(),
            'actions' => [
                'index' => ['get'],
                'view' => ['get'],
                'create' => ['get', 'post'],
                'update' => ['get', 'put', 'post'],
                'delete' => ['post', 'delete'],
            ],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['Create'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['Create'],
                    'roles' => ['@'],
                ],
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
     * 问题确认
     */
    public function actionCreate()
    {
        $postData = Yii::$app->request->post();
        if (!array_key_exists('order_id', $postData)) {
            return ['status' => '5001', 'msg' => Yii::$app->params['s_5001']];
        }
        $order_id = $postData['order_id'];
        $user_id = Yii::$app->user->id;
        $username = Yii::$app->user->identity->username;
        $ask_order = AskOrder::find()
            ->select(Question::tableName() . '.question_id,question_title,published_uid,reward,confrim,answer_uid,answer_username,order_sn')
            ->leftJoin(Question::tableName(), AskOrder::tableName() . '.question_id' . '=' . Question::tableName() . '.question_id')
            ->where([
                AskOrder::tableName() . '.order_id' => $order_id,
                AskOrder::tableName() . '.first' => 0
            ])
            ->limit(1)
            ->asArray()
            ->one();
        //用户身份不正确
        if ($ask_order['published_uid'] !== $user_id) {
            return ['status' => '6011', 'msg' => Yii::$app->params['q_6011']];
        }

        //重复验证
        if ($ask_order['confrim'] !== '1') {
            return ['status' => '6012', 'msg' => Yii::$app->params['q_6012']];
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $result = AskOrder::updateAll(['confrim' => 2, 'order_status' => 3], ['order_id' => $order_id]);
            if (!$result) {
                $transaction->rollBack();
                return ['status' => '6021', 'msg' => Yii::$app->params['q_6021']];
            }

            $teacher = TchCount::findOne(['user_id'=>$ask_order['answer_uid']]);
            if (null === $teacher) {
                return ['status' => '6015', 'msg' => Yii::$app->params['q_6015']];

            }
            $rating = $teacher['rating'];
            $rating = Yii::$app->params['systemEduCoinRate'][$rating];
            $coin = $ask_order['reward'];
            $commission = floor($coin * $rating);//系统佣金
            $income = $coin - $commission;//教师收入
            $teacher['coin'] += $income;
            $teacher['question_num'] += 1;
            if (!$teacher->save()) {
                $transaction->rollBack();
                return ['status' => '6022', 'msg' => Yii::$app->params['q_6022']];
            }

            //记录哇哇豆LOG中
            $coin_log = new CoinLog();
            $coin_log->user_id = $ask_order['answer_uid'];
            $coin_log->order_id = $order_id;
            $coin_log->order_type = '0';
            $coin_log->nums = $income;
            $coin_log->type = 1;
            $coin_log->remark = "$username 确认了问题,获得哇哇豆($income).";
            if (!$coin_log->save()) {
                $transaction->rollBack();
                return ['status' => '6023', 'msg' => Yii::$app->params['q_6023']];
            }

            //保存系统佣金日志
            $log = new SysCoinLog();
            $log->user_id = $ask_order['answer_uid'];
            $log->order_id = $ask_order['order_sn'];
            $log->order_type = 0;
            $log->nums = $commission;
            $log->remark = '回答问题提成';
            $log->createtime = date('Y-m-d H:i:s');
            if (!$log->save()) {
                $transaction->rollBack();
                return ['status' => '6024', 'msg' => Yii::$app->params['q_6024']];
            }

            $credit_rule = new CreditRule();
            $credit_rule->teacherQuestion($ask_order['answer_uid'], $ask_order['question_id']);
            $transaction->commit();

            return ['status' => '200', 'msg' => 'ok'];
        } catch (yii\db\Exception $e) {
            return ['status' => '6013', 'msg' => Yii::$app->params['q_6013']];
        }
    }
}
