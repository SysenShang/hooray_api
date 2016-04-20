<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_direct_teach_schedule".
 *
 * @property integer $id
 * @property string $code
 * @property string $teacher_id
 * @property string $username
 * @property string $date
 * @property string $begintime
 * @property string $endtime
 * @property integer $price
 * @property integer $limit
 * @property integer $num
 * @property integer $classroom_id
 * @property string $classroom_record
 * @property integer $chatroom_id
 */
class TeachSchedule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_direct_teach_schedule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'begintime', 'endtime'], 'required'],
            [['date', 'begintime', 'endtime'], 'safe'],
            [['price', 'limit', 'num', 'classroom_id', 'chatroom_id'], 'integer'],
            [['code'], 'string', 'max' => 15],
            [['teacher_id'], 'string', 'max' => 18],
            [['username'], 'string', 'max' => 40],
            [['classroom_record'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'teacher_id' => 'Teacher ID',
            'username' => 'Username',
            'date' => '日期',
            'begintime' => '开始时间',
            'endtime' => '结束时间',
            'price' => '价格(豆)',
            'limit' => '限制人数',
            'num' => '已约人数',
            'classroom_id' => 'Classroom ID',
            'classroom_record' => 'Classroom Record',
            'chatroom_id' => 'Chatroom ID',
        ];
    }
}
