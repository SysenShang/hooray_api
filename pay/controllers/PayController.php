<?php
/**
 * Created by PhpStorm.
 * User: kevingates
 * Date: 2016-2-1
 * Time: 3:56pm
 * Alipay Express Gateway
 */
namespace pay\controllers;

use Yii;
use common\models\RechargeOrder;
use common\models\F;
use common\models\PaymentDetailLog;
use common\models\CommonSetting;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * PayController implements the CRUD actions for RechargeOrder model.
 */
class PayController extends Controller
{
    private $commonSettings;

    public function init()
    {
        $this->commonSettings = CommonSetting::find()
            ->where(['scope' => 'payment'])
            ->orderBy('id asc')
            ->all();
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['notify'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Creates a new RechargeOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RechargeOrder();
        $post = Yii::$app->request->post();
        $get = Yii::$app->request->get();
        $coinRateArray = Yii::$app->params['coinRate'];

        if ($post && $post['RechargeOrder']['coin'] && $post['RechargeOrder']['gateway']) {
            $coins = (int)$post['RechargeOrder']['coin'];
            $rechargeOrder = new RechargeOrder();
            $rechargeOrder->order_id = $rechargeOrder->trade_no= F::generateOrderSn('recharge');
            $rechargeOrder->title = '哇哇豆充值(' . $coins . ')';
            $rechargeOrder->user_id = Yii::$app->user->getId();
            $rechargeOrder->total_price = strval($post['RechargeOrder']['total_price']);
            $rechargeOrder->coin = (string)$coins;
            $rechargeOrder->createtime = $rechargeOrder->updatetime = date('Y-m-d H:i:s');
            $rechargeOrder->gateway = $post['RechargeOrder']['gateway'];

            if ($post['RechargeOrder']['gateway'] == "alipayExpressGateway") {
                $alipayExpress = Yii::$app->alipayExpressGateway;
                $alipayExpress->partner = Yii::$app->params['alipayExpressGateway']['partner'];
                $alipayExpress->setKey(Yii::$app->params['alipayExpressGateway']['key']);
                $alipayExpress->seller_email = Yii::$app->params['alipayExpressGateway']['seller_email'];
                $alipayExpress->return_url = Yii::$app->params['alipayExpressGateway']['return_url'];
                $alipayExpress->notify_url = Yii::$app->params['alipayExpressGateway']['notify_url'];
                $alipayExpress->out_trade_no = $rechargeOrder->order_id;
                $alipayExpress->subject = $rechargeOrder->title;
                $alipayExpress->total_fee = $rechargeOrder->total_price;

                $alipayUrl = $alipayExpress->createPurchase();
                if ($alipayUrl) {
                    if ($rechargeOrder->save()) {
                        $this->redirect($alipayUrl);
                    } else {
                        Yii::$app->session->setFlash('error', '创建订单失败,create order failed!');
                        $this->redirect("/pay/create");
                    }
                }
            } elseif ($post['RechargeOrder']['gateway'] == "wechatWebPay") {
                //to do
                $config=array(
                    'appId' => Yii::$app->params['wechatPaymentWeb']['appId'],
                    'merchantId' => Yii::$app->params['wechatPaymentWeb']['merchantId'],
                    'appKey' => Yii::$app->params['wechatPaymentWeb']['appKey'],
                    'appSecret' => Yii::$app->params['wechatPaymentWeb']['appSecret'],
                    'notifyUrl' => Yii::$app->params['wechatPaymentWeb']['notifyUrl'],
                    'tradeType' => Yii::$app->params['wechatPaymentWeb']['tradeType'],
                );

                $orderParams = array(
                    'body' => $rechargeOrder->title,
                    'out_trade_no' => $rechargeOrder->order_id,
                    'total_fee' => $rechargeOrder->total_price * 100 //Unit is fen
                );
                $wechatPayment=Yii::$app->wechatpayment->createUniqueOrder($orderParams, $config);

                if ($wechatPayment['return_code'] == 'SUCCESS') {
                    $rechargeOrder->trade_no = $wechatPayment['prepay_id'];
                    $rechargeOrder->save();
                }
                $order= array_merge((array)$rechargeOrder->attributes, Yii::$app->wechatpayment->getOrderParams());

                $paymentDetailLog = new PaymentDetailLog();
                $paymentDetailLog->trade_no = $wechatPayment['prepay_id'];
                $paymentDetailLog->gateway = $post['RechargeOrder']['gateway'];
                $paymentDetailLog->content = "create order:".json_encode($order, JSON_UNESCAPED_UNICODE);
                $paymentDetailLog->created_at = date("Y-m-d H:i:s");
                $paymentDetailLog->save();

                return $this->render('QRCode', [
                    'wechatCodeUrl' => $wechatPayment['code_url'],
                    'totalPrice' => $rechargeOrder->total_price,
                ]);
            }
        } else {
            $model->coin = 1;
            $model->gateway = "alipayExpressGateway";
            $model->total_price = 0.5;

            return $this->render('create', [
                'model' => $model,
                'get' => $get,
                'coinRateArray' => $coinRateArray,
                'commonSettings' => $this->commonSettings,
            ]);
        }
    }

    /**
     * Notify 支付结果通知
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionNotify()
    {
        $post = Yii::$app->request->post();
        if ($post) {
            $paymentDetailLog = new PaymentDetailLog();
            $paymentDetailLog->order_id = $post['out_trade_no'];
            $paymentDetailLog->trade_no = $post['trade_no'];
            $paymentDetailLog->gateway = "alipayExpressGateway";
            $paymentDetailLog->content = "Notify Info:".json_encode($post, JSON_UNESCAPED_UNICODE);
            $paymentDetailLog->created_at = date("Y-m-d H:i:s");
            $paymentDetailLog->save();
        }

        Yii::$app->alipayExpressGateway->partner = Yii::$app->params['alipayExpressGateway']['partner'];
        Yii::$app->alipayExpressGateway->setKey(Yii::$app->params['alipayExpressGateway']['key']);

        if ($post && Yii::$app->alipayExpressGateway->completePurchase($post) == true) {
            if ($post['trade_status'] == 'TRADE_FINISHED' || $post['trade_status'] == 'TRADE_SUCCESS') {
                RechargeOrder::updateAll(['trade_no' => "{$post['trade_no']}"], 'order_id = "' . $post['out_trade_no'] . '"');
                $flg = RechargeOrder::addCoins($post['trade_no'], false);
                if ($flg) {
                    $paymentDetailLog = new PaymentDetailLog();
                    $paymentDetailLog->order_id = $post['out_trade_no'];
                    $paymentDetailLog->trade_no = $post['trade_no'];
                    $paymentDetailLog->gateway = "alipayExpressGateway";
                    $paymentDetailLog->content = "Notify success:".json_encode($post, JSON_UNESCAPED_UNICODE);
                    $paymentDetailLog->created_at = date("Y-m-d H:i:s");
                    $paymentDetailLog->save();
                    echo "success";//请不要修改或删除
                } else {
                    $paymentDetailLog = new PaymentDetailLog();
                    $paymentDetailLog->order_id = $post['out_trade_no'];
                    $paymentDetailLog->trade_no = $post['trade_no'];
                    $paymentDetailLog->gateway = "alipayExpressGateway";
                    $paymentDetailLog->content = "Notify fail:".json_encode($post, JSON_UNESCAPED_UNICODE);
                    $paymentDetailLog->created_at = date("Y-m-d H:i:s");
                    $paymentDetailLog->save();
                    echo "fail";//请不要修改或删除
                }
            } else {
                echo "trade status fail";//请不要修改或删除
            }
        } else {
            echo "verify Notify fail";//请不要修改或删除
        }
    }

    /**
     * Finds the RechargeOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RechargeOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RechargeOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
