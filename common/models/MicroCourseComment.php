<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_micro_course_comment".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $micro_id
 * @property string $user_id
 * @property string $title
 * @property string $content
 * @property string $update_time
 * @property string $create_time
 */
class MicroCourseComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_micro_course_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'micro_id'], 'integer'],
            [['update_time', 'create_time'], 'safe'],
            [['user_id'], 'string', 'max' => 18],
            [['title'], 'string', 'max' => 100],
            [['content'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'micro_id' => 'Micro ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'content' => 'Content',
            'update_time' => 'Update Time',
            'create_time' => 'Create Time',
        ];
    }
}