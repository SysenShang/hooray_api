<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_passport_netease_im_token".
 *
 * @property string $user_id
 * @property string $accid
 * @property string $token
 */
class NeteaseImToken extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_passport_netease_im_token';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'string', 'max' => 18],
            [['accid'], 'string', 'max' => 32],
            [['token'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'accid' => 'Accid',
            'token' => 'Token',
        ];
    }
}
