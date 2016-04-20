<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_recharge_order".
 *
 * @property integer $id
 * @property string $order_id
 * @property string $trade_no
 * @property string $title
 * @property string $user_id
 * @property string $buyer_id
 * @property string $buyer_email
 * @property double $total_price
 * @property integer $coin
 * @property integer $status
 * @property string $createtime
 * @property string $updatetime
 * @property string $return_url
 */
class RechargeOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_recharge_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['total_price'], 'number'],
            [['coin', 'status'], 'integer'],
            [['createtime', 'updatetime'], 'safe'],
            [['return_url'], 'string'],
            [['order_id', 'trade_no'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 200],
            [['user_id'], 'string', 'max' => 18],
            [['buyer_id'], 'string', 'max' => 30],
            [['buyer_email'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'order_id' => '充值订单id',
            'trade_no' => '支付宝交易号',
            'title' => '标题',
            'user_id' => '用户id',
            'buyer_id' => '买家taobao id',
            'buyer_email' => '买家email',
            'total_price' => '费用',
            'coin' => '学币数',
            'status' => '状态:0待支付,1支付失败,2支付成功',
            'createtime' => '增加时间',
            'updatetime' => '更新时间',
            'return_url' => '返回url',
        ];
    }

    public static function addCoins($transcationId, $ios_pay = true)
    {
        $db    = Yii::$app->db;

        $order = self::findOne(['trade_no' => $transcationId]);
        if (empty($order) || $order['status'] == 2) {
            if ($ios_pay) {
                $query = "update edu_recharge_validateinfo set delflg = 1 where transaction_id = '$transcationId'";//清除 队列 有效标志
                $db->createCommand($query)->execute();
            }
            return '';
        }
        $sqlArray   = array();
        $sqlArray[] = "update edu_recharge_order set status = 2 where status < 2 and order_id = '$order[order_id]'";

        $remark     = "充值哇哇豆(" . $order['coin'] . ")";
        $detail     = serialize([
            'trans_id' => $transcationId
        ]);
        $sqlArray[] = "insert into edu_coin_log (user_id, order_id, order_type, nums, type, remark, detail) values('$order[user_id]', '$order[order_id]', 5, $order[coin], 1, '$remark', '$detail')";

        $user = User::findOne(['user_id' => $order['user_id']]);
        if ($user['group_id'] == 1) {
            $coin = PassportStudentCount::find()->select('coin')->where(['user_id' => $order['user_id']])->one();
            if ($coin) {
                $sqlArray[] = "update edu_passport_student_count set coin = coin + $order[coin] where user_id = '$order[user_id]'";
            } else {
                $sqlArray[] = "insert into edu_passport_student_count (user_id, coin) values('$order[user_id]', $order[coin])";
            }
        } else if ($user['group_id'] == 2) {
            $coin = PassportTeacherCount::find()->select('coin')->where(['user_id' => $order['user_id']])->one();
            if ($coin) {
                $sqlArray[] = "update edu_passport_teacher_count set coin = coin + $order[coin] where user_id = '$order[user_id]'";
            } else {
                $sqlArray[] = "insert into edu_passport_teacher_count (user_id, coin) values('$order[user_id]', $order[coin])";
            }
        }
        if ($ios_pay) {
            $sqlArray[] = "update edu_recharge_validateinfo set delflg = 1 where transaction_id = '$transcationId'";//清除 队列 有效标志
        }

        if (empty($sqlArray)) {
            return false;
        }
        $trans = $db->beginTransaction();
        try {
            foreach ($sqlArray as $sql) {
                $flg = $db->createCommand($sql)->execute();
                if ($flg < 1) {
                    $trans->rollBack();
                    return false;
                }
            }
            $trans->commit();
            return true;
        } catch (Exception $e) {
            $trans->rollBack();
            return false;
        }
    }

}
