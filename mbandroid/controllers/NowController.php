<?php

namespace mbandroid\controllers;

use yii;
use yii\rest\Controller;

class NowController extends Controller
{

    public function actionIndex()
    {
        return [
            'time' => date('Y-m-d H:i:s'),
            'timestamp' => date('U'),
            'timezone' => date('T'),
            'timeoffset' => date('Z'),
            'microtime' => (string)microtime(true)
        ];
    }
}
