<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_common_subjects".
 *
 * @property integer $id
 * @property string $subject_name
 */
class Subjects extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_common_subjects';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject_name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subject_name' => 'Subject Name',
        ];
    }
}
