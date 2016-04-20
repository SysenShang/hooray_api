<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_direct_teach_reserve".
 *
 * @property integer $id
 * @property integer $schedule_id
 * @property integer $stage_gid
 * @property integer $subject_gid
 * @property string $stage_name
 * @property string $subject_name
 * @property string $student_id
 * @property string $username
 * @property string $created_at
 * @property integer $status
 * @property string $note
 * @property integer $score
 * @property string $comment
 * @property string $judge_at
 */
class TeachReserve extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_direct_teach_reserve';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['schedule_id'], 'required'],
            [['schedule_id', 'stage_gid', 'subject_gid', 'status', 'score'], 'integer'],
            [['created_at', 'judge_at'], 'safe'],
            [['stage_name', 'subject_name'], 'string', 'max' => 45],
            [['student_id'], 'string', 'max' => 18],
            [['username'], 'string', 'max' => 40],
            [['note', 'comment'], 'string', 'max' => 200],
            [['schedule_id', 'student_id'], 'unique', 'targetAttribute' => ['schedule_id', 'student_id'], 'message' => 'The combination of Schedule ID and Student ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schedule_id' => 'Schedule ID',
            'stage_gid' => '阶段',
            'subject_gid' => '课程',
            'stage_name' => '阶段名称',
            'subject_name' => '课程名称',
            'student_id' => 'Student ID',
            'username' => 'Username',
            'created_at' => 'Created At',
            'status' => '0:待上课 1:已上课',
            'note' => 'Note',
            'score' => '评分',
            'comment' => '评语',
            'judge_at' => '评判时间',
        ];
    }
}
