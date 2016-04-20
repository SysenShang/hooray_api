<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_cards".
 *
 * @property integer $id
 * @property string $prefix
 * @property string $key
 * @property string $crypt
 * @property integer $price
 * @property string $user_id
 * @property string $username
 * @property string $order_id
 * @property integer $status
 * @property string $used_at
 * @property string $created_at
 * @property string $expired_at
 */
class Cards extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_cards';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price', 'status'], 'integer'],
            [['used_at', 'created_at', 'expired_at'], 'safe'],
            [['prefix'], 'string', 'max' => 3],
            [['key'], 'string', 'max' => 15],
            [['crypt'], 'string', 'max' => 100],
            [['user_id'], 'string', 'max' => 18],
            [['username', 'order_id'], 'string', 'max' => 64],
            [['key'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'prefix' => 'Prefix',
            'key' => 'Key',
            'crypt' => 'Crypt',
            'price' => 'Price',
            'user_id' => 'User ID',
            'username' => 'Username',
            'order_id' => 'Order ID',
            'status' => 'Status',
            'used_at' => 'Used At',
            'created_at' => 'Created At',
            'expired_at' => 'Expired At',
        ];
    }
}
