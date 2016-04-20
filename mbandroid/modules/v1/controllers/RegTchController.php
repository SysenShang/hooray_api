<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 9/29/15
 * Time: 1:46 PM
 */

namespace mbandroid\modules\v1\controllers;


use common\components\Emay;
use common\components\Invite;
use common\components\NeteaseIm;
use common\models\CoinLog;
use common\models\F;
use common\models\TchCount;
use common\models\TchLog;
use common\models\TchVerify;
use common\models\TeacherInfo;
use common\models\User;
use common\models\WordFilter;
use yii;
use yii\base\ErrorException;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;

class RegTchController extends ActiveController
{
    public $modelClass = 'common\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'except' => ['index', 'create', 'vercode', 'checkname', 'sms'],  // set actions for disable access!
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        $behaviors['verbFilter'] = [
            'class' => VerbFilter::className(),
//                'actions' => $this->verbs(),
            'actions' => [
                'index' => ['get'],
                'view' => ['get'],
                'create' => ['get', 'post'],
                'update' => ['get', 'put', 'post'],
                'delete' => ['post', 'delete'],
            ],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['Index', 'Create', 'delete', 'update', 'view', 'post'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'create', 'delete', 'update', 'view', 'post'],
                    'roles' => ['@'],
                ],
                [
                    'allow' => true,
                    'actions' => ['view'],
                    'roles' => ['?'],
                ],
                // everything else is denied
            ],
            'denyCallback' => function () {
                throw new \Exception('您无权访问该页面');
            },
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);

        return $actions;
    }

    /**
     * 注册老师
     */
    public function actionCreate()
    {
        $postData = Yii::$app->request->post();
        $username = $postData['username'];
        $password = trim($postData['password']);
        $telephone = isset($postData['telephone']) ? $postData['telephone'] : "";
        $gender = isset($postData['gender']) ? $postData['gender'] : "男";
        $pwsafety = isset($postData['pwsafety']) ? $postData['pwsafety'] : 1;
        $nickname = isset($postData->nickname) ? $postData->nickname : '';
        $date = date('Y-m-d H:i:s');
        if (strlen($telephone) == 11) {
            $exists = User::findOne([
                'telephone' => $telephone,
                'xstatus' => 1,
            ]);
            if ($exists) {
                $data['status'] = "2001";
                return $data;
            }
        }
        $user_id = ukg_getkey('127.0.0.1', 7954);

        $hash = Yii::$app->getSecurity()->generatePasswordHash($password);

        $user = new User();

        $user->user_id = $user_id;
        $user->invite_code = (string)crc32($user_id);
        $user->username = $username;
        $user->upassword = $hash;
        $user->telephone = $telephone;
        $user->group_id = '2';
        $user->pwsafety = $pwsafety;
        $user->regdate = $date;

        //验证初始化
        $tchVerify = new TchVerify();
        $tchVerify->user_id = $user_id;
        $tchVerify->verify1 = 2;

        //學生信息
        $tchInfo = new TeacherInfo();
        $tchInfo->user_id = $user_id;
        $tchInfo->gender = $gender;
        $tchInfo->nickname = $nickname;

        //初始化log
        $log = new TchLog();
        $log->user_id = $user_id;
        $log->lastlogin = $date;

        //初始化统计
        $tchCount = new TchCount();
        $tchCount->user_id = $user_id;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($user->save() && $tchVerify->save() && $tchInfo->save() && $log->save() && $tchCount->save()) {
                $transaction->commit();
                $data = ['user_id' => $user_id];
                //注册老师送积分
                $CoinLog = new CoinLog();
                $CoinLog->registerTask($user_id, 2);
                Invite::done($user_id, $username, Yii::$app->request->post('invite_code', ''));
            } else {
                throw new ErrorException;
            }
        } catch (ErrorException $e) {
            $transaction->rollBack();
            $data['status'] = "2009";
            $data['msg'] = Yii::$app->params['u_2009'];
        }

        return $data;
    }

    /**
     * 发送短信 老师
     */
    public function actionSms()
    {
        $postData = Yii::$app->request->post();
        $userAgent = Yii::$app->request->getUserAgent();
        $telephone = $postData['telephone'];

        if (!F::validateMobile($telephone)) {
            return ['status' => '2002', 'msg' => Yii::$app->params['u_2002']];
        }
        $model = User::findOne(['telephone' => $telephone, 'xstatus' => 1]);
        if ($model) {
            return ['status' => '2001', 'msg' => Yii::$app->params['u_2001']];
        }
        $hash = md5($telephone . $userAgent);
        $code = Yii::$app->redis->get('code:for:' . $hash);
        if (null === $code) {
            $code = mt_rand(100000, 999999);
        } else {
            $ttl = Yii::$app->redis->ttl('code:for:' . $hash);
            if ((int)$ttl > 540) {//一分钟内,不重复发送
                return ['status' => '200'];
            }
        }

        $msg = "您正在注册 Hooray 教师会员，验证码：{$code}。屏蔽请回 TD";
        $result = NeteaseIm::sendMsg([$telephone], $msg);
        if ($result) {
            Yii::$app->redis->setnx('code:for:' . $hash, $code);
            Yii::$app->redis->expire('code:for:' . $hash, 600);
            return ['status' => '200'];
        } else {
            $emay = new Emay();
            $smsStatus = $emay->sendSMS($telephone, '【燃耀】' . $msg);

            if ($smsStatus !== null && $smsStatus === '0') {
                Yii::$app->redis->setnx('code:for:' . $hash, $code);
                Yii::$app->redis->expire('code:for:' . $hash, 600);
                return ['status' => '200'];
            }
        }
        return ['status' => '2003'];
    }

    /**
     * 检测验证码
     * @return string
     */
    public function actionVercode()
    {
        $postData = Yii::$app->request->post();
        $userAgent = Yii::$app->request->getUserAgent();

        $hash = md5(trim($postData['telephone']) . $userAgent);
        $code = Yii::$app->redis->get('code:for:' . $hash);
        if (null !== $code && $code === $postData['ver_code']) {
            return ['status' => '200'];
        } else {
            return ['status' => '2004'];
        }
    }

    /**
     * 检测用户名
     * @return string
     */
    public function actionCheckname()
    {
        $postData = Yii::$app->request->post();
        $username = trim($postData['username']);
        $filter_name = WordFilter::findOne(['words' => $username]);
        if ($filter_name) {
            $data['status'] = '2005';
            $data['msg'] = Yii::$app->params['u_2005'];
        }

        /**
         * 判断用户名
         *  if(preg_match('/^[a-zA-Z0-9\x{4e00}-\x{9fa5}!@#$%^&*()_+|{}?><\-\]\\[\/]*$/u',$username))
         *   姓名只能为中文英文或者‘_’且首字母为汉字或者英文
         */
        $gbk_username = iconv('utf-8', 'gbk//IGNORE', $username);//由UTF-8转换为GBK 一个汉字站2个字符
        $countlength = strlen($gbk_username);
        if ($countlength <= 3 || $countlength >= 17) {   //3-17个字符，一个汉字为2个字符。
            $data['status'] = '2007';
            $data['msg'] = Yii::$app->params['u_2007'];
        }

        $name = User::findByUser($username);
        if ($name) {
            $data['status'] = '2006';
            $data['msg'] = Yii::$app->params['u_2006'];
        } else {
            $data = '';
        }

        return $data;
    }

}
