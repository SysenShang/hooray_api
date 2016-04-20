<?php
namespace common\models;

use yii;

class HoorayLog extends yii\redis\ActiveRecord
{
    public static  function primaryKey()
    {
        return array('user_id');//自定义主键
    }

    public function attributes()
    {
        return [
            'user_id',
            'regip',
            'logouttime',
            'logintime',
            'version'
        ];
    }

}