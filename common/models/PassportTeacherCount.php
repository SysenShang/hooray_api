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
class PassportTeacherCount extends \yii\db\ActiveRecord
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
            'user_id' => '用户id',
            'credits' => '积分',
            'coin' => '哇哇豆',
            'lock_coin' => '暂扣哇哇豆',
            'question_num' => '问题数',
            'coureses_num' => '微课数',
            'online_coureses_num' => '云课数',
            'follower' => '粉丝数量',
            'following' => '关注数量',
            'rating' => '等级',
            'favorites' => '收藏夹',
            'positive' => '好评',
            'moderate' => '中评',
            'negative' => '差评',
            'CoursePositive' => '好评',
            'CourseModerate' => '中评',
            'CourseNegative' => '差评',
            'AskPositive' => '好评',
            'AskModerate' => '中评',
            'AskNegative' => '差评',
            'comment_num' => '评论总次数',
            'comment_sum_rating' => '评论总分数',
        ];
    }
}
