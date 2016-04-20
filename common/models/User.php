<?php

namespace common\models;

use common\components\RedisStorage;
use OAuth2\Storage\UserCredentialsInterface;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "edu_passport_users".
 *
 * @property string $user_id
 * @property string $invite_code
 * @property string $user_number
 * @property string $telephone
 * @property string $username
 * @property string $email
 * @property string $upassword
 * @property integer $pwsafety
 * @property string $regdate
 * @property integer $avatarstatus
 * @property integer $group_id
 * @property integer $area_id
 * @property integer $school_id
 * @property integer $grade
 * @property integer $attr_id
 * @property integer $xstatus
 * @property string $FormUserId
 * @property string $GradeName
 * @property integer $user_source
 * @property integer $xtype
 * @property integer $status
 * @property integer $type
 * @property string $auth_key
 * @property string $password_reset_token
 */
class User extends ActiveRecord implements IdentityInterface, UserCredentialsInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    public $auth_key;
    public $password_reset_token;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_passport_users';
    }

    /**
     * 数据过滤
     */
    public function fields()
    {
        $fields = parent::fields();

        // remove fields that contain sensitive information
        unset($fields['upassword'], $fields['FormUserId'], $fields['GradeName']);

        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['user_id'], 'required'],
            [['pwsafety', 'avatarstatus', 'group_id', 'area_id', 'school_id', 'grade', 'attr_id', 'xstatus', 'user_source', 'xtype', 'status', 'type'], 'integer'],
            [['regdate'], 'safe'],
            [['user_id', 'invite_code'], 'string', 'max' => 18],
            [['user_number'], 'string', 'max' => 15],
            [['telephone'], 'string', 'max' => 60],
            [['username', 'FormUserId'], 'string', 'max' => 40],
            [['email'], 'string', 'max' => 30],
            [['upassword'], 'string', 'max' => 64],
            [['GradeName'], 'string', 'max' => 80],
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
            'invite_code' => 'Invite Code',
            'user_number' => 'User Number',
            'telephone' => 'Telephone',
            'username' => 'Username',
            'email' => 'Email',
            'upassword' => 'Upassword',
            'pwsafety' => 'Pwsafety',
            'regdate' => 'Regdate',
            'avatarstatus' => 'Avatarstatus',
            'group_id' => 'Group ID',
            'area_id' => 'Area ID',
            'school_id' => 'School ID',
            'grade' => 'Grade',
            'attr_id' => 'Attr ID',
            'xstatus' => 'Xstatus',
            'FormUserId' => 'Form User ID',
            'GradeName' => 'Grade Name',
            'user_source' => 'User Source',
            'xtype' => 'Xtype',
            'status' => 'Status',
            'type' => 'Type',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $accessToken = AccessTokens::findOne(['access_token' => $token]);
        if ($accessToken['access_token'] === $token) {
            $user = static::findOne(['user_id' => $accessToken['user_id']]);
            return $user;
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds user by telephone
     *
     * @param string $telephone
     * @return static|null
     */
    public static function findByTelephone($telephone)
    {
        return static::findOne(['telephone' => $telephone]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->upassword);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->upassword = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUser($username)
    {
        return static::findOne(['username' => $username]);
    }

    public function checkUserCredentials($username, $password)
    {
        $user = static::findByUser($username);
        if (empty($user)) {
            return false;
        }
        return $user->validatePassword($password);
    }

    public function getUserDetails($username)
    {
        $user = static::findByUser($username);
        return ['user_id' => $user->getId()];
    }

    public function getTchinfo()
    {

        return $this->hasOne(TeacherInfo::className(), ['user_id' => 'user_id']);

    }

    public static function getUserinfo($user_id)
    {
        $user = static::findOne(['user_id'=>$user_id]);
        if($user->group_id == 2){
            $userinfo = TeacherInfo::find()->select(['avatar', 'realname'])->where(['user_id'=>$user_id])->asArray()->one();
        }else{
            $userinfo = StudentInfo::find()->select(['avatar', 'realname'])->where(['user_id'=>$user_id])->asArray()->one();
        }
        $userinfo['username'] = $user->username;
        return $userinfo;
    }

    /**
     * @param $user_id
     * @return array|null|ActiveRecord
     */
    public static function avatar($user_id)
    {

        $user = static::findOne(['user_id'=>$user_id]);
        if($user){
            if($user->group_id == 2){
                $userinfo = RedisStorage::userinfo($user_id);
            }else{
                $userinfo = RedisStorage::userinfo($user_id,1);
            }
            $avatar['avatar'] =isset($userinfo->avatar)?$userinfo->avatar:"";
        }else{
            $avatar['avatar'] ="";
        }


        return $avatar;
    }

    /**
     * the status of the user on different device
     * @param $userId
     * @param $status
     * @param $userAgent
     **/
    public static function updateUserStatus($userId, $status)
    {
        static::updateAll(['status' => $status], ['user_id' => $userId]);
    }
}
