<?php
namespace common\components;
use yii;

class Emay
{
    /**
     * 网关地址
     */
    public $gwUrl = 'http://sdk4report.eucp.b2m.cn:8080/sdk/SDKService';

    /**
     * 序列号,请通过亿美销售人员获取
     */
    public $serialNumber = '6SDK-EMY-6688-KCZSS';

    /**
     * 密码,请通过亿美销售人员获取
     */
    public $password = '939223';

    /**
     * 登录后所持有的SESSION KEY，即可通过login方法时创建
     */
    public $sessionKey = '51707508';

    /**
     * 连接超时时间，单位为秒
     */
    public $connectTimeOut = 2;

    /**
     * 远程信息读取超时时间，单位为秒
     */
    public $readTimeOut = 10;

    public $client;

    /**
     * $proxyhost		可选，代理服务器地址，默认为 false ,则不使用代理服务器
     * $proxyport		可选，代理服务器端口，默认为 false
     * $proxyusername	可选，代理服务器用户名，默认为 false
     * $proxypassword	可选，代理服务器密码，默认为 false
     */
    public $proxyhost = false;

    public $proxyport = false;

    public $proxyusername = false;

    public $proxypassword = false;

    public function __construct()
    {
//        Yii::$classMap['Client'] = 'extension/emay/Client';
//        include_once '/extension/emay/emayLoader.php';
        $path = Yii::getAlias("@third_party/emay/Client.php");
        require_once($path);

        $this->client = new \Client($this->gwUrl,$this->serialNumber,$this->password,$this->sessionKey,$this->proxyhost,$this->proxyport,$this->proxyusername,$this->proxypassword,$this->connectTimeOut,$this->readTimeOut);
        $this->client->setOutgoingEncoding("utf8");
    }
/*  public function connect(){
         $this->client = new Client($this->gwUrl,$this->serialNumber,$this->password,$this->sessionKey,$this->proxyhost,$this->proxyport,$this->proxyusername,$this->proxypassword,$this->connectTimeOut,$this->readTimeOut);
         $this->client->setOutgoingEncoding("utf8");
    } */

    /**
     * 接口调用错误查看 用例
     */
    function chkError()
    {

        $err = $this->client->getError();
        if ($err)
        {
            /**
             * 调用出错，可能是网络原因，接口版本原因 等非业务上错误的问题导致的错误
             * 可在每个方法调用后查看，用于开发人员调试
             */

            echo $err;
        }
    }

    /**
     * 登录 用例
     */
    public  function login()
    {

        /**
         * 下面的操作是产生随机6位数 session key
         * 注意: 如果要更换新的session key，则必须要求先成功执行 logout(注销操作)后才能更换
         * 我们建议 sesson key不用常变
         */
        //$sessionKey = $client->generateKey();
        //$statusCode = $client->login($sessionKey);
        $statusCode = $this->client->login();

        return $statusCode;
    }

    /**
     * 注销登录 用例
     */
    function logout()
    {

        $statusCode = $this->client->logout();
        echo "处理状态码:".$statusCode;
    }

    /**
     * 获取版本号 用例
     */
    function getVersion()
    {

        echo "版本:". $this->client->getVersion();

    }


    /**
     * 取消短信转发 用例
     */
    function cancelMOForward()
    {


        $statusCode = $this->client->cancelMOForward();
        echo "处理状态码:".$statusCode;
    }

    /**
     * 短信充值 用例
     */
    function chargeUp()
    {

        /**
         * $cardId [充值卡卡号]
         * $cardPass [密码]
         * 请通过亿美销售人员获取 [充值卡卡号]长度为20内 [密码]长度为6
         */

        $cardId = 'EMY01200810231542008';
        $cardPass = '123456';
        $statusCode = $this->client->chargeUp($cardId,$cardPass);
        echo "处理状态码:".$statusCode;
    }

    /**
     * 查询单条费用 用例
     */
    function getEachFee()
    {
        $fee = $this->client->getEachFee();
        echo "费用:".$fee;
    }

