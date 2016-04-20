<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_passport_student_info".
 *
 * @property string $user_id
 * @property string $realname
 * @property string $nickname
 * @property string $gender
 * @property integer $birthmonth
 * @property integer $birthyear
 * @property integer $birthday
 * @property string $resideprovince
 * @property string $residecity
 * @property string $residedist
 * @property string $residecommunity
 * @property string $residesuite
 * @property string $telephone
 * @property string $education
 * @property string $profile
 * @property string $avatar
 * @property string $GradeName
 * @property string $idcard
 */
class StudentInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_passport_student_info';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['gender', 'profile'], 'string'],
            [['birthmonth', 'birthyear', 'birthday'], 'integer'],
            [['user_id'], 'string', 'max' => 18],
            [['realname', 'nickname'], 'string', 'max' => 30],
            [['resideprovince', 'residecity', 'residedist', 'residecommunity', 'residesuite', 'education', 'avatar'], 'string', 'max' => 255],
            [['telephone'], 'string', 'max' => 20],
            [['GradeName'], 'string', 'max' => 200],
            [['idcard'], 'string', 'max' => 45],
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
            'realname' => 'Realname',
            'nickname' => 'Nickname',
            'gender' => 'Gender',
            'birthmonth' => 'Birthmonth',
            'birthyear' => 'Birthyear',
            'birthday' => 'Birthday',
            'resideprovince' => 'Resideprovince',
            'residecity' => 'Residecity',
            'residedist' => 'Residedist',
            'residecommunity' => 'Residecommunity',
            'residesuite' => 'Residesuite',
            'telephone' => 'Telephone',
            'education' => 'Education',
            'profile' => 'Profile',
            'avatar' => 'Avatar',
            'GradeName' => 'Grade Name',
            'idcard' => 'Idcard',
        ];
    }
}
