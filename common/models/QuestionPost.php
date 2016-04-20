<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_ask_question_post".
 *
 * @property integer $post_id
 * @property integer $qid
 * @property integer $first
 * @property string $question_title
 * @property string $question_detail
 * @property integer $reward
 * @property string $add_time
 * @property string $expired_time
 * @property string $update_time
 * @property string $published_uid
 * @property string $published_nickname
 * @property string $published_username
 * @property integer $has_attach
 * @property integer $status
 * @property integer $grade_id
 * @property integer $subject_id
 * @property string $grade_name
 * @property string $subject_name
 * @property integer $anonymous
 * @property integer $view_count
 * @property integer $is_recommend
 * @property string $attach_info
 * @property integer $expired_id
 * @property integer $question_type_id
 * @property string $question_type_name
 * @property string $question_answer
 * @property string $persistentId
 */
class QuestionPost extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_ask_question_post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['qid', 'first', 'reward', 'has_attach', 'status', 'grade_id', 'subject_id', 'anonymous', 'view_count', 'is_recommend', 'expired_id', 'question_type_id'], 'integer'],
            [['question_detail', 'attach_info', 'question_answer'], 'string'],
            [['add_time', 'expired_time', 'update_time'], 'safe'],
            [['question_title'], 'string', 'max' => 255],
            [['published_uid'], 'string', 'max' => 18],
            [['published_nickname', 'published_username', 'question_type_name'], 'string', 'max' => 50],
            [['grade_name', 'subject_name'], 'string', 'max' => 10],
            [['persistentId'], 'string', 'max' => 120]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'post_id' => 'Post ID',
            'qid' => 'Qid',
            'first' => 'First',
            'question_title' => 'Question Title',
            'question_detail' => 'Question Detail',
            'reward' => 'Reward',
            'add_time' => 'Add Time',
            'expired_time' => 'Expired Time',
            'update_time' => 'Update Time',
            'published_uid' => 'Published Uid',
            'published_nickname' => 'Published Nickname',
            'published_username' => 'Published Username',
            'has_attach' => 'Has Attach',
            'status' => 'Status',
            'grade_id' => 'Grade ID',
            'subject_id' => 'Subject ID',
            'grade_name' => 'Grade Name',
            'subject_name' => 'Subject Name',
            'anonymous' => 'Anonymous',
            'view_count' => 'View Count',
            'is_recommend' => 'Is Recommend',
            'attach_info' => 'Attach Info',
            'expired_id' => 'Expired ID',
            'question_type_id' => 'Question Type ID',
            'question_type_name' => 'Question Type Name',
            'question_answer' => 'Question Answer',
            'persistentId' => 'Persistent ID',
        ];
    }
}
