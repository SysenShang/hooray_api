<?php

namespace console\Controllers;

use yii;
use yii\console\Controller;
use JPush\Model as M;
use JPush\JPushClient;
use JPush\Exception\APIRequestException;
use common\models\PassportMessages;

/**
 * Class JPushController
 * @package console\Controllers
 */
class JPushController extends Controller
{
    private static $padClient;
    private static $mobileClient;
    private static $appkey;
    private static $masterkey;

    private static function getPadClient()
    {
        if (null === self::$padClient) {
            self::$appkey    = Yii::$app->params['jpush-pad']['appkey'];
            self::$masterkey = Yii::$app->params['jpush-pad']['masterkey'];
            self::$padClient = new JPushClient(self::$appkey, self::$masterkey);
        }
        return self::$padClient;
    }

    private static function getMobileClient()
    {
        if (null === self::$mobileClient) {
            self::$appkey       = Yii::$app->params['jpush-mobile']['appkey'];
            self::$masterkey    = Yii::$app->params['jpush-mobile']['masterkey'];
            self::$mobileClient = new JPushClient(self::$appkey, self::$masterkey);
        }
        return self::$mobileClient;
    }

    /**
     * @author grg
     */
    public function actionSend()
    {
        set_time_limit(300);
        $audience = json_decode(func_get_arg(0), true);
        $content  = json_decode(func_get_arg(1), true);
        $extras   = $content;
        unset($extras['title']);
        $extras['time'] = array_key_exists('time', $extras) ? $extras['time'] : date('Y-m-d H:i:s');
        $extras['type'] = array_key_exists('type', $extras) ? $extras['type'] : '1000';
        $this->push($audience, $content, $extras);
        if (is_array($audience)) {
            $this->saveNotification($extras['type'], $audience, $content);
        }
    }

    private function push($audience, $content, $extras)
    {
        $sound = 'sound.caf';
        try {
            self::getPadClient()
                ->push()
                ->setPlatform(M\platform('ios', 'android'))
                ->setAudience(is_array($audience) ? M\alias($audience) : M\tag(explode(',', $audience)))
                ->setNotification(M\notification(M\ios($content['title'], $sound, 0), M\android($content['title'])))
                ->setMessage(M\message($content['title'], 'Hooray HD', null, $extras))
                ->setOptions(M\options(null, 864000, null, YII_ENV === 'prod'))
                ->send();
        } catch (APIRequestException $e) {
        }
        try {
            self::getMobileClient()
                ->push()
                ->setPlatform(M\platform('ios', 'android'))
                ->setAudience(is_array($audience) ? M\alias($audience) : M\tag(explode(',', $audience)))
                ->setNotification(M\notification(M\ios($content['title'], $sound, 0), M\android($content['title'])))
                ->setMessage(M\message($content['title'], 'Hooray', null, $extras))
                ->setOptions(M\options(null, 864000, null, YII_ENV === 'prod'))
                ->send();
        } catch (APIRequestException $e) {
        }
    }

    /**
     * 保存消息到db
     * @access public
     * @param string $type (1000:系统消息,2000:课程, 3000：问答, 4000:充值)
     * @param array $userArray <b>接收用户ID 数组.例:(array("478794343558152192","478794343558152192"))</b>
     * @param array $dataArray <b>消息内容数组(一维,可包含多个字段的内容).
     * 例: array("title" => "这是推送消息标题", "content" => "这是推送消息内容", "time" => "这是推送消息时间")</b>
     * @return void
     * @author zhuyongchao
     */
    private function saveNotification($type, $userArray, $dataArray)
    {
        if ('' === $type || 0 === count($userArray) || 0 === count($dataArray)) {
            return;
        }
        $time = date('Y-m-d H:i:s');
        foreach ($userArray as $uid) {
            $message           = new PassportMessages();
            $message->sender   = $userArray[0];
            $message->user_id  = $uid;
            $message->type     = $type;
            $message->cat_id   = 0;
            $message->message  = $dataArray['title'];
            $message->reg_date = $time;
            $message->upd_date = $time;
            $message->save();
        }
    }
}
