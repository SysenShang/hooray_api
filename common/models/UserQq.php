<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_passport_users_qq".
 *
 * @property string $id
 * @property string $uid
 * @property string $nickname
 * @property string $openid
 * @property string $gender
 * @property string $add_time
 * @property string $access_token
 * @property string $refresh_token
 * @property integer $expires_time
 * @property string $figureurl
 * @property integer $group_id
 */
class UserQq extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_passport_users_qq';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'group_id'], 'required'],
            [['add_time'], 'safe'],
            [['expires_time', 'group_id'], 'integer'],
            [['uid'], 'string', 'max' => 18],
            [['nickname', 'access_token', 'refresh_token'], 'string', 'max' => 64],
            [['openid'], 'string', 'max' => 128],
            [['gender'], 'string', 'max' => 8],
            [['figureurl'], 'string', 'max' => 255],
            [['openid'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'nickname' => 'Nickname',
            'openid' => 'Openid',
            'gender' => 'Gender',
            'add_time' => 'Add Time',
            'access_token' => 'Access Token',
            'refresh_token' => 'Refresh Token',
            'expires_time' => 'Expires Time',
            'figureurl' => 'Figureurl',
            'group_id' => 'Group ID',
        ];
    }
}
