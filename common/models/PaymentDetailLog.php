<?php
/** 
 * Created by Aptana studio. 
 * User: Kevin Henry Gates III at Hihooray,Inc 
 * Date: 2015/12/23  
 * Time: 11:41 AM 
 */
namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_payment_detail_logs".
 *
 * @property integer $id
 * @property string $order_id
 * @property string $trade_no
 * @property string $gateway
 * @property string $content
 * @property string $created_at
 */
class PaymentDetailLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_payment_detail_logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'required'],
            [['content'], 'string'],
            [['created_at'], 'safe'],
            [['order_id', 'trade_no'], 'string', 'max' => 64],
            [['gateway'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'trade_no' => 'Trade No',
            'gateway' => 'Gateway',
            'content' => 'Content',
            'created_at' => 'Created At',
        ];
    }
}
