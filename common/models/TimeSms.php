<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_time_sms".
 *
 * @property integer $id
 * @property string $mobile
 * @property string $msg
 * @property string $at_time
 */
class TimeSms extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_time_sms';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['at_time'], 'safe'],
            [['mobile'], 'string', 'max' => 30],
            [['msg'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mobile' => '手机号',
            'msg' => '消息内容',
            'at_time' => 'At Time',
        ];
    }
}
