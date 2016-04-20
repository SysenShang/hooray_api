<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 8/28/15
 * Time: 5:15 PM
 */

namespace common\models;

use yii;

Class Common
{
//    public static function tableName()
//    {
//        return 'edu_passport_users';
//    }

    public function rules()
    {
        return [
            // rule applied when corresponding field is "safe"
//            ['username', 'string', 'length' => [1, 3]],
//            ['first_name', 'string', 'max' => 128],
//            ['password', 'required'],

            // rule applied when scenario is "signup" no matter if field is "safe" or not
            ['hashcode',  'on' => 'signup'],
        ];
    }

    public  function scenarios()
    {
        return [
            // on signup allow mass assignment of username
            'signup' => ['username', 'password'],
            'update' => ['username', 'first_name'],
        ];
    }

}