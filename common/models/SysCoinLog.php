<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_system_educoin_log".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $order_id
 * @property integer $order_type
 * @property integer $nums
 * @property string $remark
 * @property string $createtime
 */
class SysCoinLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_system_educoin_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_type', 'nums'], 'integer'],
            [['createtime'], 'safe'],
            [['user_id'], 'string', 'max' => 18],
            [['order_id'], 'string', 'max' => 64],
            [['remark'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'order_id' => 'Order ID',
            'order_type' => 'Order Type',
            'nums' => 'Nums',
            'remark' => 'Remark',
            'createtime' => 'Createtime',
        ];
    }
}
