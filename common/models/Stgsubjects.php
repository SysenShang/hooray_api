<?php

namespace common\models;

use yii;

/**
 * This is the model class for table "edu_common_stages_subjects".
 *
 * @property integer $sta_sub_id
 * @property integer $stages_id
 * @property string $stages_name
 * @property integer $grades_id
 * @property string $grades_name
 * @property integer $subjects_id
 * @property string $subjects_name
 * @property integer $xtype
 * @property integer $sort
 * @property integer $question_type_id
 * @property string $question_type_name
 * @property integer $qt_sort
 * @property integer $enable
 */
class Stgsubjects extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_common_stages_subjects';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stages_id', 'grades_id', 'subjects_id', 'xtype', 'sort', 'question_type_id', 'qt_sort', 'enable'], 'integer'],
            [['stages_name', 'grades_name', 'subjects_name', 'question_type_name'], 'string', 'max' => 45],
//            [['stages_name', 'grades_name','subjects_name'], 'filter', 'filter' => 'trim', 'skipOnArray' => true],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sta_sub_id' => 'Sta Sub ID',
            'stages_id' => 'Stages ID',
            'stages_name' => 'Stages Name',
            'grades_id' => 'Grades ID',
            'grades_name' => 'Grades Name',
            'subjects_id' => 'Subjects ID',
            'subjects_name' => 'Subjects Name',
            'xtype' => 'Xtype',
            'sort' => 'Sort',
            'question_type_id' => 'Question Type ID',
            'question_type_name' => 'Question Type Name',
            'qt_sort' => 'Qt Sort',
            'enable' => 'Enable',
        ];
    }
}
