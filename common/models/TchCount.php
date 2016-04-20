<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_passport_teacher_count".
 *
 * @property string $user_id
 * @property integer $credits
 * @property integer $coin
 * @property integer $lock_coin
 * @property integer $question_num
 * @property integer $coureses_num
 * @property integer $online_coureses_num
 * @property integer $follower
 * @property integer $following
 * @property integer $rating
 * @property integer $favorites
 * @property integer $positive
 * @property integer $moderate
 * @property integer $negative
 * @property integer $CoursePositive
 * @property integer $CourseModerate
 * @property integer $CourseNegative
 * @property integer $AskPositive
 * @property integer $AskModerate
 * @property integer $AskNegative
 * @property integer $comment_num
 * @property integer $comment_sum_rating
 */
class TchCount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_passport_teacher_count';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['credits', 'coin', 'lock_coin', 'question_num', 'coureses_num', 'online_coureses_num', 'follower', 'following', 'rating', 'favorites', 'positive', 'moderate', 'negative', 'CoursePositive', 'CourseModerate', 'CourseNegative', 'AskPositive', 'AskModerate', 'AskNegative', 'comment_num', 'comment_sum_rating'], 'integer'],
            [['user_id'], 'string', 'max' => 18],
            [['user_id'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'credits' => 'Credits',
            'coin' => 'Coin',
            'lock_coin' => 'Lock Coin',
            'question_num' => 'Question Num',
            'coureses_num' => 'Coureses Num',
            'online_coureses_num' => 'Online Coureses Num',
            'follower' => 'Follower',
            'following' => 'Following',
            'rating' => 'Rating',
            'favorites' => 'Favorites',
            'positive' => 'Positive',
            'moderate' => 'Moderate',
            'negative' => 'Negative',
            'CoursePositive' => 'Course Positive',
            'CourseModerate' => 'Course Moderate',
            'CourseNegative' => 'Course Negative',
            'AskPositive' => 'Ask Positive',
            'AskModerate' => 'Ask Moderate',
            'AskNegative' => 'Ask Negative',
            'comment_num' => 'Comment Num',
            'comment_sum_rating' => 'Comment Sum Rating',
        ];
    }

    public static function getRatingByCredits($credits) {
        $level = Yii::$app->params['teacherLevelCredits'];
        if ($credits < $level[2]) {
            $rating = 1;
        } elseif ($credits >= $level[2] && $credits < $level[3]) {
            $rating = 2;
        } elseif ($credits >= $level[3] && $credits < $level[4]) {
            $rating = 3;
        } elseif ($credits >= $level[4] && $credits < $level[5]) {
            $rating = 4;
        } elseif ($credits >= $level[5] && $credits < $level[6]) {
            $rating = 5;
        } elseif ($credits >= $level[6] && $credits < $level[7]) {
            $rating = 6;
        } elseif ($credits >= $level[7] && $credits < $level[8]) {
            $rating = 7;
        } elseif ($credits >= $level[8] && $credits < $level[9]) {
            $rating = 8;
        } elseif ($credits >= $level[9] && $credits < $level[10]) {
            $rating = 9;
        } else {
            $rating = 10;
        }
        return $rating;
    }
}
