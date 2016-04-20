<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_passport_student_count".
 *
 * @property string $user_id
 * @property integer $coin
 * @property integer $lock_coin
 * @property integer $credits
 * @property integer $question_num
 * @property integer $comment_num
 * @property integer $coureses_num
 * @property integer $online_coureses_num
 * @property integer $follower
 * @property integer $following
 * @property integer $rating
 * @property integer $favorites
 */
class PassportStudentCount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_passport_student_count';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['coin', 'lock_coin', 'credits', 'question_num', 'comment_num', 'coureses_num', 'online_coureses_num', 'follower', 'following', 'rating', 'favorites'], 'integer'],
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
            'coin' => '哇哇豆',
            'lock_coin' => '暂扣哇哇豆',
            'credits' => '积分',
            'question_num' => '问题数',
            'comment_num' => '评论数',
            'coureses_num' => '微课数',
            'online_coureses_num' => '云课数',
            'follower' => '粉丝数量',
            'following' => '关注数量',
            'rating' => '等级',
            'favorites' => '收藏夹',
        ];
    }
}
