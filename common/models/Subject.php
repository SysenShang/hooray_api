<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_common_subject".
 *
 * @property integer $id
 * @property integer $fid
 * @property string $subject_name
 * @property integer $level
 * @property integer $xtype
 */
class Subject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_common_subject';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fid', 'level', 'xtype'], 'integer'],
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
            'fid' => 'Fid',
            'subject_name' => 'Subject Name',
            'level' => 'Level',
            'xtype' => 'Xtype',
        ];
    }
}
