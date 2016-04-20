<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_passport_student_log".
 *
 * @property string $user_id
 * @property string $regip
 * @property string $lastlogin
 * @property string $version
 * @property string $logintime
 * @property string $logouttime
 */
class StuLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_passport_student_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['lastlogin', 'logintime', 'logouttime'], 'safe'],
            [['user_id'], 'string', 'max' => 18],
            [['regip'], 'string', 'max' => 15],
            [['version'], 'string', 'max' => 30],
            [['user_id'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'regip' => 'Regip',
            'lastlogin' => 'Lastlogin',
            'version' => 'Version',
            'logintime' => 'Logintime',
            'logouttime' => 'Logouttime',
        ];
    }
}
