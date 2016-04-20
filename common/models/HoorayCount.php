<?php
namespace common\models;

use yii;

class HoorayCount extends yii\redis\ActiveRecord
{
    public static  function primaryKey()
    {
        return array('user_id');//自定义主键
    }

    public function attributes()
    {
        return [
            'user_id',
            'credits',
            'coin',
            'lock_coin',
            'question_num',
            'coureses_num',
            'online_coureses_num',
            'follower',
            'following',
            'rating',
            'favorites',
            'positive',
            'moderate',
            'negative',
            'CoursePositive',
            'CourseModerate',
            'CourseNegative',
            'AskPositive',
            'AskModerate',
            'AskNegative',
            'comment_num',
            'comment_sum_rating',
        ];
    }
}