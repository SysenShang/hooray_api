<?php
namespace common\models;

use yii;

class HoorayUserinfo extends yii\redis\ActiveRecord
{
    public static function primaryKey()
    {
        return array('user_id');//自定义主键
    }

    public function attributes()
    {
        return [
            'user_id',
            'realname',
            'nickname',
            'gender',
            'birthmonth',
            'birthyear',
            'birthday',
            'resideprovince',
            'residecity',
            'residedist',
            'residecommunity',
            'residesuite',
            'telephone',
            'education',
            'profile',
            'avatar',
            'characteristics',
            'SchoolName',
            'GradeName',
            'idcard',
        ];
    }
}