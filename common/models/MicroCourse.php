<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_micro_course".
 *
 * @property integer $id
 * @property string $name
 * @property string $user_id
 * @property string $realname
 * @property integer $video_id
 * @property integer $stage_id
 * @property string $stagename
 * @property integer $grade_id
 * @property string $gradename
 * @property integer $course_id
 * @property string $coursename
 * @property integer $price
 * @property string $description
 * @property string $create_time
 * @property string $update_time
 * @property string $video_url
 * @property string $video_small_image
 * @property string $video_middle_image
 * @property string $video_big_image
 * @property string $video_duration
 * @property string $content
 * @property integer $free
 * @property integer $publish
 * @property integer $xstatus
 * @property integer $isauth
 * @property string $persistentId
 * @property integer $isfop
 * @property string $authtime
 * @property integer $school_id
 * @property integer $access
 * @property integer $viewnums
 * @property integer $favnums
 * @property integer $buynums
 */
class MicroCourse extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_micro_course';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['video_id', 'stage_id', 'grade_id', 'course_id', 'price', 'free', 'publish', 'xstatus', 'isauth', 'isfop', 'school_id', 'access', 'viewnums', 'favnums', 'buynums'], 'integer'],
            [['create_time', 'update_time', 'authtime'], 'safe'],
            [['content'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['user_id'], 'string', 'max' => 18],
            [['realname', 'stagename', 'gradename', 'coursename', 'video_duration'], 'string', 'max' => 20],
            [['description', 'video_url', 'video_small_image', 'video_middle_image', 'video_big_image'], 'string', 'max' => 255],
            [['persistentId'], 'string', 'max' => 120]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'user_id' => 'User ID',
            'realname' => 'Realname',
            'video_id' => 'Video ID',
            'stage_id' => 'Stage ID',
            'stagename' => 'Stagename',
            'grade_id' => 'Grade ID',
            'gradename' => 'Gradename',
            'course_id' => 'Course ID',
            'coursename' => 'Coursename',
            'price' => 'Price',
            'description' => 'Description',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'video_url' => 'Video Url',
            'video_small_image' => 'Video Small Image',
            'video_middle_image' => 'Video Middle Image',
            'video_big_image' => 'Video Big Image',
            'video_duration' => 'Video Duration',
            'content' => 'Content',
            'free' => 'Free',
            'publish' => 'Publish',
            'xstatus' => 'Xstatus',
            'isauth' => 'Isauth',
            'persistentId' => 'Persistent ID',
            'isfop' => 'Isfop',
            'authtime' => 'Authtime',
            'school_id' => 'School ID',
            'access' => 'Access',
            'viewnums' => 'Viewnums',
            'favnums' => 'Favnums',
            'buynums' => 'Buynums',
        ];
    }
}