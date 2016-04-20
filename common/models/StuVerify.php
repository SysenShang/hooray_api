<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_student_verify".
 *
 * @property string $user_id
 * @property integer $verify1
 * @property integer $verify2
 * @property integer $verify3
 * @property integer $verify4
 * @property integer $verify5
 * @property integer $verify6
 * @property integer $verify7
 * @property integer $verify8
 * @property integer $verify9
 * @property integer $verify10
 * @property integer $is_check_in
 */
class StuVerify extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_student_verify';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['verify1', 'verify2', 'verify3', 'verify4', 'verify5', 'verify6', 'verify7', 'verify8', 'verify9', 'verify10', 'is_check_in'], 'integer'],
            [['user_id'], 'string', 'max' => 18]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户ID',
            'verify1' => '手机认证:  -1:被拒绝;0:未认证 1:待审核 2:审核通过',
            'verify2' => '邮箱认证: -1:被拒绝;0:未认证 1:待审核 2:审核通过',
            'verify3' => '考试苑: -1:被拒绝;0:未认证 1:待审核 2:审核通过',
            'verify4' => '开课认证: -1:被拒绝;0:未认证 1:待审核 2:审核通过',
            'verify5' => '身份认证: -1:被拒绝;0:未认证 1:待审核 2:审核通过',
            'verify6' => '资质认证: -1:被拒绝;0:未认证 1:待审核 2:审核通过',
            'verify7' => 'verify7: -1:被拒绝;0:未认证 1:待审核 2:审核通过',
            'verify8' => 'verify8: -1:被拒绝;0:未认证 1:待审核 2:审核通过',
            'verify9' => 'verify9:  -1:被拒绝;0:未认证 1:待审核 2:审核通过',
            'verify10' => 'verify10:  -1:被拒绝;0:未认证 1:待审核 2:审核通过',
            'is_check_in' => '签到5天',
        ];
    }
}