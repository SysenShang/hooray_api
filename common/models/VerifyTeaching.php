<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_teacher_givecourses_verify_teaching".
 *
 * @property string $user_id
 * @property integer $stages_id
 * @property string $stages_name
 * @property integer $subjects_id
 * @property string $subjects_name
 * @property string $createtime
 * @property integer $is_recommend
 * @property string $user_name
 * @property integer $flag
 * @property string $verify_time
 * @property integer $rathing
 * @property integer $is_rathing
 */
class VerifyTeaching extends \yii\db\ActiveRecord
{
    public static  function primaryKey()
    {
        return array('user_id');//自定义主键
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_teacher_givecourses_verify_teaching';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['stages_id', 'subjects_id', 'is_recommend', 'flag', 'rathing', 'is_rathing'], 'integer'],
            [['createtime', 'verify_time'], 'safe'],
            [['user_id'], 'string', 'max' => 18],
            [['stages_name', 'subjects_name'], 'string', 'max' => 45],
            [['user_name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'stages_id' => 'Stages ID',
            'stages_name' => 'Stages Name',
            'subjects_id' => 'Subjects ID',
            'subjects_name' => 'Subjects Name',
            'createtime' => 'Createtime',
            'is_recommend' => 'Is Recommend',
            'user_name' => 'User Name',
            'flag' => 'Flag',
            'verify_time' => 'Verify Time',
            'rathing' => 'Rathing',
            'is_rathing' => 'Is Rathing',
        ];
    }

}
