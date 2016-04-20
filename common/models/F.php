<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 7/29/15
 * Time: 10:00 AM
 */
namespace common\models;

use yii;

class F
{
    public static function genTree($items, $id = 'cat_id', $pid = 'fid', $son = 'children')
    {
        $tree = array(); // 格式化的树
        $tmpMap = array(); // 临时扁平数据

        foreach ($items as $item) {
            $tmpMap[$item[$id]] = $item;
        }

        foreach ($items as $item) {
            if (isset($tmpMap[$item[$pid]])) {
                $tmpMap[$item[$pid]][$son][] = &$tmpMap[$item[$id]];
            } else {
                $tree[] = &$tmpMap[$item[$id]];
            }
        }
        unset($tmpMap);
        return $tree;
    }


    public static function grd_id($type = '小学')
    {
        if ($type == '小学') {
            return array(5, 6, 7, 8, 9, 10);
        } elseif ($type == '初中') {
            return array(11, 12, 13);
        } elseif ($type == '高中') {
            return array(14, 15, 16);
        }
    }


    public static function stg_id($type)
    {
        $senior_school = array(14, 15, 16);
        $junior_school = array(11, 12, 13);
        $primary_school = array(5, 6, 7, 8, 9, 10);
        if (in_array($type, $junior_school)) {
            return '初中';
        } elseif (in_array($type, $senior_school)) {
            return '高中';
        } elseif (in_array($type, $primary_school)) {
            return '小学';
        } else {
            return '其他';
        }
    }

    public static function open_session()
    {
        $session = Yii::$app->session;
        if (!$session->isActive) {
            $session->open();
        }
        return $session;
    }

    public static function close_session()
    {
        $session = self::open_session();
        return $session->close();
    }

    public static function destory_session()
    {
        $session = self::open_session();
        return $session->destroy();
    }

    /**
     * 友好的时间显示
     * @param int $sTime 待显示的时间
     * @param string $type 类型. normal | mohu | full | ymd | other
     * @param string $alt 已失效
     * @return string
     */
    public static function friendlyDate($sTime, $type = 'normal', $alt = 'false')
    {
        if (!$sTime)
            return '';
        //sTime=源时间，cTime=当前时间，dTime=时间差
        $cTime = time();
        $sTime = strtotime($sTime);
        $dTime = $cTime - $sTime;
        $dDay = intval(date("z", $cTime)) - intval(date("z", $sTime));
        //$dDay     =   intval($dTime/3600/24);
        $dYear = intval(date("Y", $cTime)) - intval(date("Y", $sTime));
        //normal：n秒前，n分钟前，n小时前，日期
        if ($type == 'normal') {
            if ($dTime < 60) {
                if ($dTime < 10) {
                    return '刚刚';    //by yangjs
                } else {
                    return intval(floor($dTime / 10) * 10) . "秒前";
                }
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
                //今天的数据.年份相同.日期相同.
            } elseif ($dYear == 0 && $dDay == 0) {
                //return intval($dTime/3600)."小时前";
                return '今天' . date('H:i', $sTime);
            } elseif ($dYear == 0) {
                return date("m月d日 H:i", $sTime);
            } else {
                return date("Y-m-d H:i", $sTime);
            }
        } elseif ($type == 'mohu') {
            if ($dTime < 60) {
                return $dTime . "秒前";
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
            } elseif ($dTime >= 3600 && $dDay == 0) {
                return intval($dTime / 3600) . "小时前";
            } elseif ($dDay > 0 && $dDay <= 7) {
                return intval($dDay) . "天前";
            } elseif ($dDay > 7 && $dDay <= 30) {
                return intval($dDay / 7) . '周前';
            } elseif ($dDay > 30) {
                return intval($dDay / 30) . '个月前';
            }
            //full: Y-m-d , H:i:s
        } elseif ($type == 'full') {
            return date("Y-m-d , H:i:s", $sTime);
        } elseif ($type == 'ymd') {
            return date("Y-m-d", $sTime);
        } else {
            if ($dTime < 60) {
                return $dTime . "秒前";
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
            } elseif ($dTime >= 3600 && $dDay == 0) {
                return intval($dTime / 3600) . "小时前";
            } elseif ($dYear == 0) {
                return date("Y-m-d H:i:s", $sTime);
            } else {
                return date("Y-m-d H:i:s", $sTime);
            }
        }
    }

