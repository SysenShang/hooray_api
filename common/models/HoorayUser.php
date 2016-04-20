<?php
namespace common\models;

use yii;

class HoorayUser extends yii\redis\ActiveRecord
{
    public static  function primaryKey()
    {
        return array('user_id');//自定义主键
    }

    public static function tableName()
    {
        return 'hooray_user';
    }

    public function attributes()
    {
        return ['user_id', 'user_number', 'telephone', 'username','email','upassword','pwsafety','regdate','avatarstatus','group_id','attr_id','xstatus','FormUserId','GradeName','user_source','xtype','status','type'];
    }
}
