<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_ask_attach".
 *
 * @property integer $id
 * @property string $file_name
 * @property string $add_time
 * @property string $file_location
 * @property integer $question_id
 * @property integer $type_id
 * @property string $published_uid
 * @property string $file_size
 * @property integer $file_type
 */
class AskAttach extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_ask_attach';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['add_time'], 'safe'],
            [['question_id', 'type_id', 'file_type'], 'integer'],
            [['file_name', 'file_location'], 'string', 'max' => 255],
            [['published_uid'], 'string', 'max' => 18],
            [['file_size'], 'string', 'max' => 30]
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
            'type_id' => 'Type ID',
            'published_uid' => 'Published Uid',
            'file_size' => 'File Size',
            'file_type' => 'File Type',
        ];
    }
}
