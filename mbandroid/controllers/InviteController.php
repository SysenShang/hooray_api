<?php

namespace mbandroid\controllers;

use dosamigos\qrcode\QrCode;
use yii;
use yii\web\Controller;

class InviteController extends Controller
{
    public function actionIndex()
    {
        Yii::$app->response->format = yii\web\Response::FORMAT_RAW;
        echo $this->renderPartial('index', Yii::$app->request->get());
    }

    public function actionQrcode()
    {
        return QrCode::png(Yii::$app->request->get('code'));
    }
}
