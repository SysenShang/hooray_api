<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_micro_course_viewlog".
 *
 * @property integer $id
 * @property string $user_id
 * @property integer $mc_id
 * @property integer $mc_orderid
 * @property integer $isdel
 * @property string $updatetime
 */
class MicroViewlog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_micro_course_viewlog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mc_id', 'mc_orderid', 'isdel'], 'integer'],
            [['updatetime'], 'safe'],
            [['user_id'], 'string', 'max' => 18]
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
            'mc_id' => 'Mc ID',
            'mc_orderid' => 'Mc Orderid',
            'isdel' => 'Isdel',
            'updatetime' => 'Updatetime',
        ];
    }
}
