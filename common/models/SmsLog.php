<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_sms_log".
 *
 * @property integer $id
 * @property string $username
 * @property string $uid
 * @property integer $version
 * @property string $datetime
 * @property string $status_code
 */
class SmsLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_sms_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['version'], 'integer'],
            [['datetime'], 'safe'],
            [['username'], 'string', 'max' => 100],
            [['uid'], 'string', 'max' => 18],
            [['status_code'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '短信ID',
            'username' => '用户名',
            'uid' => '版本',
            'version' => '版本号',
            'datetime' => '提交时间',
            'status_code' => '返回状态代码',
        ];
    }
}
