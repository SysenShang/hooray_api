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
class StuCount extends \yii\db\ActiveRecord
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
            'user_id' => 'User ID',
            'coin' => 'Coin',
            'lock_coin' => 'Lock Coin',
            'credits' => 'Credits',
            'question_num' => 'Question Num',
            'comment_num' => 'Comment Num',
            'coureses_num' => 'Coureses Num',
            'online_coureses_num' => 'Online Coureses Num',
            'follower' => 'Follower',
            'following' => 'Following',
            'rating' => 'Rating',
            'favorites' => 'Favorites',
        ];
    }
}
