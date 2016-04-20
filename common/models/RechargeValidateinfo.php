<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_recharge_validateinfo".
 *
 * @property integer $id
 * @property string $hash
 * @property string $code
 * @property integer $delflg
 * @property string $transaction_id
 */
class RechargeValidateinfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_recharge_validateinfo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'string'],
            [['delflg'], 'integer'],
            [['hash', 'transaction_id'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'hash' => 'hash码',
            'code' => '校验码',
            'delflg' => '删除标志:0有效，1删除',
            'transaction_id' => 'apple server 返回的数据',
        ];
    }
}
