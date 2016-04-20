<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_passport_users_login_history".
 *
 * @property string $user_id
 * @property string $login_at
 * @property string $ip
 * @property string $ua
 */
class UsersLoginHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_passport_users_login_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['login_at'], 'safe'],
            [['user_id', 'ip'], 'string', 'max' => 20],
            [['ua'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'login_at' => 'Login At',
            'ip' => 'Ip',
            'ua' => 'Ua',
        ];
    }
}