    /**
     * 企业注册 用例
     */
    function registDetailInfo()
    {

        $eName = "上海燃耀信息科技有限公司";
        $linkMan = "魏利";
        $phoneNum = "021-51707509";
        $mobile = "15901771973";
        $email = "webwlsong@vip.qq.com";
        $fax = "021-51707509";
        $address = "上海闵行区漕宝路1243号勇卫商务大厦4楼3A88室";
        $postcode = "200235";

        /**
         * 企业注册  [邮政编码]长度为6 其它参数长度为20以内
         *
         * @param string $eName 	企业名称
         * @param string $linkMan 	联系人姓名
         * @param string $phoneNum 	联系电话
         * @param string $mobile 	联系手机号码
         * @param string $email 	联系电子邮件
         * @param string $fax 		传真号码
         * @param string $address 	联系地址
         * @param string $postcode  邮政编码
         *
         * @return int 操作结果状态码
         *
         */
        $statusCode = $this->client->registDetailInfo($eName,$linkMan,$phoneNum,$mobile,$email,$fax,$address,$postcode);
        echo "处理状态码:".$statusCode;

    }

    /**
     * 更新密码 用例
     */
    function updatePassword()
    {

        /**
         * [密码]长度为6
         *
         * 如下面的例子是将密码修改成: 654321
         */
        $statusCode = $this->client->updatePassword('654321');
        echo "处理状态码:".$statusCode;
    }

    /**
     * 短信转发 用例
     */
    function setMOForward()
    {


        /**
         * 向 159xxxxxxxx 进行转发短信
         */
        $statusCode = $this->client->setMOForward('159xxxxxxxx');
        echo "处理状态码:".$statusCode;
    }

    /**
     * 得到上行短信 用例
     */
    function getMO()
    {
        $moResult = $this->client->getMO();
        echo "返回数量:".count($moResult);
        foreach($moResult as $mo)
        {
            //$mo 是位于 Client.php 里的 Mo 对象
            // 实例代码为直接输出
            echo "发送者附加码:".$mo->getAddSerial();
            echo "接收者附加码:".$mo->getAddSerialRev();
            echo "通道号:".$mo->getChannelnumber();
            echo "手机号:".$mo->getMobileNumber();
            echo "发送时间:".$mo->getSentTime();

            /**
             * 由于服务端返回的编码是UTF-8,所以需要进行编码转换
            */
            echo "短信内容:".iconv("UTF-8","GBK",$mo->getSmsContent());

            // 上行短信务必要保存,加入业务逻辑代码,如：保存数据库，写文件等等
        }

    }

    /**
     * 短信发送 用例
     */
    function sendSMS($mobile,$content)
    {
        /**
         * 下面的代码将发送内容为 test 给 159xxxxxxxx 和 159xxxxxxxx
         * $client->sendSMS还有更多可用参数，请参考 Client.php
         */
        $statusCode = $this->client->sendSMS(array($mobile),$content);
        return $statusCode;
    }

    /**
     * 发送语音验证码 用例
     */
    function sendVoice($mobile,$number)
    {
        /**
         * 下面的代码将发送验证码123456给 159xxxxxxxx
         * $client->sendSMS还有更多可用参数，请参考 Client.php
         */
        $statusCode = $this->client->sendVoice(array($mobile),$number);
        return $statusCode;
    }

    /**
     * 余额查询 用例
     */
    function getBalance()
    {
        $balance = $this->client->getBalance();
        echo "余额:".$balance;
    }

    /**
     * 短信转发扩展 用例
     */
    function setMOForwardEx()
    {
        /**
         * 向多个号码进行转发短信
         *
         * 以数组形式填写手机号码
         */
        $statusCode = $this->client->setMOForwardEx(
            array('159xxxxxxxx','159xxxxxxxx','159xxxxxxxx')
        );
        echo "处理状态码:".$statusCode;
    }

    function sendTchSMS($mobile,$verification_code)
    {
        $statusCode =  $this->client->sendSMS(array($mobile), "【燃耀】正在注册为hooray老师会员,手机验证码:$verification_code.如需屏蔽信息回复TD");

        return $statusCode;
    }


    function sendStuSMS($mobile,$verification_code)
    {
        $statusCode = $this->client->sendSMS(array($mobile), "【燃耀】正在注册为hooray学生会员,手机验证码:$verification_code.如需屏蔽信息回复TD");

        return $statusCode;
    }

    function sendBackSMS($mobile,$verification_code)
    {
        $statusCode = $this->client->sendSMS(array($mobile), "【燃耀】好哇,您正在进行找回密码操作,切勿将验证泄露于他人.如验证码泄漏会有账户被盗风险.验证码:$verification_code.如需屏蔽信息回复TD");

        return $statusCode;
    }

}
