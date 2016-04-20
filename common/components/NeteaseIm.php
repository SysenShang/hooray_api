<?php

/**
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 12/14/15
 * Time: 11:30 AM
 * File: NeteaseIm.php
 */

namespace common\components;

use common\models\TimeIm;
use common\models\TimeSms;
use Yii;
use linslin\yii2\curl;
use common\models\SmsLog;
use common\models\NeteaseImToken;

class NeteaseIm
{
    public static function getToken($user_id, $user_name = '', $refresh = false)
    {
        $createUrl  = 'https://api.netease.im/nimserver/user/create.action';
        $refreshUrl = 'https://api.netease.im/nimserver/user/refreshToken.action';

        $neteaseImToken = NeteaseImToken::find()->select('token')->where([
            'user_id' => $user_id
        ])->limit(1)->asArray()->one();
        if ($refresh || null === $neteaseImToken || null === $neteaseImToken['token'] || '' === $neteaseImToken['token']) {
            $response = self::curlRequest($refresh ? $refreshUrl : $createUrl, [
                'accid' => $user_id,
                'name' => $user_name
            ]);
            if (null !== $response) {
                $response = json_decode($response, true);
                if ($response['code'] === 414) {
                    $response = self::curlRequest($refreshUrl, [
                        'accid' => $user_id,
                        'name' => $user_name
                    ]);
                    if (null === $response) {
                        return '';
                    }
                    $response = json_decode($response, true);
                }
                if ($response['code'] !== 200) {
                    return '';
                }
                $affected = NeteaseImToken::updateAll([
                    'accid' => $user_id,
                    'token' => $response['info']['token']
                ], 'user_id = "' . $user_id . '"');
                if ($affected === 0) {
                    $newIm          = new NeteaseImToken();
                    $newIm->user_id = $user_id;
                    $newIm->accid   = $user_id;
                    $newIm->token   = $response['info']['token'];
                    $newIm->save();
                }
                return $response['info']['token'];
            } else {
                return '';
            }
        } else {
            return $neteaseImToken['token'];
        }
    }

    public static function sendCode($mobile)
    {
        $url      = 'https://api.netease.im/sms/sendcode.action';
        $response = self::curlRequest($url, [
            'mobile' => $mobile
        ]);
        if (null === $response) {
            return false;
        }
        $response = json_decode($response, true);
        return $response['code'] === 200;
    }

    public static function verifyCode($mobile, $code)
    {
        $url      = 'https://api.netease.im/sms/verifycode.action';
        $response = self::curlRequest($url, [
            'mobile' => $mobile,
            'code' => $code
        ]);
        if (null === $response) {
            return false;
        }
        $response = json_decode($response, true);
        return $response['code'] === 200;
    }

    public static function sendMsg($mobile, $msg, $time = null)
    {
        if (null !== $time) {
            $timeSms          = new TimeSms();
            $timeSms->mobile  = serialize($mobile);
            $timeSms->msg     = $msg;
            $timeSms->at_time = $time;
            return $timeSms->save();
        }
        $param = [$msg, ''];
        if (mb_strlen($msg, 'UTF-8') > 30) {//30个字一个变量
            $param = [
                mb_substr($msg, 0, 30, 'UTF-8'),
                mb_substr($msg, 30, null, 'UTF-8')
            ];
        }
        $url      = 'https://api.netease.im/sms/sendtemplate.action';
        $response = self::curlRequest($url, [
            'templateid' => 8078,
            'mobiles' => json_encode($mobile),
            'params' => json_encode($param)
        ]);
        if (null === $response) {
            return false;
        }
        $response = json_decode($response, true);
        if ($response['code'] !== 200) {
            $smsLog              = new SmsLog();
            $smsLog->username    = $mobile;
            $smsLog->uid         = 0;
            $smsLog->datetime    = date('Y-m-d H:i:s');
            $smsLog->version     = 0;
            $smsLog->status_code = $response['code'];
            $smsLog->save();
        }
        return $response['code'] === 200;
    }

    /**
     *
     * creator    String    是    聊天室属主的账号accid
     * name    String    是    聊天室名称，长度限制128个字符
     * announcement    String    否    公告，长度限制4096个字符
     * broadcasturl    String    否    直播地址，长度限制1024个字符
     * ext    String    否    扩展字段，最长4096字节
     * @param $data
     * @return bool
     * @author grg
     */
    public static function createChatRoom($data)
    {
        $url      = 'https://api.netease.im/nimserver/chatroom/create.action';
        $response = self::curlRequest($url, $data);
        if (null === $response) {
            return false;
        }
        $response = json_decode($response, true);
        return $response['code'] === 200 ? $response['chatroom']['roomid'] : false;
    }

    /**
     * 修改聊天室开/关闭状态
     * @author grg
     */
    public static function toggleChatRoom($data, $time = null)
    {
        if (null !== $time && $time > date('Y-m-d H:i:s')) {
            $timeIm = new TimeIm();
            $timeIm->setAttributes($data);
            $timeIm->at_time = $time;
            return $timeIm->save();
        }
        $url      = 'https://api.netease.im/nimserver/chatroom/toggleCloseStat.action';
        $response = self::curlRequest($url, $data);
        if (null === $response) {
            return false;
        }
        $response = json_decode($response, true);
        $valid    = array_key_exists('valid', $response) && $response['valid'] === ($data['valid'] === 'true');
        return $valid || array_key_exists('code', $response) && $response['code'] === 417;
    }

    public static function getChatRoom($data)
    {
        $url = 'https://api.netease.im/nimserver/chatroom/get.action';
        return self::curlRequest($url, $data);
    }

    private static function curlRequest($url, $data)
    {
        $Nonce    = md5(mt_rand());
        $CurTime  = time();
        $CheckSum = sha1(Yii::$app->params['netease_im']['AppSecret'] . $Nonce . $CurTime);
        $header   = [
            'AppKey: ' . Yii::$app->params['netease_im']['AppKey'],
            'Nonce: ' . $Nonce,
            'CurTime: ' . $CurTime,
            'CheckSum: ' . $CheckSum,
            'Content-Type: application/x-www-form-urlencoded',
        ];

        try {
            $curl     = new curl\Curl();
            $response = $curl->setOption(CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4)
                             ->setOption(CURLOPT_SSL_VERIFYPEER, false)
                             ->setOption(CURLOPT_SSL_VERIFYHOST, false)
                             ->setOption(CURLOPT_TIMEOUT, 30)
                             ->setOption(CURLOPT_RETURNTRANSFER, 1)
                             ->setOption(CURLOPT_HTTPHEADER, $header)
                             ->setOption(CURLOPT_HEADER, 0)
                             ->setOption(CURLOPT_POSTFIELDS, http_build_query($data))
                             ->post($url);
            return $curl->responseCode === 200 ? $response : null;
        } catch (\Exception $e) {
            return null;
        }

    }
}
