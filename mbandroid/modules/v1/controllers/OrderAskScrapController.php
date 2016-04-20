<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 9/16/15
 * Time: 5:18 PM
 */

namespace mbandroid\modules\v1\controllers;


use common\components\JPushNotice;
use common\models\AskOrder;
use common\models\CoinLog;
use common\models\CommonOrder;
use common\models\PassportStudentCount;
use common\models\Question;
use common\models\QuestionPost;
use common\models\Scrap;
use yii\rest\ActiveController;
use yii;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\db\Expression;

class OrderAskScrapController extends ActiveController
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
        $behaviors['verbFilter'] = [
            'class' => VerbFilter::className(),
//                'actions' => $this->verbs(),
            'actions' => [
                'create' => ['get', 'post'],
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
     * 取消解答
     * @return mixed
     */
    public function actionCreate()
    {
        $postData = Yii::$app->request->post();

        $order_id = $postData['order_id'];
        $question_id = $postData['question_id'];

        $answer_uid = Yii::$app->user->getId();

        //取消超过次数
        $scrap_count = Scrap::find()->where([
            'first' => 0,
            'answer_uid' => $answer_uid,
            'TO_DAYS(add_time)' => new Expression('TO_DAYS(NOW())')
        ])->count();
        if ($scrap_count >= Yii::$app->params['tchmaxjiedan']) {
            $result['status'] = '6004';
            $result['msg'] = Yii::$app->params['q_6004'] . Yii::$app->params['tchmaxjiedan'] . '次';
            return $result;
        } else {
            $data['exceedmax'] = $scrap_count + 1;
            $data['residue_times'] = Yii::$app->params['tchmaxjiedan'] - $scrap_count - 1;
        }

        // 1: 判断该题 是否属于自己 且自己没有回答
        $askOrder = AskOrder::find()
            ->from(AskOrder::tableName() . ' o')
            ->select('o.*,q.published_uid,q.question_title')
            ->leftJoin(Question::tableName() . ' q', 'q.question_id = o.question_id')
            ->where([
                'o.question_id' => $question_id,
                'answer_uid' => $answer_uid,
                'first' => '0'
            ])
            ->asArray()
            ->limit(1)
            ->one();

        if (null === $askOrder) {
            $data['status'] = '6006';
            $data['msg'] = Yii::$app->params['q_6006'];
            return $data;
        }
        if ($askOrder['replies'] === '1') {
            $data['status'] = '6007';
            $data['msg'] = Yii::$app->params['q_6007'];
            return $data;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $orderinfo = AskOrder::findOne(['order_id' => $order_id]);
            $question = Question::findOne(['question_id' => $question_id]);
            //判断是@老师还是指定老师 还是普通问题
            //是指定老师   则执行   否则跳过这里
            $minCoin = Yii::$app->params['defaultCoins']['ask'];
            $appointCoin = Yii::$app->params['defaultCoins']['askSpecify'];
            if ($orderinfo->appoint_type_id == '1' && $question->reward > $minCoin) {
                $nowTime = date('Y-m-d H:i:s');
                //获取指定老师价格
                $expense = CoinLog::findOne(['user_id' => $question->published_uid, 'order_id' => $orderinfo->order_sn]);
                //计算问题返还值
                if ($question->reward == $appointCoin) {
                    $diffCoin = $appointCoin - $minCoin;
                } else {
                    $diffCoin = $expense->nums - $minCoin;
                }
                $question->reward = $minCoin;
                $question->update_time = $nowTime;
                if (!$question->save()) {
                    $transaction->rollBack();
                }
                $questionPost = QuestionPost::findOne(['qid' => $question_id]);
                $questionPost->reward = $minCoin;
                $questionPost->update_time = $nowTime;
                if (!$questionPost->save()) {
                    $transaction->rollBack();
                }
                $commonOrder = CommonOrder::findOne(['order_id' => $askOrder['order_sn'], 'user_id' => $question->published_uid]);
                $commonOrder->price = $minCoin;
                $array = json_decode($commonOrder->data, true);
                $array['reward'] = "$diffCoin";
                $array['answer_uid'] = "";
                $array['answer_username'] = "";
                $commonOrder->data = json_encode($array);
                if (!$commonOrder->save()) {
                    $transaction->rollBack();
                }
                //更新哇哇豆日志
                $coinLog = new CoinLog();
                $coinLog->user_id = $question->published_uid;
                $coinLog->order_id = $askOrder['order_sn'];
                $coinLog->order_type = '0';
                $coinLog->nums = $diffCoin;
                $coinLog->type = '1';
                $coinLog->remark = "$question->published_username 提问状态由指定老师变为普通问题,返还哇哇豆.($diffCoin)";
                if (!$coinLog->save()) {
                    $transaction->rollBack();
                }
                //更新学生的哇哇豆
                $studentCoin = PassportStudentCount::updateAllCounters(
                    [
                        'coin' => '+' . $diffCoin,
                        'lock_coin' => '+' . $diffCoin
                    ],
                    [
                        'user_id' => $question->published_uid,
                    ]
                );
                if (!$studentCoin) {
                    $transaction->rollBack();
                }
            }
            $orderinfo->order_status = '1';
            $orderinfo->replies = '0';
            $orderinfo->answer_uid = '';
            $orderinfo->answer_nickname = '';
            $orderinfo->appoint_type_id = '0';
            $orderinfo->answer_username = '';
            $orderinfo->acquire_time = '0000-00-00 00:00:00';
            $orderinfo->classroom_id = '';//remove netease classroom Id
            $orderinfo->answer_begin_time = '0000-00-00 00:00:00';
            if (!$orderinfo->save()) {
                $transaction->rollBack();
            }

            $scrap = new Scrap();
            $scrap->order_id = $order_id;
            $scrap->first = $orderinfo->first;
            $scrap->add_time = date('Y-m-d H:i:s');
            $scrap->question_id = $question_id;
            $scrap->answer_uid = $answer_uid;
            if (!$scrap->save()) {
                $transaction->rollBack();
            }
            $transaction->commit();
            $jpush = new JPushNotice();
            $jpush->send([
                $askOrder['published_uid']
            ], [
                'type' => '3008',
                'title' => '您的问题《' . $askOrder['question_title'] . '》被老师放弃！',
                'time' => date('Y-m-d H:i:s')
            ]);
            return $data;
        } catch (yii\db\Exception $e) {
            //return $e->getMessage();
        }

        $data['status'] = '6016';
        $data['msg'] = Yii::$app->params['q_6016'];
        return $data;
    }
}
