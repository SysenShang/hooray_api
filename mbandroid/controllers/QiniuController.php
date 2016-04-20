<?php

namespace mbandroid\controllers;

use yii\rest\ActiveController;
use yii;
use Qiniu\Auth;

class QiniuController extends ActiveController
{
    public $modelClass = 'common\models\common';

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);

        return $actions;
    }

    public function actionToken()
    {
        ob_end_clean();
        $getData = Yii::$app->request->get();

        $accessKey = Yii::$app->params['qiniu']['access_key'];
        $secretKey = Yii::$app->params['qiniu']['secret_key'];
        $auth      = new Auth($accessKey, $secretKey);
        $bucket    = empty($getData['bucket']) ? Yii::$app->params['hooray-system']['bucket'] : $getData['bucket'];
        $token     = $auth->uploadToken($bucket);

        $_array['uptoken'] = $token;

        $str                = 'abcdefghijkmnpqrstuvwxyz23456789';
        $_array['filename'] = $bucket . '_' . substr(str_shuffle($str), 0, 10) . "_" . time();
        $_array['url']      = Yii::$app->params[$bucket]['url'];

        return $_array;
    }

    /**
     * 持久化结果通知接口
     * @throws yii\db\Exception
     * @author grg
     */
    public function actionNotify()
    {
        return '';
        fastcgi_finish_request();
        $postData = Yii::$app->request->post();
        if (!empty($postData) && $postData['code'] === 0) {
            $key    = $postData['inputKey'];
            $bucket = $postData['inputBucket'];
            $data = [];
            $now = date('Y-m-d H:i:s');
            if (is_array($postData['items'])) {
                foreach ($postData['items'] as $item) {
                    if ($item['code'] === 0) {
                        $data[] = [
                           $key,
                           $bucket,
                           $item['hash'],
                           $item['key'],
                           $now
                        ];
                    }
                }
                if (!empty($data)) {
                    Yii::$app->db->createCommand()->batchInsert('edu_qiniu_pfop', ['input_key', 'bucket', 'hash', 'file_key', 'created_at'], $data)->execute();
                    return true;
                }
            }
        }
        return false;
    }

}
