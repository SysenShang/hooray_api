<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_coin_product".
 *
 * @property integer $id
 * @property string $product_id
 * @property integer $price
 * @property integer $nums
 * @property string $remark
 * @property integer $status
 * @property string $createtime
 */
class CoinProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_coin_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price', 'nums', 'status'], 'integer'],
            [['createtime'], 'safe'],
            [['product_id'], 'string', 'max' => 50],
            [['remark'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '哇哇豆商品id',
            'product_id' => 'product id',
            'price' => '价格',
            'nums' => '哇哇豆数量',
            'remark' => '备注',
            'status' => '状态:0无效,1有效',
            'createtime' => '生成时间',
        ];
    }
}
