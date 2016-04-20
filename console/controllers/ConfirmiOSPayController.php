<?php

namespace console\Controllers;

use yii;
use yii\console\Controller;
use common\models\RechargeOrder;
use common\models\RechargeValidateinfo;

/**
 * Class ConfirmiOSPayController
 * @useage ./console/yii ConfirmiOSPay --appconfig=console/config/main.php
 * @package console\Controllers
 */
class ConfirmiOSPayController extends Controller
{
    public function actionIndex()
    {
        $i = 10;//保底循环10次，正式验证并不占用这10次
        while ($i--) {
            $allData = RechargeValidateinfo::find()->where(['delflg' => 0])->limit(10)->asArray()->all();
            if (!empty($allData)) {
                foreach ($allData as $data) {
                    $i++;
                    $validateInfo = $this->ValidateAppleServer($data['code'], Yii::$app->params['ios_validation_buy']);
                    if (empty($validateInfo)) {
                        $this->log("buy server out of service");
                        break;
                    }
                    $validateArray = json_decode($validateInfo, true);
                    if ($validateArray['status'] == '21007') {
                        $validateInfo = $this->ValidateAppleServer($data['code'], Yii::$app->params['ios_validation_sandbox']);
                        if (empty($validateInfo)) {
                            $this->log("sandbox server out of service");
                            break;
                        }
                        $validateArray = json_decode($validateInfo, true);
                    }
                    if ($validateArray['status'] == '0') {//校验成功
                        $receipt = $validateArray['receipt'];
                        if (!empty($receipt)) {
                            $in_app_array = $receipt['in_app'];
                            if (!empty($in_app_array)) {
                                foreach ($in_app_array as $receipt_array) {
                                    $flag = RechargeOrder::addCoins($receipt_array['transaction_id']);
                                    if ($flag === true) {
                                        $this->log('transaction id: ' . $receipt_array['transaction_id'] . " add coins ok!");
                                    } elseif ($flag === false) {
                                        $this->log('transaction id: ' . $receipt_array['transaction_id'] . " add coins failed.");
                                    } elseif ($flag === '') {
                                        $this->log('transaction id: ' . $receipt_array['transaction_id'] . " repeated.");
                                    }
                                }
                            }
                        }
                    } elseif (in_array($validateArray['status'], ['21000', '21002', '21003'])) {
                        $query = "update edu_recharge_validateinfo set delflg = 1 where transaction_id = '{$receipt_array['transaction_id']}'";//清除 队列 有效标志
                        Yii::$app->db->createCommand($query)->execute();
                        $this->log($validateInfo);
                    }
                }
            } else {
                //$this->log("no request");
            }
            sleep(1);
        }
    }

    private function log($txt)
    {
        $path = Yii::$app->params['external_log_path'];
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        $fp = fopen($path . '/iap_confirm_log.txt', "a");
        flock($fp, LOCK_EX);
        fwrite($fp, date('Y-m-d H:i:s') . "\n" . $txt . "\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    private function ValidateAppleServer($receipt_data, $url, $method = "POST")
    {
        $data = '{"receipt-data":"' . $receipt_data . '"}';
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        }
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 60); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        //curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        //curl_setopt($curl, CURLOPT_PROXY, "11.11.2.1"); //代理服务器地址
        //curl_setopt($curl, CURLOPT_PROXYPORT, 8888); //代理服务器端口
        $begin = microtime(true);
        $tmpInfo = curl_exec($curl); // 执行操作
        $this->log('curl exec ' . (microtime(true) - $begin) . ' seconds');
        if (curl_errno($curl)) {
            $errnoMsg = 'Curl error: ' . curl_error($curl);
            error_log($errnoMsg);
            return null;
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }

}
