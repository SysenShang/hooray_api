<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oauth_access_tokens".
 *
 * @property string $access_token
 * @property string $client_id
 * @property string $user_id
 * @property string $expires
 * @property string $scope
 *
 * @property OauthClients $client
 */
class AccessTokens extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oauth_access_tokens';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['access_token', 'client_id'], 'required'],
            [['expires'], 'safe'],
            [['access_token'], 'string', 'max' => 40],
            [['client_id'], 'string', 'max' => 32],
            [['user_id'], 'string', 'max' => 18],
            [['scope'], 'string', 'max' => 2000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'access_token' => 'Access Token',
            'client_id' => 'Client ID',
            'user_id' => 'User ID',
            'expires' => 'Expires',
            'scope' => 'Scope',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(OauthClients::className(), ['client_id' => 'client_id']);
    }

    public  function getUserinfo()
    {
        return $this->hasOne(UsersInfo::className(), ['user_id' => 'user_id']);

    }
}
