<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_passport_users_weixin".
 *
 * @property integer $id
 * @property string $uid
 * @property string $openid
 * @property integer $expires_in
 * @property string $access_token
 * @property string $refresh_token
 * @property string $scope
 * @property string $headimgurl
 * @property string $nickname
 * @property string $sex
 * @property string $province
 * @property string $city
 * @property string $country
 * @property integer $add_time
 * @property double $latitude
 * @property double $longitude
 * @property integer $location_update
 * @property integer $group_id
 */
class UserWeixin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_passport_users_weixin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'openid', 'add_time'], 'required'],
            [['expires_in', 'add_time', 'location_update', 'group_id'], 'integer'],
            [['sex'], 'string'],
            [['latitude', 'longitude'], 'number'],
            [['uid'], 'string', 'max' => 18],
            [['openid', 'access_token', 'refresh_token', 'headimgurl'], 'string', 'max' => 255],
            [['scope', 'nickname'], 'string', 'max' => 64],
            [['province', 'city', 'country'], 'string', 'max' => 32],
            [['uid'], 'unique']
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
            'openid' => 'Openid',
            'expires_in' => 'Expires In',
            'access_token' => 'Access Token',
            'refresh_token' => 'Refresh Token',
            'scope' => 'Scope',
            'headimgurl' => 'Headimgurl',
            'nickname' => 'Nickname',
            'sex' => 'Sex',
            'province' => 'Province',
            'city' => 'City',
            'country' => 'Country',
            'add_time' => 'Add Time',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'location_update' => 'Location Update',
            'group_id' => 'Group ID',
        ];
    }
}
