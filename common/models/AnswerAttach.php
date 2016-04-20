<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_ask_teacher_attach".
 *
 * @property integer $id
 * @property string $file_name
 * @property string $add_time
 * @property string $file_location
 * @property integer $question_id
 * @property string $order_id
 * @property string $published_uid
 * @property string $thumbnail
 * @property string $file_size
 * @property integer $first
 * @property integer $file_type
 */
class AnswerAttach extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_ask_teacher_attach';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['add_time'], 'safe'],
            [['question_id', 'first', 'file_type'], 'integer'],
            [['file_name', 'file_location', 'thumbnail'], 'string', 'max' => 255],
            [['order_id'], 'string', 'max' => 60],
            [['published_uid'], 'string', 'max' => 18],
            [['file_size'], 'string', 'max' => 40]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file_name' => 'File Name',
            'add_time' => 'Add Time',
            'file_location' => 'File Location',
            'question_id' => 'Question ID',
            'order_id' => 'Order ID',
            'published_uid' => 'Published Uid',
            'thumbnail' => 'Thumbnail',
            'file_size' => 'File Size',
            'first' => 'First',
            'file_type' => 'File Type',
        ];
    }
}
