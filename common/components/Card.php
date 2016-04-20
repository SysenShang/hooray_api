<?php

/**
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 11/11/15
 * Time: 10:27 AM
 * File: Card.php
 * @param status -1:作废 0:已生成，未激活 1:分发，可用，已激活 2:已用
 */

namespace common\components;

use yii;
use common\models\Cards;

class Card
{
    private static $cipher = MCRYPT_RIJNDAEL_128; //密码类型
    private static $modes  = MCRYPT_MODE_ECB; //密码模式
    private static $key    = 'key:118899916786';

    /**
     * @param $str
     * @param mixed $key
     * @return mixed|string
     * @author grg
     */
    public static function crypt($str, $key = null)
    {
        $key = null === $key ? self::$key : $key;
        $miv  = mcrypt_create_iv(mcrypt_get_iv_size(self::$cipher, self::$modes), MCRYPT_RAND);//初始化向量

        return self::safeB64encode(mcrypt_encrypt(self::$cipher, $key, $str, self::$modes, $miv)); //加密函数
    }

    public function encode($price, $user_id = 0, $num = 1, $expired_at = '', $prefix = '')
    {
        $idx = 0;
        do {
            $str = microtime(true) . ':' . $price . ':' . mt_rand(-100000, 100000);

            $str_encrypt = self::crypt($str);

            $code = strtoupper(substr($str_encrypt, 0, 8));
            $ret  = false;
            if (ctype_alnum($code) && strpos($code, 'O') === false && strpos($code, '0') === false &&
                strpos($code, 'I') === false && strpos($code, '1') === false) {
                $card = new Cards();

                $card['key']        = $code;
                $card['prefix']     = $prefix;
                $card['crypt']      = $str_encrypt;
                $card['price']      = $price;
                $card['user_id']    = (string)$user_id;
                $card['expired_at'] = date('Y-m-d H:i:s', strtotime($expired_at . '23:59:59'));

                $ret = $card->save();
                usleep(13);
            }
            if ($ret) {
                $idx++;
            }
        } while ($idx < $num);

    }

    /**
     * @param $str_encrypt
     * @param mixed $key
     * @return string
     * @author grg
     */
    public function decode($str_encrypt, $key = null)
    {
        $key = null === $key ? self::$key : $key;
        $miv  = mcrypt_create_iv(mcrypt_get_iv_size(self::$cipher, self::$modes), MCRYPT_RAND);//初始化向量

        return mcrypt_decrypt(self::$cipher, $key, self::safeB64decode($str_encrypt), self::$modes, $miv); //解密函数
    }

    //处理特殊字符

    public static function safeB64encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(['+', '/', '='], ['-', '_', ''], $data);
        return $data;
    }

    //解析特殊字符

    public static function safeB64decode($string)
    {
        $data = str_replace(['-', '_'], ['+', '/'], $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
}
