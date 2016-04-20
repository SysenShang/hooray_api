<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_ask_order".
 *
 * @property integer $order_id
 * @property string $order_sn
 * @property integer $question_id
 * @property string $answer_uid
 * @property string $answer_nickname
 * @property string $answer_username
 * @property string $order_time
 * @property integer $confrim
 * @property string $answer_add_time
 * @property integer $replies
 * @property integer $order_status
 * @property integer $t_is_comment
 * @property integer $s_is_comment
 * @property string $acquire_time
 * @property integer $t_status
 * @property integer $dissent_id
 * @property integer $solve_id
 * @property integer $refund_status
 * @property integer $sh_id
 * @property integer $first
 * @property integer $is_video
 * @property integer $is_append
 * @property integer $appoint_type_id
 * @property integer $s_is_del
 * @property integer $t_is_del
 */
class AskOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_ask_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'confrim', 'replies', 'order_status', 't_is_comment', 's_is_comment', 't_status', 'dissent_id', 'solve_id', 'refund_status', 'sh_id', 'first', 'is_video', 'is_append', 'appoint_type_id', 's_is_del', 't_is_del'], 'integer'],
            [['order_time', 'answer_add_time', 'acquire_time'], 'safe'],
            [['order_sn'], 'string', 'max' => 60],
            [['answer_uid'], 'string', 'max' => 18],
            [['answer_nickname', 'answer_username'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'order_sn' => 'Order Sn',
            'question_id' => 'Question ID',
            'answer_uid' => 'Answer Uid',
            'answer_nickname' => 'Answer Nickname',
            'answer_username' => 'Answer Username',
            'order_time' => 'Order Time',
            'confrim' => 'Confrim',
            'answer_add_time' => 'Answer Add Time',
            'replies' => 'Replies',
            'order_status' => 'Order Status',
            't_is_comment' => 'T Is Comment',
            's_is_comment' => 'S Is Comment',
            'acquire_time' => 'Acquire Time',
            't_status' => 'T Status',
            'dissent_id' => 'Dissent ID',
            'solve_id' => 'Solve ID',
            'refund_status' => 'Refund Status',
            'sh_id' => 'Sh ID',
            'first' => 'First',
            'is_video' => 'Is Video',
            'is_append' => 'Is Append',
            'appoint_type_id' => 'Appoint Type ID',
            's_is_del' => 'S Is Del',
            't_is_del' => 'T Is Del',
        ];
    }

    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['question_id' => 'question_id']);
    }

    /*
     * 确认问题
     */
    public function getFrimquestion($order_id)
    {
        return $this->hasOne(Question::tableName(),['question_id' => 'question_id'])->where(['order_id'=>$order_id,'first'=>0]);
    }

}
