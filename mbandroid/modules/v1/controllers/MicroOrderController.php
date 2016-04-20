<?php
/**
 * Created by PhpStorm.
 * User: lvdongxiao
 * Date: 8/5/15
 * Time: 4:56 PM
 */
namespace mbandroid\modules\v1\controllers;

use common\components\CTask;
use common\components\JPushNotice;
use common\components\RedisStorage;
use common\models\CoinLog;
use common\models\CommonOrder;
use common\models\F;
use common\models\MicroCourse;
use common\models\MicroCourseOrder;
use common\models\StuCount;
use common\models\TchCount;
use common\models\User;
use yii;
use yii\behaviors\TimestampBehavior;
use yii\data\Pagination;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

class MicroOrderController extends ActiveController
{
    public $modelClass = 'common\models\MicroOrder';


    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'authenticator' => [
                    'class' => CompositeAuth::className(),
                    'except' => ['index'],  // set actions for disable access!
                    'authMethods' => [
                        HttpBasicAuth::className(),
                        HttpBearerAuth::className(),
                        QueryParamAuth::className(),
                    ]
                ],
                'access' => [
                    'class' => AccessControl::className(),
                    'except' => [],
                    'rules' => [
                        // allow authenticated users
                        [
                            'allow' => true,
                            'actions' => [
                                'pay',
                                'my-all-list',
                                'my-list',
                            ],
                            'roles' => ['@'],
                        ],
                        // everything else is denied
                    ],
                    'denyCallback' => function () {
                        throw new \Exception('您无权访问该页面');
                    },
                ],
                [
                    'class' => TimestampBehavior::className(),
                    'createdAtAttribute' => 'create_time',
                    'updatedAtAttribute' => 'update_time',
                    'value' => new Expression('NOW()'),
                ],
            ]
        );
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);

        return $actions;
    }

    public function getUserName($user_id)
    {
        $redis_storage = new RedisStorage();
        $user_info = $redis_storage->user($user_id);
        return $user_info['username'];
    }

    /**
     *
     */
    public function actionIndex()
    {
        //
    }

    /**
     * 提交微课购买订单
     * @return bool
     */
    public function actionCreate()
    {
        $param = Yii::$app->request->post();
        $micro_id = isset($param['micro_id']) ? $param['micro_id'] : '';
        if (empty($micro_id)) {
            return ['status' => '9001', 'msg' => Yii::$app->params['mc_9001']];
        }
        $user_id = Yii::$app->user->getId();
        $microinfo = MicroCourse::findOne(['id' => $micro_id]);
        if ($microinfo->price == 0) {
            return ['status' => '9002', 'msg' => Yii::$app->params['mc_9002']];
        }
        if ($user_id == $microinfo->user_id) {
            return ['status' => '9004', 'msg' => Yii::$app->params['mc_9004']];
        }
        $exists = MicroCourseOrder::find('id', 'order_id')->where(['user_id' => $user_id, 'mc_id' => $micro_id, 'isdel' => 0])
            ->andWhere(['>', 'valid_time', time()])
            ->asArray()
            ->one();
        if ($exists) {
            return ['status' => '9005', 'msg' => Yii::$app->params['mc_9005']];
        }
        $order_id = F::generateOrderSn("weike");
        $order = new CommonOrder();
        $order->order_id = $order_id;
        $order->user_id = $user_id;
        $order->title = $microinfo->name;
        $order->order_type = 2;
        $order->price = $microinfo->price;
        $order->data = json_encode($microinfo->attributes);
        $order->createtime = date('Y-m-d H-i-s');

        if ($order->save()) {
            $data['status'] = '200';
            $data['data']['order_id'] = $order['order_id'];
            $data['data']['user_id'] = $user_id;
            $data['data']['title'] = $order['title'];
            $data['data']['createtime'] = $order['createtime'];
            $data['data']['order_coins'] = $order['price'] . '';
            $coininfo = StuCount::findOne(['user_id' => $user_id]);
            $data['data']['user_coins'] = intval($coininfo['coin']) . '';
        } else {
            $this->sendResponse('8601');
            $data['status'] = '9003';
            $data['msg'] = Yii::$app->params['mc_9003'];
        }
        return $data;
    }

    /**
     * 支付微课订单
     * @return array
     * @throws yii\db\Exception
     */
    public function actionPay()
    {
        $param = Yii::$app->request->post();
        $micro_id = isset($param['micro_id']) ? $param['micro_id'] : '';
//        echo $micro_id;exit;
        if (empty($micro_id)) {
            return ['status' => '9001', 'msg' => Yii::$app->params['mc_9001']];
        }
        $user_id = Yii::$app->user->getId();
        if (empty($user_id)) {
            return ['status' => '9020', 'msg' => Yii::$app->params['mc_9020']];
        }
        $userinfo = User::findOne(['user_id' => $user_id]);
        $username = $userinfo['username'];
        if ((int)$userinfo['group_id'] == 2) {
            return ['status' => '9021', 'msg' => Yii::$app->params['mc_9021']];
        }

        $microinfo = MicroCourse::findOne(['id' => $micro_id, 'xstatus' => 1, 'publish' => 1, 'isauth' => 1]);
        if (empty($microinfo)) {
            return ['status' => '9034', 'msg' => Yii::$app->params['mc_9034']];
        }
        if ($microinfo->price == 0) {
            return ['status' => '9002', 'msg' => Yii::$app->params['mc_9002']];
        }
        if ($user_id == $microinfo->user_id) {
            return ['status' => '9004', 'msg' => Yii::$app->params['mc_9004']];
        }
        $exists = MicroCourseOrder::find()
            ->where(['user_id' => $user_id, 'mc_id' => $micro_id, 'isdel' => 0])
            ->andWhere(['>', 'valid_time', time()])
            ->asArray()
            ->one();
        if (!empty($exists)) {
            return ['status' => '9005', 'msg' => Yii::$app->params['mc_9005']];
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $stu_coin_info = StuCount::findOne(['user_id' => $user_id]);
            $coin = (int)$stu_coin_info['coin'];
            $price = (int)$microinfo['price'];
            if (($coin <= 0) || ($coin < $price)) {
                return ['status' => '9010', 'msg' => Yii::$app->params['mc_9010']];
            }

            $order_id = F::generateOrderSn("weike");
            if (empty($order_id)) {
                return ['status' => '9006', 'msg' => Yii::$app->params['mc_9006']];
            }
            $common_order = new CommonOrder();
            $common_order->order_id = $order_id;
            $common_order->user_id = $user_id;
            $common_order->title = $microinfo->name;
            $common_order->order_type = 2;
            $common_order->price = $microinfo->price;
            $common_order->data = json_encode($microinfo->attributes);
            $common_order->createtime = date('Y-m-d H-i-s');

            if (!$common_order->save()) {
                $transaction->rollBack();
                return ['status' => '9003', 'msg' => Yii::$app->params['mc_9003']];
            }

            $stu_coin_info->coin = $coin - $price;
            $stu_coin_info->coureses_num += 1;
            if (!$stu_coin_info->save()) {
                $transaction->rollBack();
                return ['status' => '9011', 'msg' => Yii::$app->params['mc_9011']];
            }

            $order = new MicroCourseOrder();
            $order->order_id = $order_id;
            $order->user_id = $user_id;
            $order->mc_id = $micro_id;
            $order->price = $price;
            $order->valid_time = strtotime('+6 month');
            $order->isshow = 1;
            $order->isdel = 0;
            $order->view_nums = 0;
            $order->createtime = date('Y-m-d H-i-s');
            $order->updatetime = date('Y-m-d H-i-s');

            if (!$order->save()) {
                $transaction->rollBack();
                return ['status' => '9003', 'msg' => Yii::$app->params['mc_9003']];
            }

            // 更新支付成功状态
            $common_order->status = 3;
            if (!$common_order->save()) {
                $transaction->rollBack();
                return ['status' => '9012', 'msg' => Yii::$app->params['mc_9012']];
            }

            // 更新学生哇哇豆日志
            $coinlog = new CoinLog();
            $coinlog->user_id = $user_id;
            $coinlog->order_id = $order_id;
            $coinlog->order_type = 7;
            $coinlog->nums = $price;
            $coinlog->type = 0;
            $coinlog->remark = '购买微课' . $microinfo['name'];
            $coinlog->status = 2;
            $coinlog->createtime = date('Y-m-d H-i-s');
            if (!$coinlog->save()) {
                $transaction->rollBack();
                return ['status' => '9013', 'msg' => Yii::$app->params['mc_9013']];
            }

            // 更新老师哇哇豆
            $teacher = TchCount::findOne(['user_id' => $microinfo['user_id']]);
            if (empty($teacher)) {
                $transaction->rollBack();
                return ['status' => '9024', 'msg' => Yii::$app->params['mc_9024']];
            }
            //老师提成新规则,价格的一半
            $rating = 1;//$rating = $teacher['rating']
            $rating = Yii::$app->params['systemEduCoinRate'][$rating];
            $commission = floor($price * $rating);   // 佣金
            $income = $price - $commission;  // 收入
            $teacher->coin += $income;
            if ($microinfo['buynums'] <= 100) {
                $teacher->credits += 1;
                $level = TchCount::getRatingByCredits($teacher->credits);
                $teacher->rating = $level;
            }
            if (!$teacher->save()) {
                $transaction->rollBack();
                return ['status' => '9014', 'msg' => Yii::$app->params['mc_9014']];
            }

            // 更新老师哇哇豆日志
            $coinlog = new CoinLog();
            $coinlog->user_id = $microinfo['user_id'];
            $coinlog->order_id = $order_id;
            $coinlog->order_type = 7;
            $coinlog->nums = $income;
            $coinlog->type = 1;
            $coinlog->remark = '学生购买微课' . $microinfo['name'] . "($username)";
            $coinlog->createtime = date('Y-m-d H-i-s');
            if (!$coinlog->save()) {
                $transaction->rollBack();
                return ['status' => '9015', 'msg' => Yii::$app->params['mc_9015']];
            }

            //系统提成哇哇豆
//            $sysArray = array(
//                "coin" => $microinfo['price'],
//                "user_id" => $microinfo['user_id'],
//                "order_id" => $order_id,
//                "order_type" => 7,
//                "remark" => '学生购买微课'
//            );
            //$fee 有可能返回为 0 暂不做回滚处理
//            $fee = SystemEduCoin::doSystemEduCoin($sysArray);

            // 更新微课购买量
            $microinfo['buynums'] += 1;
            if (!$microinfo->save()) {
                $transaction->rollBack();
                return ['status' => '9023', 'msg' => Yii::$app->params['mc_9023']];
            }

            $transaction->commit(); //提交事务会真正的执行数据库操作

            CTask::done($user_id, 1, 'buyweike');

            $jpush = new JPushNotice();
            //学生
            $push_array = [
                "title" => "您购买${microinfo['name']}微课成功，系统已扣费${price}哇哇豆",
                "type" => 2006
            ];
            $jpush->send([$user_id], $push_array);

            //积分
            // $CreditRule= new CreditRule();
            // $CreditRule->studentCouresesBuy($user_id);

            return ['status' => '200', 'msg' => 'ok'];
        } catch (yii\db\Exception $e) {
            $transaction->rollBack();
            return ['status' => '9016', 'msg' => Yii::$app->params['mc_9016']];
        }
    }

    /**
     * 取消微课订单
     * @return array
     */
    public function actionCancel()
    {
        $param = Yii::$app->request->post();
        $order_id = isset($param['order_id']) ? $param['order_id'] : '';
        $user_id = Yii::$app->user->getId();
        if (empty($order_id)) {
            return ['status' => '9006', 'msg' => Yii::$app->params['mc_9006']];
        }

        $order = CommonOrder::findOne(['order_id' => $order_id, 'user_id' => $user_id]);
        if ((int)$order['status'] != 0) {
            return ['status' => '9019', 'msg' => Yii::$app->params['mc_9019']];
        }
        $order->status = 4;
        if ($order->save()) {
            return ['status' => '200'];
        } else {
            return ['status' => '9017', 'msg' => Yii::$app->params['mc_9017']];
        }
    }

    /**
     * 我的微课列表
     * @return mixed
     */
    public function actionMyList()
    {
        $param = Yii::$app->request->get();
        $page = isset($param['page']) ? $param['page'] : 1;
        $param = Yii::$app->request->post();
        $type = isset($param['type']) ? $param['type'] : '全部';
        $pageSize = isset($param['page_size']) ? $param['page_size'] : 10;
        $feed_id = isset($param['feed_id']) ? $param['feed_id'] : '';
        $slip_direction = isset($param['slip_direction']) ? $param['slip_direction'] : 'down';

        $user_id = Yii::$app->user->getId();

        $query = new Query;
        $query->from(MicroCourseOrder::tableName() . ' mco');
        $selectArray = [
            'mco.id',
            'mco.order_id',
            'mco.user_id',
            'mco.createtime',
            'mco.mc_id micro_id',
            'from_unixtime(mco.valid_time) valid_time',
            'mco.price',
            'mco.view_nums',
            'mc.name',
            'mc.user_id mc_user_id',
            'mc.realname',
            'mc.stagename',
            'mc.gradename',
            'mc.coursename',
            'mc.video_url',
            'mc.video_small_image',
            'mc.video_middle_image',
            'mc.video_big_image',
            'mc.video_duration',
            'mc.content',
            'mc.viewnums',
            'mc.favnums',
            'mc.price',
            'mc.buynums',
        ];
        $query->select($selectArray);
        $query->leftJoin(MicroCourse::tableName() . ' mc', "mc.id = mco.mc_id");
        $query->where(['mco.user_id' => $user_id, 'mco.isshow' => 1, 'mco.isdel' => 0]);


        if ($type == '过期') {
            $query->andWhere(['<=', 'valid_time', time()]);
        } else if ($type == '有效') {
            $query->andWhere(['>', 'valid_time', time()]);
        }

        $andWhere = [];
        if (!empty($feed_id)) {
            if ($slip_direction == 'up') {
                $andWhere = ['>', 'mco.id', $feed_id];
            } else {
                $andWhere = ['<', 'mco.id', $feed_id];
            }
        }
        if (!empty($andWhere)) {
            $query->andWhere($andWhere);
        }

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);

        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['mco.createtime' => SORT_DESC])
            ->all();

        $pageCount = $pages->getPageCount();
        $data['page_count'] = $pageCount . '';
        $data['page'] = $page . '';
        $data['page_size'] = ($pages->limit) . '';
        $data['data'] = [];
        if ($page <= $pageCount) {
            foreach ($rows as $row) {
                $row['username'] = $this->getUserName($row['mc_user_id']);
                $row['realname'] = $row['username'];
                $temp['feed_id'] = $row['id'];
                $temp['feed'] = $row;
                $data['data'][] = $temp;
            }
        }
        return $data;
    }

}

