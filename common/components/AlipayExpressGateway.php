<?php
/**
 * Created by PhpStorm.
 * User: kevingates
 * Date: 2016-2-1
 * Time: 3:56pm
 * Alipay Express Gateway
 */
namespace common\components;
/**
 * Class AlipayExpressGateway
 *
 * @package common\components\AlipayExpressGateway
 */
class AlipayExpressGateway extends BaseAbstractGateway
{

    protected $service = 'create_direct_pay_by_user';

    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     */
    public function getName()
    {
        return $this->service;
    }

    public function createPurchase()
    {
        return $this->purchase();
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->verifyNotify($parameters);
    }
}


/**
 * Alipay Base Gateway Class
 */
abstract class BaseAbstractGateway
{
    protected $endpoint = 'http://notify.alipay.com/trade/notify_query.do?';
    protected $endpointHttps = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';

    protected $liveEndpoint = 'https://mapi.alipay.com/gateway.do';
    protected $parameters;
    protected $key;
    protected $sign_type = "MD5";
    protected $cacert = '';

    public function getDefaultParameters()
    {
        return array(
            '_input_charset' => 'utf-8',
            'payment_type'  => 1,
            'service' => 'create_direct_pay_by_user',
            'transport' => 'http'
        );
    }

    public function purchase()
    {
        $data= array_merge($this->getDefaultParameters(), $this->parameters);
        $data = array_filter($data);

        $data['sign']      = $this->getParamsSignature($data);
        $data['sign_type'] = strtoupper(trim($this->sign_type));

        return $this->liveEndpoint."?".http_build_query($data);
    }

    public function __set($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->parameters)) {
            return $this->parameters[$name];
        }
        return null;
    }

    protected function getParamsSignature($data)
    {
        ksort($data);
        reset($data);
        $query = http_build_query($data);
        $query = urldecode($query);

        if ($this->sign_type == 'MD5') {
            $sign = $this->signWithMD5($query);
        } else {
            $sign = '';
        }

        return $sign;
    }

    protected function signWithMD5($query)
    {
        return md5($query . $this->key);
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * 1.生成签名结果
     * 2.获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
     * @return 验证结果
     */
    public function verifyNotify(array $parameters = array())
    {
        if (empty($parameters)) {
            return false;
        } else {
            //生成签名结果
            $isSign = $this->getSignVeryfy($parameters, $parameters["sign"]);
            //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
            $responseTxt = 'false';
            if (!empty($parameters["notify_id"])) {
                $responseTxt = $this->getResponse($parameters["notify_id"]);
            }

            if (preg_match("/true$/i", $responseTxt) && $isSign) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 获取返回时的签名验证结果
     */
    public function getSignVeryfy($parameters, $sign)
    {
        //remove sign,sign_type
        unset($parameters['sign']);
        unset($parameters['sign_type']);
        //除去待签名参数数组中的空值和签名参数
        $parameters = array_filter($parameters);
        //对待签名参数数组排序
        ksort($parameters);
        reset($parameters);
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串

        $query = http_build_query($parameters);
        $query = urldecode($query);

        $isSgin = false;
        switch (strtoupper(trim($this->sign_type))) {
            case "MD5":
                $isSgin = $this->md5Verify($query, $sign, $this->key);
                break;
            default:
                $isSgin = false;
        }

        return $isSgin;
    }

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
    public function getResponse($notify_id)
    {
        $DefaultParameters = $this->getDefaultParameters();
        $transport = strtolower(trim($DefaultParameters['transport']));
        $partner = trim($this->parameters['partner']);
        if ($transport == 'https') {
            $veryfyUrl = $this->endpointHttps;
        } else {
            $veryfyUrl = $this->endpoint;
        }
        $veryfyUrl = $veryfyUrl."partner=" . $partner . "&notify_id=" . $notify_id;
        $this->cacert = getcwd().'\\cacert.pem';
        $responseTxt = $this->getHttpResponseGET($veryfyUrl, $this->cacert);

        return $responseTxt;
    }

    /**
     * 远程获取数据，GET模式
     */
    public function getHttpResponseGET($url, $cacertUrl)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        curl_setopt($curl, CURLOPT_CAINFO, $cacertUrl);//证书地址
        $responseText = curl_exec($curl);
        curl_close($curl);

        return $responseText;
    }

    /**
     * 验证签名
     * return 签名结果
     */
    public function md5Verify($prepareSign, $sign, $key)
    {
        $prepareSign = $prepareSign . $key;
        $newSign = md5($prepareSign);

        if ($newSign == $sign) {
            return true;
        } else {
            return false;
        }
    }
}
