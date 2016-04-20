<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_common_comment".
 *
 * @property integer $comment_id
 * @property integer $comment_style
 * @property integer $target
 * @property string $student_id
 * @property string $student_name
 * @property string $teacher_id
 * @property string $teacher_name
 * @property string $title
 * @property string $content
 * @property string $rating
 * @property integer $comment_rating
 * @property string $describe_teacher
 * @property string $update_time
 * @property string $create_time
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_common_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment_style', 'target', 'comment_rating'], 'integer'],
            [['student_id', 'teacher_id'], 'required'],
            [['update_time', 'create_time'], 'safe'],
            [['student_id', 'teacher_id'], 'string', 'max' => 18],
            [['student_name', 'teacher_name'], 'string', 'max' => 40],
            [['title'], 'string', 'max' => 100],
            [['content'], 'string', 'max' => 255],
            [['rating'], 'string', 'max' => 45],
            [['describe_teacher'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'comment_id' => 'Comment ID',
            'comment_style' => 'Comment Style',
            'target' => 'Target',
            'student_id' => 'Student ID',
            'student_name' => 'Student Name',
            'teacher_id' => 'Teacher ID',
            'teacher_name' => 'Teacher Name',
            'title' => 'Title',
            'content' => 'Content',
            'rating' => 'Rating',
            'comment_rating' => 'Comment Rating',
            'describe_teacher' => 'Describe Teacher',
            'update_time' => 'Update Time',
            'create_time' => 'Create Time',
        ];
    }
}
