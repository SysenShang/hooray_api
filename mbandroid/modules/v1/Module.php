<?php

namespace mbandroid\modules\v1;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'mbandroid\modules\v1\controllers';


    public function init()
    {
        parent::init();
//        \Yii::$app->user->enableSession = false;


        // custom initialization code goes here
    }
}