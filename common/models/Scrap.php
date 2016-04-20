<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_ask_scrap".
 *
 * @property integer $scrap_id
 * @property integer $order_id
 * @property integer $first
 * @property string $add_time
 * @property integer $question_id
 * @property string $answer_uid
 */
class Scrap extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_ask_scrap';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'first', 'question_id'], 'integer'],
            [['add_time'], 'safe'],
            [['answer_uid'], 'string', 'max' => 18]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'scrap_id' => 'Scrap ID',
            'order_id' => 'Order ID',
            'first' => 'First',
            'add_time' => 'Add Time',
            'question_id' => 'Question ID',
            'answer_uid' => 'Answer Uid',
        ];
    }
}
