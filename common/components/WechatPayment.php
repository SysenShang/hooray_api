<?php
/**
 * Created by PhpStorm.
 * User: kevingates
 * Date: 15-12-21
 * Time: 5:56pm
 * wechat payment
 */

namespace common\components;


use Yii;


class WechatPayment extends BasePaymentGateway
{
    protected $config = array();
    private $_orderParams = array();

    public function createUniqueOrder($orderParams, $config, $timeOut = 6)
    {
        $this->cofig = $config;

        $orderParams['appid'] = $this->cofig['appId'];
        $orderParams['mch_id'] = $this->cofig['merchantId'];
        $orderParams['notify_url'] = $this->cofig['notifyUrl'];
        $orderParams['trade_type'] = $this->cofig['tradeType'];//NATIVE,APP

        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        //检测必填参数
        if(!isset($orderParams['out_trade_no'])) {
            return "缺少统一支付接口必填参数out_trade_no！";
        } elseif (!isset($orderParams['body'])) {
            return "缺少统一支付接口必填参数body！";
        } elseif (!isset($orderParams['total_fee'])) {
            return "缺少统一支付接口必填参数total_fee！";
        }

        $orderParams['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
        $orderParams['nonce_str'] = $this->getNonceStr();
        $orderParams['time_start'] = date('YmdHis');

        //签名
        $sign = $this->SetSign($orderParams);
        $orderParams['sign'] = $sign;
        $xml = $this->ToXml($orderParams);

        $response = $this->postXmlCurl($xml, $url, false, $timeOut);
        $response = $this->FromXml($response);
        $this->_orderParams = $orderParams;//android and Ios need these

        return $response;
    }

    public function notify($resultArray)
    {
        if (!isset($resultArray['return_code'])) {
            return "[WecatPayment]传入的不是合法的微信支付 XML";
        }

        if ($resultArray['return_code']== 'SUCCESS') {
            return true;
        } else {
            return "微信支付失败";
        }
    }

    public function getOrderParams() {
        $this->_orderParams['timestamp'] = time();
        return $this->_orderParams;
    }

    /**
     * 写日志，方便测试（看网站需求，也可以改成把记录存入数据库）
     * 注意：服务器需要开通fopen配置
     * @param $content 要写入日志里的文本内容 默认值：空值
     */
    public function log($content = '')
    {
        $path = Yii::$app->params['external_log_path'];
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        $fp = fopen($path . '/wechatPayment_notify_log.txt', "a");
        flock($fp, LOCK_EX);
        fwrite($fp, "datetime：" . date('Y-m-d H:i:s') . "\n" . $content . "\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}

abstract class BasePaymentGateway
{

    /**
     *
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    public function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /**
     * 直接输出xml
     * @param string $xml
     */
    public function replyNotify($xml)
    {
        echo $xml;
    }


    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param string $xml  需要post的xml数据
     * @param string $url  url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second   url执行超时时间，默认30s
     * @throws WxPayException
     */
    protected function postXmlCurl($xml, $url, $useCert = false, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, WxPayConfig::SSLCERT_PATH);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, WxPayConfig::SSLKEY_PATH);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            exit("curl出错，错误码:".$error);
        }
    }

    /**
     * 获取毫秒级别的时间戳
     */
    protected function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }

    /**
     * 设置签名，详见签名生成算法
     * @param string $value
     **/
    protected function SetSign($orderParams)
    {
        $sign = $this->MakeSign($orderParams);

        return $sign;
    }

    /**
     * 生成签名
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    private function MakeSign($orderParams)
    {
        //签名步骤一：按字典序排序参数
        ksort($orderParams);
        $string = $this->ToUrlParams($orderParams);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$this->cofig['appKey'];
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 格式化参数格式化成url参数
     */
    public function ToUrlParams($orderParams)
    {
        $buff = "";
        foreach ($orderParams as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 输出xml字符
     * @throws WxPayException
     **/
    public function ToXml($orderParams)
    {
        if(!is_array($orderParams)
            || count($orderParams) <= 0)
        {
            return "数组数据异常！";
        }

        $xml = "<xml>";
        foreach ($orderParams as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
    public function FromXml($xml)
    {
        if(!$xml){
            return "xml数据异常！";
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return  json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }
}
