<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_passport_teacher_log".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $regip
 * @property string $lastlogin
 * @property string $version
 * @property string $logouttime
 * @property string $logintime
 */
class TchLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_passport_teacher_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lastlogin', 'logouttime', 'logintime'], 'safe'],
            [['user_id'], 'string', 'max' => 18],
            [['regip'], 'string', 'max' => 15],
            [['version'], 'string', 'max' => 30]
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
            'regip' => 'Regip',
            'lastlogin' => 'Lastlogin',
            'version' => 'Version',
            'logouttime' => 'Logouttime',
            'logintime' => 'Logintime',
        ];
    }
}
