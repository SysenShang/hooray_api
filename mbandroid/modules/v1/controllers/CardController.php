<?php

namespace mbandroid\modules\v1\controllers;

use common\models\Cards;
use common\models\CoinLog;
use common\models\StuCount;
use common\models\F;
use yii\rest\ActiveController;
use yii;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\AccessControl;
use common\components\JPushNotice;

class CardController extends ActiveController
{
    public function init()
    {
        $this->modelClass = '';
        parent::init();
    }

    public function behaviors()
    {
        $behaviors                  = parent::behaviors();
        $behaviors['authenticator'] = [
            'except' => ['validated', 'gen', 'bind', 'active', 'cancel'],  // set actions for disable access!
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
                throw new yii\base\Exception('您无权访问该页面');
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
     * 充值卡兑换
     * @return string
     * @throws yii\db\Exception
     * @author grg
     */
    public function actionUse()
    {
        $userId   = Yii::$app->user->id;
        $username = Yii::$app->user->identity->username;
        $postData = Yii::$app->request->post();

        $postData['code'] = strtoupper(trim($postData['code']));

        $result = [];
        $date   = date('Y-m-d H:i:s');

        $transaction = Yii::$app->db->beginTransaction(yii\db\Transaction::SERIALIZABLE);
        try {
            //row lock
            $sql  = Cards::find()->where([
                'key' => $postData['code']
            ])->andWhere('`status` > -1')->limit(1)->createCommand()->getRawSql();
            $card = Cards::findBySql($sql . ' FOR UPDATE')->one();
            switch (true) {
                case null === $card || $card['expired_at'] < $date:
                    $transaction->rollBack();
                    $result['status'] = '6602';
                    return $result;
                case 0 === $card['status']:
                    $transaction->rollBack();
                    $result['status'] = '6606';
                    return $result;
                case 2 === $card['status']:
                    $transaction->rollBack();
                    $result['status'] = '6601';
                    return $result;
            }
            if ($card['id'] <= 3000) {
                $usedCard = Cards::find()->where(['user_id' => $userId])->andWhere('id <= 3000')->limit(1)->one();
                if (null !== $usedCard) {
                    $transaction->rollBack();
                    $result['status'] = '6607';
                    return $result;
                }
            }
            $orderId  = F::generateOrderSn(null);
            $affected = Cards::updateAll([
                'status' => 2,
                'user_id' => $userId,
                'username' => $username,
                'order_id' => $orderId,
                'used_at' => $date
            ], [
                'id' => $card['id']
            ]);
            if (1 !== $affected) {
                $transaction->rollBack();
                $result['status'] = '6603';
                return $result;
            }
            $affected = StuCount::updateAllCounters(['coin' => $card['price']], ['user_id' => $userId]);
            if (1 !== $affected) {
                $transaction->rollBack();
                $result['status'] = '6603';
                return $result;
            }

            $coinLog = new CoinLog();

            $coinLog['user_id']    = $userId;
            $coinLog['order_id']   = $orderId;
            $coinLog['order_type'] = 5;
            $coinLog['nums']       = $card['price'];
            $coinLog['type']       = 1;
            $coinLog['remark']     = "学习卡({$postData['code']})兑换成功,获得哇哇豆({$card['price']}).";
            $coinLog['detail']     = serialize([
                'code' => $postData['code']
            ]);
            $coinLog['status']     = 2;
            $coinLog['createtime'] = $date;
            $coinLog->save();

            $transaction->commit();

            $jpush = new JPushNotice();
            $msg   = "您的学习卡({$postData['code']})兑换成功，获得哇哇豆{$card['price']}个！";
            $jpush->send([$userId], ['type' => '4000', 'title' => $msg]);
            return '';
        } catch (yii\db\Exception $e) {
            $transaction->rollBack();
            $result['status'] = '6603';
            return $result;
        }

    }
}
