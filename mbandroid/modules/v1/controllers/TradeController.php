<?php

namespace mbandroid\modules\v1\controllers;

use common\models\CoinLog;
use common\models\CoinProduct;


use common\models\RechargeOrder;
use common\models\RechargeValidateinfo;
use common\models\F;
use common\components\AlipayNotify;
use common\models\PaymentDetailLog;
use common\models\TchCount;

use yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\AccessControl;
use yii\db\Query;
use yii\data\Pagination;

class TradeController extends ActiveController
{
    public $modelClass = 'common\models\CoinLog';

    public function behaviors()
    {
        $behaviors                  = parent::behaviors();
        $behaviors['authenticator'] = [
            'except' => ['notify','confirm','status'],  // set actions for disable access!
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        $behaviors['access']        = [
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

        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view'], $actions['put'], $actions['head']);

        return $actions;
    }

    public function actionIndex($type = null)
    {
        $uid     = Yii::$app->user->getId();
        $getData = Yii::$app->request->get();
        $query   = new Query;
        $query->from(CoinLog::tableName());
        $query->select("(CASE type WHEN 0 THEN '支出' WHEN 1 THEN '收入' END) type,order_id,remark,(CASE order_type WHEN 0 THEN '问答' WHEN 1 THEN '课程' WHEN 2 THEN '登录' WHEN 4 THEN '注册' WHEN 5 THEN '充值' WHEN 6 THEN '老师提现' WHEN 7 THEN '购买微课' WHEN 8 THEN '其它' WHEN 9 THEN '考试苑' WHEN 10 THEN '签到' END) label,nums,createtime");
        $cont = ['user_id' => $uid, 'status' => 2];
        if (!is_null($type)) {
            $cont['type'] = $type;
        }
        if (array_key_exists('order_type', $getData)) {
            $cont['order_type'] = $getData['order_type'];
        }
        $query->where($cont);
        if (array_key_exists('order_type_specify', $getData) && $getData['order_type_specify'] == 1) {
            $query->andWhere("order_type in (7, 11)");
        }
        if (array_key_exists('createtime', $getData)) {
            $query->andWhere("createtime >= '{$getData['createtime']}' ");
        }
        $countQuery = clone $query;
        $pages      = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        $coins      = $query->orderBy('createtime DESC')->offset($pages->offset)->limit($pages->limit)->all();
        $totalCoins = $query->sum('nums');

        if ($pages->pageCount > 0) {
            $data['total_coins']       = $totalCoins;
            $data['total_page'] = $pages->pageCount;
            $data['list']       = $coins;
        } else {
            $data['list'] = [];
        }

        return $data;
    }

    public function actionIncome()
    {
        return $this->actionIndex(1);
    }

    public function actionOutgo()
    {
        return $this->actionIndex(0);
    }

    /**
     * 哇哇豆人民币汇率
     * @return array
     * @author grg
     */
    public function actionCoinrate()
    {
        $rate = Yii::$app->redis->hget('common.setting', 'coin_exchange_rate');
        return ['rate' => empty($rate) ? (time() < strtotime('2015-10-29 23:59:59') ? '0.01' : '0.5') : $rate];
    }

    public function actionCoincard()
    {
        return CoinProduct::find()->select('product_id,nums,remark')->where('status = 1')->all();
    }

    /**
     * 充值订单
     * @author grg
     */
    public function actionPurchase()
    {
        $postData = Yii::$app->request->post();
        if (array_key_exists('coins', $postData)) {
            $coins = (int)$postData['coins'];
            if ($coins == 0) {
                Yii::$app->response->statusCode = 400;
                return ['status' => 6451];
            }
        } elseif (array_key_exists('product', $postData)) {
            $product = $postData['product'];
            if (empty($product)) {
                Yii::$app->response->statusCode = 400;
                return ['status' => 6451];
            }
            $coin = CoinProduct::find()->select('nums')->where('product_id = "' . $product . '"')->one();
            $coins = $coin['nums'];
        }

        $order = new RechargeOrder();
        $order->order_id = F::generateOrderSn('recharge');
        $order->title = '哇哇豆充值(' . $coins . ')';
        $order->user_id = Yii::$app->user->getId();
        $order->total_price = strval($coins * floatval($this->actionCoinrate()['rate']));
        $order->coin = (string)$coins;
        $order->createtime = $order->updatetime = date('Y-m-d H:i:s');
        $order->gateway = isset($postData['gateway']) ? $postData['gateway']:'Alipay';

        if (isset($postData['gateway']) && $postData['gateway'] == "wechatPayment") {
            $config=array(
                'appId' => Yii::$app->params['wechatPayment']['appId'],
                'merchantId' => Yii::$app->params['wechatPayment']['merchantId'],
                'appKey' => Yii::$app->params['wechatPayment']['appKey'],
                'appSecret' => Yii::$app->params['wechatPayment']['appSecret'],
                'notifyUrl' => Yii::$app->params['wechatPayment']['notifyUrl'],
                'tradeType' => Yii::$app->params['wechatPayment']['tradeType'],
            );

            $orderParams = array(
                'body' => $order->title,
                'out_trade_no' => $order->order_id,
                'total_fee' => $order->total_price * 100 //Unit is fen
            );
            $wechatPayment=Yii::$app->wechatpayment->createUniqueOrder($orderParams,$config);

            if($wechatPayment['return_code'] == 'SUCCESS') {
                $order->trade_no = $wechatPayment['prepay_id'];
                $order->save();
            }
            $order= array_merge((array)$order->attributes, Yii::$app->wechatpayment->getOrderParams());

            $paymentDetailLog = new PaymentDetailLog();
            $paymentDetailLog->trade_no = $wechatPayment['prepay_id'];
            $paymentDetailLog->gateway = $postData['gateway'];
            $paymentDetailLog->content = "create order:".json_encode($order, JSON_UNESCAPED_UNICODE);
            $paymentDetailLog->created_at = date("Y-m-d H:i:s");
            $paymentDetailLog->save();
        } else {
            $order->save();
        }

        return array_key_exists('product', $postData) ? ['order_id' => $order['order_id']] : $order;
    }

    /**
     * 请求确认充值支付结果
     * @return array|string
     * @author grg
     */
    public function actionConfirm()
    {
        if (Yii::$app->request->isGet) {
            return '';
        }
        $postData = Yii::$app->request->post();
        $this->log('接受到待确认数据: ' . json_encode($postData, JSON_UNESCAPED_UNICODE));

        $receipt_data = $postData['receipt-data'];
        $transaction  = $postData['transaction'];
        if (empty($receipt_data) || empty($transaction) || !is_array($transaction)) {
            Yii::$app->response->statusCode = 500;
            return '';
        }
        $process = shell_exec('ps x | grep ConfirmiOSPay');
        if (strpos($process, 'php') === false) {
            //异步
            proc_close(proc_open('php '.__DIR__.'/../../../../console/yii ConfirmiOSPay &', [], $foo));
        }
        //检查数据是否已存在
        $hash       = md5(json_encode($postData));
        $hashResult = RechargeValidateinfo::findOne(['hash' => $hash]);
        if (null !== $hashResult) {
            return ['status' => '200'];
        } else {
            $trans = Yii::$app->db->beginTransaction();
            try {
                $transaction_id = '';
                //绑定order_id 与 transaction_id
                foreach ($transaction as $item) {
                    $flg = RechargeOrder::updateAll(['trade_no' => $item['apple-transcation-id']], ['order_id' => $item['hooray-order-id']]);
                    if (!$flg) {
                        $exists = RechargeOrder::findOne(['trade_no' => $item['apple-transcation-id'], 'order_id' => $item['hooray-order-id']]);
                        if (null === $exists) {
                            throw new yii\db\Exception('充值订单' . $item['hooray-order-id'] . '不存在!');
                        }
                    }
                    $transaction_id = $item['apple-transcation-id'];
                }

                $exists_info = RechargeValidateinfo::findOne(['transaction_id' => $transaction_id]);
                if (null !== $exists_info) {//更新
                    $exists_info->hash = $hash;
                    $exists_info->code = $receipt_data;
                    $exists_info->save();
                } else {
                    $hashResult                 = new RechargeValidateinfo();
                    $hashResult->hash           = $hash;
                    $hashResult->code           = $receipt_data;
                    $hashResult->transaction_id = $transaction_id;
                    if (!$hashResult->save()) {
                        $trans->rollBack();
                        throw new yii\db\Exception('待确认数据保存失败!');
                    }
                }

                $trans->commit();
                return ['status' => '200'];
            } catch (yii\db\Exception $e) {
                $this->log($e->getMessage());
                Yii::$app->response->statusCode = 400;
                return ['status' => '400'];
            }
        }
    }

    /**
     * Alipay Notify
     * @author grg
     */
    public function actionNotify()
    {
        ob_end_clean();
        $resultArray = Yii::$app->request->post();
        if (array_key_exists('return_code', $resultArray) || (array_key_exists('HTTP_REFERER', $_SERVER) && strpos($_SERVER['HTTP_REFERER'], 'weixin') !== false)) {
            if (Yii::$app->wechatpayment->notify($resultArray) === true) {
                RechargeOrder::updateAll(['trade_no' => "{$resultArray['transaction_id']}"], 'order_id = "' . $resultArray['out_trade_no'] . '"');
                $flg = RechargeOrder::addCoins($resultArray['transaction_id'], false);
                if ($flg) {
                    $paymentDetailLog = new PaymentDetailLog();
                    $paymentDetailLog->order_id = $resultArray['out_trade_no'];
                    $paymentDetailLog->trade_no = $resultArray['transaction_id'];
                    $paymentDetailLog->gateway = "wechatPayment";
                    $paymentDetailLog->content = "Notify success:".json_encode($resultArray, JSON_UNESCAPED_UNICODE);
                    $paymentDetailLog->created_at = date("Y-m-d H:i:s");
                    $paymentDetailLog->save();
                    return '<xml>
                                <return_code><![CDATA[SUCCESS]]></return_code>
                                <return_msg><![CDATA[OK]]></return_msg>
                            </xml>';
                } else {
                    $paymentDetailLog = new PaymentDetailLog();
                    $paymentDetailLog->order_id = $resultArray['out_trade_no'];
                    $paymentDetailLog->trade_no = $resultArray['transaction_id'];
                    $paymentDetailLog->gateway = "wechatPayment";
                    $paymentDetailLog->content = "Notify fail:".json_encode($resultArray, JSON_UNESCAPED_UNICODE);
                    $paymentDetailLog->created_at = date("Y-m-d H:i:s");
                    $paymentDetailLog->save();
                    return '<xml>
                                <return_code><![CDATA[FAIL]]></return_code>
                                <return_msg><![CDATA[NO]]></return_msg>
                            </xml>';
                }
            } else {
                return '<xml>
                            <return_code><![CDATA[FAIL]]></return_code>
                            <return_msg><![CDATA[NO]]></return_msg>
                        </xml>';
            }
        } else {
            if (empty($_POST)) {
                echo 'fail';
            } else {
                $alipayNotify = new AlipayNotify();
                $alipayNotify->logResult('接收到参数: ' . json_encode($_POST, JSON_UNESCAPED_UNICODE));
                try {
                    $verify_result = $alipayNotify->verifyNotify();
                    if ($verify_result) {
                        $alipayNotify->logResult('校验通过.');

                        if ($_POST['trade_status'] == 'TRADE_SUCCESS' || $_POST['trade_status'] == 'TRADE_FINISHED') {
                            $order = RechargeOrder::find()
                                                  ->where(['order_id' => '"' . $_POST['out_trade_no'] . '"'])
                                                  ->limit(1)
                                                  ->asArray()
                                                  ->one();
                            if ((int)$order['status'] === 2) {
                                $alipayNotify->logResult('再次通知.');
                                echo 'success';
                            } else {
                                $alipayNotify->logResult('相关订单信息:' . json_encode($order, JSON_UNESCAPED_UNICODE));
                                RechargeOrder::updateAll(['trade_no' => "{$_POST['trade_no']}"], 'order_id = "' . $_POST['out_trade_no'] . '"');
                                $flg = RechargeOrder::addCoins($_POST['trade_no'], false);
                                if ($flg) {
                                    $alipayNotify->logResult('操作成功.');
                                    echo 'success';
                                } else {
                                    $alipayNotify->logResult('送豆操作失败.');
                                    echo 'fail';
                                }
                                $paymentDetailLog             = new PaymentDetailLog();
                                $paymentDetailLog->order_id   = $_POST['out_trade_no'];
                                $paymentDetailLog->trade_no   = $_POST['trade_no'];
                                $paymentDetailLog->gateway    = 'AliPayment';
                                $paymentDetailLog->content    = 'Notify ' . ($flg ? 'success' : 'fail') . json_encode($_POST, JSON_UNESCAPED_UNICODE);
                                $paymentDetailLog->created_at = date('Y-m-d H:i:s');
                                $paymentDetailLog->save();
                            }
                        } else {
                            $alipayNotify->logResult('支付未完成. 支付状态为:' . $_POST['trade_status']);
                            echo 'fail';
                        }
                    } else {
                        $alipayNotify->logResult('校验失败.');
                        echo 'fail';
                    }
                } catch (Exception $e) {
                    $alipayNotify->logResult('Exception:  ' . $e->getMessage());
                    echo 'fail';
                }
                $alipayNotify->logResult('处理完成.');
            }
        }
    }

    public function actionStatus()
    {
        $postData = Yii::$app->request->post();
        if (empty($postData) || empty($postData['transcation-id'])) {
            Yii::$app->response->statusCode = 500;
            return '';
        }
        $transcation_id = $postData['transcation-id'];
        if(isset($postData['gateway']) && $postData['gateway']) {
            $order = RechargeOrder::findOne(['order_id' => $transcation_id, 'gateway' => $postData['gateway']]);			
			if($order) {
	            $paymentDetailLog = new PaymentDetailLog();
				$paymentDetailLog->order_id = $order->order_id;
	            $paymentDetailLog->gateway = $postData['gateway'];
	            $paymentDetailLog->content = "queryStatus".json_encode($postData, JSON_UNESCAPED_UNICODE).json_encode($order->attributes, JSON_UNESCAPED_UNICODE);
	            $paymentDetailLog->created_at = date("Y-m-d H:i:s");
	            $paymentDetailLog->save();				
			} else {
            	return ['status' => '400', 'msg' => 'fail'];
			}
        } else {
            $order = RechargeOrder::findOne(['trade_no' => $transcation_id]);
        }
		
        if (!empty($order)) {
            if ($order['status'] == 2) {
                return ['status' => '200'];
            } elseif ($order['status'] == 0) {
                return ['status' => '200', 'msg' => 'confirming'];
            } else {
                return ['status' => '400', 'msg' => 'fail'];
            }
        } else {
            Yii::$app->response->statusCode = 400;
            return '';
        }
    }

    /**
     * apply for cash by coin 申请提现
     * params @$post['coin'] : 1
     */
    public function actionApplyForCash()
    {
        $userId = Yii::$app->user->getId();
        $groupId = Yii::$app->user->identity->group_id;
        if ($groupId !== 2) {
            return ['status' => '2040', 'msg' => Yii::$app->params['u_2040']];
        }

        if (Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            if (!isset($post['coin']) || empty($post['coin'])) {
                return ['status' => '5001', 'msg' => Yii::$app->params['s_5001']];
            }
            $coin = $post['coin'];
            if ($coin < 100) {
                return ['status' => '2044', 'msg' => Yii::$app->params['u_2044']];
            }
            $teacherCount = TchCount::findOne($userId);
            if($teacherCount->apply_for_cash == 0) {
                if($teacherCount->coin > $coin) {
                    $teacherCount->apply_for_cash = $post['coin'];
                    $teacherCount->save();
                    return ['status' => '200', 'msg' => 'successful'];
                } else {
                    return ['status' => '2042', 'msg' => Yii::$app->params['u_2042']];
                }
            } else {
                return ['status' => '2043', 'msg' => Yii::$app->params['u_2043']];
            }
        }
    }

    private function log($txt)
    {
        $path = Yii::$app->params['external_log_path'];
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        $logFile = fopen($path . '/iap_confirm_log.txt', 'a');
        flock($logFile, LOCK_EX);
        fwrite($logFile, date('Y-m-d H:i:s') . "\n" . $txt . "\n");
        flock($logFile, LOCK_UN);
        fclose($logFile);
    }
}
