<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_micro_course_order".
 *
 * @property integer $id
 * @property string $order_id
 * @property string $user_id
 * @property string $username
 * @property integer $mc_id
 * @property string $mc_name
 * @property string $t_user_id
 * @property integer $stage_id
 * @property integer $grade_id
 * @property string $grade_name
 * @property integer $course_id
 * @property string $course_name
 * @property integer $price
 * @property string $video_small_image
 * @property string $video_middle_image
 * @property string $video_big_image
 * @property integer $view_nums
 * @property integer $valid_time
 * @property integer $isshow
 * @property integer $isdel
 * @property string $createtime
 * @property string $updatetime
 */
class MicroCourseOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_micro_course_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['mc_id', 'stage_id', 'grade_id', 'course_id', 'price', 'view_nums', 'valid_time', 'isshow', 'isdel'], 'integer'],
            [['createtime', 'updatetime'], 'safe'],
            [['order_id'], 'string', 'max' => 60],
            [['user_id', 't_user_id'], 'string', 'max' => 18],
            [['username'], 'string', 'max' => 15],
            [['mc_name'], 'string', 'max' => 100],
            [['grade_name', 'course_name'], 'string', 'max' => 20],
            [['video_small_image', 'video_middle_image', 'video_big_image'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'user_id' => 'User ID',
            'username' => 'Username',
            'mc_id' => 'Mc ID',
            'mc_name' => 'Mc Name',
            't_user_id' => 'T User ID',
            'stage_id' => 'Stage ID',
            'grade_id' => 'Grade ID',
            'grade_name' => 'Grade Name',
            'course_id' => 'Course ID',
            'course_name' => 'Course Name',
            'price' => 'Price',
            'video_small_image' => 'Video Small Image',
            'video_middle_image' => 'Video Middle Image',
            'video_big_image' => 'Video Big Image',
            'view_nums' => 'View Nums',
            'valid_time' => 'Valid Time',
            'isshow' => 'Isshow',
            'isdel' => 'Isdel',
            'createtime' => 'Createtime',
            'updatetime' => 'Updatetime',
        ];
    }
}
