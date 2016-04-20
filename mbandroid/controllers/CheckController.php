<?php

namespace mbandroid\controllers;

use common\models\VerifyStatus;
use yii;

class CheckController extends yii\rest\ActiveController
{
    public function init()
    {
        $this->modelClass = '';
        parent::init();
    }

    public function actions()
    {
        return [];
    }

    public function actionIndex()
    {
        $userAgent = Yii::$app->request->userAgent;
        $device    = '';
        $version   = '';
        switch (true) {
            case false !== strpos($userAgent, 'os/iPhone OS'):
                $device = false !== strpos($userAgent, 'iPad') ? 'iPad' : 'iPhone';
                if (preg_match('/^Hooray\/([\d.]+)/', $userAgent, $match)) {
                    $version = $match[1];
                }
                break;
            case preg_match('/com.[hi]*hooray/', $userAgent):
                $device = false !== strpos($userAgent, '.mobile') ? 'Android' : 'Android Pad';
                if (preg_match('/V([\d.]+)$/', $userAgent, $match)) {
                    $version = $match[1];
                }
                break;
        }
        $type = '';
        switch (true) {
            case false !== strpos($userAgent, 'student'):
                $type = 'student';
                break;
            case false !== strpos($userAgent, 'teacher'):
                $type = 'teacher';
                break;
        }
        if ('' !== $device) {
            $status = VerifyStatus::find()->select('verify')->where([
                'device' => $device,
                'version' => $version,
                'type' => $type
            ])->one();
            if (null === $status) {
                $status = new VerifyStatus();

                $status->device  = $device;
                $status->version = $version;
                $status->type = $type;
                $status->verify  = 0;
                $status->save();
            }
            return $status->verify;
        }
        return '0';
    }
}
