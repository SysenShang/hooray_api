<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_common_order".
 *
 * @property integer $id
 * @property string $order_id
 * @property string $user_id
 * @property string $title
 * @property integer $order_type
 * @property integer $price
 * @property string $data
 * @property string $createtime
 * @property integer $status
 */
class CommonOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_common_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_type', 'price', 'status'], 'integer'],
            [['data'], 'string'],
            [['createtime'], 'safe'],
            [['order_id'], 'string', 'max' => 60],
            [['user_id'], 'string', 'max' => 18],
            [['title'], 'string', 'max' => 255]
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
            'user_id' => 'User ID',
            'title' => 'Title',
            'order_type' => 'Order Type',
            'price' => 'Price',
            'data' => 'Data',
            'createtime' => 'Createtime',
            'status' => 'Status',
        ];
    }
}