    /**
     * @param $t  类型 根据类型生成订单id
     * @return string
     */
    public static function generateOrderSn($t)
    {

        $orderType = array(
            "register" => 300,
            "login" => 301,
            "course" => 350,
            "recharge" => 351,
            "qa" => 352,
            "withdraw" => 353,
            "weike" => 354,
        );
        $default = 500;

        if (empty($orderType[$t])) {
            $pre = $default;
        } else {
            $pre = $orderType[$t];
        }
        return $pre . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -4) . substr(microtime(), 2, 5) . sprintf('%003d', rand(0, 999));
    }


    /**
     * 检测手机号码
     * @param $mobile
     * @return bool
     */
    public static function validateMobile($mobile)
    {
        $phone_parttern = '/^1(?:3[\d]|5[012356789]|8[\d]|7[01678]|4[57])(-?)\d{4}\1\d{4}$/';
        return preg_match($phone_parttern, $mobile);

        ///**
        // * 手机号码
        // * 移动：134[0-8],135,136,137,138,139,150,151,157,158,159,182,187,188,183,147
        // * 联通：130,131,132,152,155,156,185,186
        // * 电信：133,1349,153,180,189
        // */
        //$res1= preg_match('/^1(3[0-9]|5[0-35-9]|8[025-9])\\d{8}$/', $mobile);
        //
        ///**
        // * 中国移动：China Mobile
        //11         * 134[0-8],135,136,137,138,139,150,151,157,158,159,182,187,188,183,147
        // */
        //$res2= preg_match('/^1(34[0-8]|(3[5-9]|5[017-9]|8[2378]|4[7]|7[0])\\d)\\d{7}$/', $mobile);
        //
        ///**
        // * 中国联通：China Unicom
        // * 130,131,132,152,155,156,185,186
        // */
        //$res3= preg_match('/^1(3[0-2]|5[256]|8[56])\\d{8}$/', $mobile);
        //
        ///**
        // * 中国电信：China Telecom
        // * 133,1349,153,180,189,181
        // */
        //$res4= preg_match('/^1((33|53|8[019])[0-9]|349)\\d{7}$/', $mobile);
        //
        ///**
        // * 大陆地区固话及小灵通
        // * 区号：010,020,021,022,023,024,025,027,028,029
        // * 号码：七位或八位
        // */
        //$res5= preg_match('/^0(10|2[0-5789]|\\d{3})\\d{7,8}$/', $mobile);
        //
        //if ( $mobile && ( $res1 ||$res2 ||$res3 ||$res4   ) )
        //{
        //    return true;
        //}else{
        //    return false;
        //}

    }


    /**
     * +----------------------------------------------------------
     * 生成随机字符串  生成短信规则
     * +----------------------------------------------------------
     * @param int $length 要生成的随机字符串长度
     * @param string $type 随机码类型：0，数字+大小写字母；1，数字；2，小写字母；3，大写字母；4，特殊字符；-1，数字+大小写字母+特殊字符
     * +----------------------------------------------------------
     * @return string
    +----------------------------------------------------------
     */
    public static function randCode($length = 5, $type = 0)
    {
        $arr = array(1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4 => "~@#$%^&*(){}[]|");
        if ($type == 0) {
            array_pop($arr);
            $string = implode("", $arr);
        } elseif ($type == "-1") {
            $string = implode("", $arr);
        } else {
            $string = $arr[$type];
        }
        $count = strlen($string) - 1;
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $string[rand(0, $count)];
        }
        return $code;
    }

    /**
     * 核对邮箱
     *
     * @access public
     * @param unknown $email
     * @return return_type
     * @version 1.0.0 (Aug 9, 2014)
     * @author weili
     */
    static public function checkEmail($email)
    {
        if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 把数据写入REDIS
     * @param $name
     * @param $user_id
     * @param $array
     */
    static public function addRedis($name, $user_id, $array)
    {
        $redisUsers = U::fromredis($name, $user_id);
        return $redisUsers->hmadd($array);
    }

}
