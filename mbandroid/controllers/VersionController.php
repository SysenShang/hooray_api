<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 8/17/15
 * Time: 4:13 PM
 */

namespace mbandroid\controllers;

use common\models\MobileVersionPublish;
use common\models\PcVersion;
use common\models\StudentVersionPublish;
use yii;

class VersionController extends yii\rest\ActiveController
{
    public $modelClass = 'common\models\PcVersion';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $param = Yii::$app->request->get();
        $os = isset($param['os']) ? $param['os'] : '';
        if ($os == 'android_mobile') {
            $version = MobileVersionPublish::find()->asArray()->one();
            if ($version) {
                //由于老的程序有MD5不能升级，暂时不要这个值
                //unset($version['filemd5']);
                return $version;
            } else {
                return ['status' => '10003', 'msg' => Yii::$app->params['v_10003']];
            }
        } elseif ($os == 'pc') {
            $pc = PcVersion::find()->asArray()->one();
            if (empty($pc)) {
                return ['status' => '10001', 'msg' => Yii::$app->params['v_10001']];
            } else {
                return $pc;
            }
        } elseif ($os == 'android_pad') {
            $pad = StudentVersionPublish::find()->asArray()->one();
            if (empty($pad)) {
                return ['status' => '10001', 'msg' => Yii::$app->params['v_10001']];
            } else {
                return $pad;
            }

        } else {
            return ['status' => '10002', 'msg' => Yii::$app->params['v_10002']];
        }
    }
}
