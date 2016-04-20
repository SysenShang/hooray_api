<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 7/16/15
 * Time: 7:19 PM
 */
namespace mbandroid\modules\v1\controllers;

use common\components\Emay;
use common\components\JPushNotice;
use common\components\NeteaseIm;
use common\models\AccessTokens;
use common\models\Area;
use common\models\CoinLog;
use common\models\F;
use common\models\HoorayUserinfo;
use common\models\InviterBonus;
use common\models\Invites;
use common\models\Rating;
use common\models\School;
use common\models\SmsLog;
use common\models\StuCount;
use common\models\StudentInfo;
use common\models\Subjects;
use common\models\Task;
use common\models\TchCount;
use common\models\TchVerify;
use common\models\TeacherInfo;
use common\models\User;
use common\models\UsersLoginHistory;
use common\models\VerifyTeaching;
use yii;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;

class UserController extends ActiveController
{
    public $modelClass = 'common\models\User';
//
//    public $serializer = [
//        'class' => 'yii\rest\Serializer',
//        'collectionEnvelope' => 'items',
//    ];
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
//            'except' => ['index'],  // set actions for disable access!
//            'class' => QueryParamAuth::className(),

            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['index'],
            'rules' => [
                // allow authenticated users
//                [
//                    'allow' => true,
//                    'actions' => ['index'],
//                    'roles' => ['?'],
//                ],
                [
                    'allow' => true,
                    'actions' => ['index', 'changepassword', 'verify-teaching', 'update-teacher-info'],
                    'roles' => ['@'],
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
     * @return mixed
     */
    public function actionIndex()
    {
        $userId  = Yii::$app->user->identity->user_id;
        $groupId = Yii::$app->user->identity->group_id;
        $fields  = [
            'u.user_id',
            'invite_code',
            'u.telephone',
            'username',
            'email',
            'pwsafety',
            'regdate',
            'group_id',
            'area_id',
            'school_id',
            'grade',
            'i.realname',
            'i.nickname',
            'i.gender',
            'i.profile',
            'i.avatar',
            'c.credits',
            'c.rating'
        ];
        $query   = User::find();
        if ($groupId === 2) {
            $fields[] = 'i.characteristics';
            $fields[] = 'i.tags';
            $fields[] = 'i.teaching_age';
            $query->select($fields);
            $query->from(User::tableName() . ' u');
            $query->leftJoin(TeacherInfo::tableName() . ' i', 'u.user_id' . '=' . 'i.user_id');
            $query->leftJoin(TchCount::tableName() . ' c', 'u.user_id' . '=' . 'c.user_id');
        } else {
            $query->select($fields);
            $query->from(User::tableName() . ' u');
            $query->leftJoin(StudentInfo::tableName() . ' i', 'u.user_id' . '=' . 'i.user_id');
            $query->leftJoin(StuCount::tableName() . ' c', 'u.user_id' . '=' . 'c.user_id');
        }
        $data = $query->where(['u.user_id' => $userId])->limit(1)->asArray()->one();
        if ($groupId === 2) {
            $data['tags'] = json_decode($data['tags'], true);
            $tchApplyStatus = TchVerify::find()->select('apply_status,verify4,verify5')->where(['user_id' => $userId])->limit(1)->asArray()->one();
            if (null === $tchApplyStatus) {
                $data['apply_status'] = 0;
            } else {
                $data['apply_status'] = $tchApplyStatus['apply_status'];
                if ($tchApplyStatus['verify4'] === '2' && $tchApplyStatus['verify5'] === '2') {
                    $data['apply_status'] = 2;
                }
            }
            //teacher's subjects and ID cards verify status
            $data['is_verify_subject'] = isset($tchApplyStatus['verify4']) ? $tchApplyStatus['verify4'] : 0;
            $data['is_verify_id_cards'] = isset($tchApplyStatus['verify5']) ? $tchApplyStatus['verify5'] : 0;
            $data['teaching'] = VerifyTeaching::find()
                                              ->select('stages_id,stages_name,subjects_id,subjects_name')
                                              ->where(['user_id' => $userId, 'flag' => 2])
                                              ->asArray()
                                              ->all();
        }
        $data['area_name'] = '';
        if ($data['area_id'] > 0) {
            $sp_city           = [1, 2, 9, 22];//直辖市
            $area              = Area::find()->where(['id' => $data['area_id']])->asArray()->limit(1)->one();
            $data['area_name'] = $area['area_name'];
            while ($area['parent_id'] > 0 && !in_array($area['parent_id'], $sp_city, false)) {
                $area              = Area::find()->where(['id' => $area['parent_id']])->asArray()->limit(1)->one();
                $data['area_name'] = $area['area_name'] . ' ' . $data['area_name'];
            }
        }
        $data['schoolname'] = '';
        if ($data['school_id'] > 0) {
            $school             = School::find()->where(['id' => $data['school_id']])->asArray()->limit(1)->one();
            $data['schoolname'] = $school['name'];
        }
        $data = $this->gradeInfo($data);
        $rating = Rating::find()
                        ->where(['rating' => $data['rating'], 'xtype' => 'teacher'])
                        ->asArray()
                        ->limit(1)
                        ->one();

        $data['rating'] = null !== $rating ? (string)$rating['rating_img'] : '';

        $data['netease_im']['accid'] = $userId;
        $data['netease_im']['token'] = NeteaseIm::getToken($userId, $data['username']);

        $lastLogin = UsersLoginHistory::find()
                                      ->select('login_at')
                                      ->where(['user_id' => $userId])
                                      ->orderBy('login_at DESC')
                                      ->limit(1)
                                      ->offset(1)
                                      ->asArray()
                                      ->one();

        $data['last_login'] = null === $lastLogin ? '' : $lastLogin['login_at'];

        $session = F::open_session();
        foreach ($data as $key => $value) {
            $session->set($key, $value);

        }
        F::close_session();

        return $data;
    }

    private function gradeInfo($data)
    {
        $grade_level = ['幼儿园', '小学', '初中', '高中'];
        $grade_name  = ['幼儿园', '一年级', '二年级', '三年级', '四年级', '五年级', '六年级', '初一', '初二', '初三', '高一', '高二', '高三'];

        $data['grade_level'] = '';
        $data['grade_name']  = '';
        if ('' !== (string)$data['grade'] && 0 <= $data['grade'] && $data['grade'] <= 12) {
            $level = 0;
            switch (true) {
                case 1 <= $data['grade'] && $data['grade'] <= 6:
                    $level = 1;
                    break;
                case 7 <= $data['grade'] && $data['grade'] <= 9:
                    $level = 2;
                    break;
                case 10 <= $data['grade']:
                    $level = 3;
                    break;
            }
            $data['grade_level'] = $grade_level[$level];
            $data['grade_name']  = $grade_name[$data['grade']];
        }
        return $data;
    }

    /**
     * 设置昵称
     */
    public function actionNickname()
    {
        $user_id  = Yii::$app->user->getId();
        $postData = Yii::$app->request->post();
        $nickname = trim($postData['nickname']);
        $group_id = Yii::$app->user->identity->group_id;
        if ($group_id === 2) {
            TeacherInfo::updateAll(['nickname' => $nickname], ['user_id' => $user_id]);
        } else {
            StudentInfo::updateAll(['nickname' => $nickname], ['user_id' => $user_id]);
        }
        //更新redis中
        HoorayUserinfo::updateAll(['nickname' => $nickname], ['user_id' => $user_id]);
        $this->isCompleteProfile();
        return ['status' => '200'];
    }

    /**
     * 发送验证码
     */
    public function actionSendcode()
    {
        if (Yii::$app->user->isGuest) {
            return ['status' => '7001']; // 用户未登录
        }
        $postData = Yii::$app->request->post();
        $userAgent = Yii::$app->request->getUserAgent();

        $postData['telephone'] = trim($postData['telephone']);

        $hash = md5($postData['telephone'] . $userAgent);
        $code = Yii::$app->redis->get('code:for:' . $hash);
        if (null === $code) {
            $code = F::randCode(6, 1);
        } else {
            $ttl = Yii::$app->redis->ttl('code:for:' . $hash);
            if ((int)$ttl > 540) {//一分钟内,不重复发送
                return ['status' => '200'];
            }
        }
        $msg = "Hooray！账户操作验证码：{$code}，请勿泄露。如非本人操作，请及时修改密码。屏蔽请回 TD";
        $result = NeteaseIm::sendMsg([$postData['telephone']], $msg);
        if ($result) {
            Yii::$app->redis->setnx('code:for:' . $hash, $code);
            Yii::$app->redis->expire('code:for:' . $hash, 600);
            return ['status' => '200'];
        } else {
            $emay = new Emay();
            $smsStatus = $emay->sendSMS($postData['telephone'], '【燃耀】' . $msg);

            if ($smsStatus !== null && $smsStatus === '0') {
                Yii::$app->redis->setnx('code:for:' . $hash, $code);
                Yii::$app->redis->expire('code:for:' . $hash, 600);
                return ['status' => '200'];
            } else {
                $smsLog = new SmsLog();
                $smsLog->username = '';
                $smsLog->uid = Yii::$app->user->identity->user_id;
                $smsLog->datetime = date('Y-m-d H:i:s');
                $smsLog->version = array_key_exists('HTTP_X_REST_VERSION', $_SERVER) ? $_SERVER['HTTP_X_REST_VERSION'] : 0;
                $smsLog->status_code = $smsStatus;
                $smsLog->save();
                Yii::$app->response->statusCode = 400;
                return ['status' => '8001'];
            }
        }
    }

    /**
     * 校验验证码
     * @return array|string
     */
    public function actionCheckcode()
    {
        $postData = Yii::$app->request->post();
        $userAgent = Yii::$app->request->getUserAgent();

        $hash = md5(trim($postData['telephone']) . $userAgent);
        $code = Yii::$app->redis->get('code:for:' . $hash);
        if (null !== $code && $code === $postData['VerificationCode']) {
            return ['status' => '200'];
        } else {
            Yii::$app->response->statusCode = 400;
            return ['status' => '8003'];
        }
    }

    public function actionChangepassword()
    {
        $postData = Yii::$app->request->post();
        $password = isset($postData['newpassword']) ? $postData['newpassword'] : '';
        if (empty($password) || strlen($password) < 6) {
            return ['status' => '200', 'msg' => Yii::$app->params['s_7017']];
        }
        $user_id = Yii::$app->user->identity->user_id;
        $password = Yii::$app->security->generatePasswordHash($postData['newpassword']);
        $model = User::findOne(['user_id' => $user_id]);
        if (empty($model)) {
            return ['status' => '7001', 'msg' => Yii::$app->params['s_7001']];
        }
        $model->upassword = $password;
        $model->pwsafety = $postData['pwsafety'];
        if ($model->update()) {
            NeteaseIm::getToken($user_id, $model['username'], true); // refreshToken
            Yii::$app->user->logout();
            //改变密码推送消息
            $jpush = new JPushNotice();
            $jpush->send(array($user_id), array("title" => "您的新密码修改成功，请妥善保管！", "type" => 1004));

            return ['status' => '200'];
        } else {
            return ['status' => '7008', 'msg' => Yii::$app->params['s_7008']];
        }
    }

    /**
     * 检查邮箱是否注册
     *
     * @access public
     * @param unknown $data
     * @return return_type
     */
    public function actionCheckemail()
    {
        if (Yii::$app->user->isGuest) {
            return ['status' => '7001']; // 用户未登录
        }
        $postData = Yii::$app->request->post();
        $email = trim($postData['email']);
        $checkEmail = F::checkEmail($email);
        if ($checkEmail) {
            $model = User::findOne(['email' => $email]);
            if (!is_null($model)) {
                Yii::$app->response->statusCode = 400;
                return ['status' => '2110'];//邮箱已经注册
            } else {
                return ['status' => '200'];
            }
        } else {
            Yii::$app->response->statusCode = 400;
            return ['status' => '7015'];//邮箱不正确
        }
    }


    /**
     * 发送验证码到邮箱短信
     * @return array
     */
    public function actionSendemailcode()
    {
        if (Yii::$app->user->isGuest) {
            return ['status' => '7001']; // 用户未登录
        }
        $postData = Yii::$app->request->post();
        $session = Yii::$app->session;

        $email = trim($postData['email']);
        $username = $session['username'];
        $verification_code = F::randCode(6, 1);

        $path = Yii::getAlias("@third_party/sendcloud/SendCloudLoader.php");
        require_once($path);
        //
        // 		include __dir__ . "/../SendCloudLoader.php"; // 导入SendCloud依赖
        //include '/path/to/sendcloud_php/SendCloudLoader.php';或者 导入SendCloud依赖
        // X-SMTPAPI 请参照 http://sendcloud.sohu.com/v2/api-doc/smtp-api-extension.jsp
        try {
            // 设置脚本执行的最长时间，以免附件较大时，需要传输比较久的时间
            // Fatal error: Maximum execution time of 30 seconds exceeded
            // http://php.net/manual/en/function.set-time-limit.php
            // set_time_limit(300);

            $sendCloud = new \SendCloud('postmaster@hihooray.sendcloud.org', 'vhOmk1Wa2J7c');
            // 			$sendCloud = new SendCloud('postmaster@noreplyhooray.sendcloud.org', '2vSOKzhj');

            //         	$sendCloud->setDebug(true); // 设置SMTP Debug, 默认关闭
            // 		$sendCloud->setServer('smtp.qq.com',25); // 设置发送服务器，默认使用smtpcloud.sohu.com:25, 这样才会有X-SMTPAPI的功能

            // 使用SmtpApiHeader辅助类，生成X-SMTPAPI字段的json形式。
            $xSmtpApiHeader = new \SendCloud\SmtpApiHeader();
            // 设置开启取消订阅，打开跟踪，点击链接跟踪
            // 			$xSmtpApiHeader->addFilterSetting(SendCloud\AppFilter::$ADD_UNSUBSCRIBE, 'enable', '1') //取消订阅
            $xSmtpApiHeader->addFilterSetting(\SendCloud\AppFilter::$ADD_HIDDEN_IMAGE, 'enable', '1')//打开跟踪
            ->addFilterSetting(\SendCloud\AppFilter::$PROCESS_URL_REPLACE, 'enable', '1'); //点击链接跟踪

            // 设置接受者和相应的内容替换
            $recipients = array($email);
            $xSmtpApiHeader->addRecipients($recipients);
            $xSmtpApiHeader->addSubstitution('%name%', array($username))// 保证sub的替换内容和$recipients的个数相等
            ->addSubstitution('%verification_code%', array($verification_code)); // 保证sub的替换内容和$recipients的个数相等

            $message = new \SendCloud\Message();
            $message->setXsmtpApiJsonString($xSmtpApiHeader->toJsonString()); // 设置X-SMTPAPI字符串
            //$message->setXsmtpApiHeaderArray($xSmtpApiHeader->getSmtpApiHeaderArray()); // 这种效果和上面一句相同
            $message->addHeader('header_test', 'header_test_value')// 头部必须是ascii码
            ->setFromName('Hooray 好哇！')// 添加发送者称呼
            ->setFromAddress('postmaster@hihooray.sendcloud.org')// 添加发送者地址
            ->setSubject('Hooray 好哇！邮箱操作验证码')// 邮件主题
            ->setBody('
					<div style="margin: 0px auto; border: 1px solid rgb(204, 204, 204); width: 960px; background: rgb(244, 244, 244);">
        			<h3 style="height: 74px; padding: 10px;"><a href="http://www.hihooray.com" style="height: 34px; line-height: 34px; border: 0px;"><img src="http://linwo.oss-cn-hangzhou.aliyuncs.com/website/logo_top.png" style="height: 74px; width: 224px; border: 0px;" /></a></h3>
        			<div style="border: none; padding: 15px; color: rgb(51, 51, 51); font-size: 14px; background: rgb(252, 252, 252);">
        			<p>尊敬的 %name%，您好！</p>

        			<p>您的邮箱操作要求需要得到验证。</p>

        			<p>&nbsp;</p>

        			<p>您的验证码：</p>

        			<h1 style="font-size:36px;color:red;padding-left:30px;">%verification_code%</h1>

        			<p style="height: 20px; border-top-width: 1px; border-top-style: solid; border-top-color: rgb(204, 204, 204);">&nbsp;</p>

        			<p><span>请将上面的验证码输入到相应的地址进入 Hooray。</span></p>
        			</div>

        			<div style="border: 0px solid rgb(204, 204, 204); width: 960px; height:120px; background: rgb(244, 244, 244); ">
        			<p style="display:block; width: 450px; float:left;margin-left:15px;margin-top:15px;"><span>您在使用中有任何的疑问和建议，欢迎您给我们<a href="http://www.hihooray.com" style="color: rgb(6, 73, 119); text-decoration: none;">反馈意见。</a></span><br />
        			<span>联系电话：021-51707509 <br>QQ官方群：313729517 </span></p>

        			<p style="float:right;display:block;"><a href="http://www.hihooray.com"><img src="http://linwo.oss-cn-hangzhou.aliyuncs.com/website/logo_foot.png" style="height: 74px; width: 224px;" /></a></p>
        			</div>
        			</div>'); // 邮件正文html形式

            $sendCloud->send($message);
            $session['setting_email'] = [$email => $verification_code];
            return ['status' => '200'];
        } catch (Exception $e) {
            Yii::$app->response->statusCode = 400;
            return ['status' => '8005'];//邮件发送失败；
        }
    }

    /**
     * 检测输入邮箱验证码是否正确
     *
     * @access public
     * @return return_type
     */
    public function actionCheckemailcode()
    {
        if (Yii::$app->user->isGuest) {
            return ['status' => '7001']; // 用户未登录
        }
        $session = Yii::$app->session;
        $postData = Yii::$app->request->post();
        $email = trim($postData['email']);

        if ($session['setting_email'][$email] == $postData['VerificationCode'] && !empty($postData['VerificationCode'])) {
            return ['status' => '200'];
        } else {
            Yii::$app->response->statusCode = 400;
            return ['status' => '8004'];
        }
    }

    /**
     * 绑定邮箱
     */
    public function actionBindemail()
    {
        if (Yii::$app->user->isGuest) {
            return ['status' => '7001']; // 用户未登录
        }
        $session = Yii::$app->session;
        $userId = Yii::$app->user->identity->user_id;
        $postData = Yii::$app->request->post();
        $email = trim($postData['email']);
        $model = User::findOne(['user_id' => $userId]);
        if (!is_null($model)) {
            $model['email'] = $email;
            if ($model->save()) {
                $session['email'] = $email;//修改成功之后重写邮箱的SESSION
                //$StudentVerify            = new StudentVerify();
                //$StudentVerify['verify2'] = 2;
                //$StudentVerify->save();

                //完成邮箱验证赠送积分
                //$user_id    = $session['user_id'];
                //$CreditRule = new CreditRule();
                //$CreditRule->studentVerifyEmail($user_id);

                //F::addRedis('users', $user_id, array('email' => $email));
                return ['status' => '200'];
            } else {
                Yii::$app->response->statusCode = 400;
                return ['status' => '7016'];//7016绑定邮箱失败
            }
        } else {
            Yii::$app->response->statusCode = 400;
            return ['status' => '2101'];
        }
    }

    public function actionCheckmobile()
    {
        if (Yii::$app->user->isGuest) {
            return ['status' => '7001']; // 用户未登录
        }
        $postData = Yii::$app->request->post();
        $telephone = trim($postData['telephone']);
        $validateMobile = F::validateMobile($telephone);
        if ($validateMobile) {
            $model = User::findOne(['telephone' => $telephone]);
            if (!is_null($model)) {
                Yii::$app->response->statusCode = 400;
                return ['status' => '2001'];//已经注册
            } else {
                return ['status' => '200'];
            }
        } else {
            Yii::$app->response->statusCode = 400;
            return ['status' => '2002'];//手机号码输入错误
        }
    }

    public function actionBindmobile()
    {
        if (Yii::$app->user->isGuest) {
            return ['status' => '7001']; // 用户未登录
        }
        $userId = Yii::$app->user->identity->user_id;
        $postData = Yii::$app->request->post();
        $telephone = trim($postData['telephone']);
        $model = User::findOne(['user_id' => $userId]);
        if ($model) {
            $model->telephone = $telephone;

            if ($model->save()) {
                //修改手机号码写入session
                $session['telephone'] = $telephone;
                //F::addRedis('users',$session['user_id'],array('telephone'=>$telephone));
                $this->isCompleteProfile();
                return ['status' => '200'];
            } else {
                Yii::$app->response->statusCode = 400;
                return ['status' => '7011'];
            }
        } else {
            Yii::$app->response->statusCode = 400;
            return ['status' => '2101'];
        }
    }

    public function actionProfile()
    {
        if (Yii::$app->user->isGuest) {
            return ['status' => '7001']; // 用户未登录
        }
        $session = Yii::$app->session;
        $user_id = Yii::$app->user->getId();
        $postData = Yii::$app->request->post();
        $profile = trim($postData['profile']);
        $group_id = Yii::$app->user->identity->group_id;
        if ($group_id == 2) {
            $result = TeacherInfo::updateAll(['profile' => $profile], 'user_id = ' . $user_id);
        } else {
            $result = StudentInfo::updateAll(['profile' => $profile], 'user_id = ' . $user_id);
        }
        if ($result > 0) {
            $session['profile'] = $profile; // 重新设置 Session
            return ['status' => '200'];
        } else {
            Yii::$app->response->statusCode = 400;
            return ['status' => '2993']; // 更新失败
        }
    }

    /**
     * 教学特色
     * @return array|string
     * @author grg
     */
    public function actionTchfeature()
    {
        if (Yii::$app->user->isGuest) {
            return ['status' => '7001']; // 用户未登录
        }
        $session = Yii::$app->session;
        $user_id = Yii::$app->user->getId();
        $postData = Yii::$app->request->post();
        $characteristics = trim($postData['characteristics']);

        $result = TeacherInfo::updateAll(['characteristics' => $characteristics], 'user_id = ' . $user_id);
        if ($result > 0) {
            //写入redis中
            HoorayUserinfo::updateAll(['characteristics' => $characteristics], ['user_id' => $user_id]);

            $session['characteristics'] = $characteristics; // 重新设置 Session
            return ['status' => '200'];
        } else {
            Yii::$app->response->statusCode = 400;
            return ['status' => '2993']; // 更新失败
        }
    }

    /**
     * 修改头像
     */
    public function actionAvatar()
    {
        if (Yii::$app->user->isGuest) {
            return ['status' => '7001']; // 用户未登录
        }
        $session = Yii::$app->session;
        $user_id = Yii::$app->user->getId();
        $postData = Yii::$app->request->post();
        $group_id = Yii::$app->user->identity->group_id;
        if ($group_id === 2) {
            $result = TeacherInfo::updateAll(['avatar' => $postData['avatar']], 'user_id = "' . $user_id . '"');
        } else {
            $result = StudentInfo::updateAll(['avatar' => $postData['avatar']], 'user_id = "' . $user_id . '"');
        }
        if ($result > 0) {
            //头像写入redis
            HoorayUserinfo::updateAll(['avatar' => $postData['avatar']], ['user_id' => $user_id]);

            User::updateAll(['avatarstatus' => 1], 'user_id = ' . $user_id);
            $session->set('avatarstatus', 1);
            $this->isCompleteProfile();
            return ['status' => '200'];
        } else {
            Yii::$app->response->statusCode = 400;
            return ['status' => '7009'];//头像上传失败
        }
    }

    /**
     * 设置所属地区
     */
    public function actionArea()
    {
        if (Yii::$app->user->isGuest) {
            return ['status' => '7001']; // 用户未登录
        }
        $session = Yii::$app->session;
        $user_id = Yii::$app->user->getId();
        $postData = Yii::$app->request->post();
        $area_id = trim($postData['area_id']);
        $model = User::findOne(['user_id' => $user_id]);
        if ($model) {
            $model['area_id'] = $area_id;

            if ($model->save()) {
                //修改手机号码写入session
                $session['area_id'] = $area_id;
                //F::addRedis('users',$session['user_id'],array('telephone'=>$telephone));
                $this->isCompleteProfile();
                return ['status' => '200'];
            } else {
                Yii::$app->response->statusCode = 400;
                return ['status' => '7012'];
            }
        } else {
            Yii::$app->response->statusCode = 400;
            return ['status' => '2101'];
        }
    }

    /**
     * 设置学校
     */
    public function actionSchool()
    {
        if (Yii::$app->user->isGuest) {
            return ['status' => '7001']; // 用户未登录
        }
        $session = Yii::$app->session;
        $user_id = Yii::$app->user->getId();
        $postData = Yii::$app->request->post();
        $school_id = trim($postData['school_id']);
        $model = User::findOne(['user_id' => $user_id]);
        if ($model) {
            $model['school_id'] = $school_id;

            if ($model->save()) {
                //修改手机号码写入session
                $session['school_id'] = $school_id;
                //F::addRedis('users',$session['user_id'],array('telephone'=>$telephone));
                $this->isCompleteProfile();
                return ['status' => '200'];
            } else {
                Yii::$app->response->statusCode = 400;
                return ['status' => '7012'];
            }
        } else {
            Yii::$app->response->statusCode = 400;
            return ['status' => '2101'];
        }
    }

    /**
     * 设置年级
     */
    public function actionGrade()
    {
        if (Yii::$app->user->isGuest) {
            return ['status' => '7001']; // 用户未登录
        }
        $session = Yii::$app->session;
        $user_id = Yii::$app->user->getId();
        $postData = Yii::$app->request->post();
        $grade = trim($postData['grade']);
        $model = User::findOne(['user_id' => $user_id]);
        if ($model) {
            $model['grade'] = $grade;

            if ($model->save()) {
                //修改手机号码写入session
                $session['grade'] = $grade;
                //F::addRedis('users',$session['user_id'],array('telephone'=>$telephone));
                $this->isCompleteProfile();
                return ['status' => '200'];
            } else {
                Yii::$app->response->statusCode = 400;
                return ['status' => '7012'];
            }
        } else {
            Yii::$app->response->statusCode = 400;
            return ['status' => '2101'];
        }
    }

    /**
     * 完善资料,赠送哇哇豆
     * 新规则是:现在只送学生,必填项:头像 ,昵称 ,年级 ,地区 ,学校
     */
    public function isCompleteProfile()
    {
        $tasks = Yii::$app->params['tasks'];
        $reward = $tasks[0]['lists'][1]['value'];
        $userId = Yii::$app->user->getId();

        $user = User::find()->select('telephone,avatarstatus,area_id,grade,school_id')->where(['user_id' => $userId])->one();
        if ($user && !empty($user['telephone']) && $user['avatarstatus'] == 1 && !empty($user['area_id']) && !empty($user['school_id']) && $user['grade'] > 0) {
            $groupId = Yii::$app->user->identity->group_id;
            if ($groupId == 1) {
                $result = StudentInfo::findOne(['user_id' => $userId]);
                if ($result && !empty($result['nickname'])) {
                    $score = Task::findOne(['user_id' => $userId]);
                    if (null === $score) {
                        return false;
                    }
                    if ($score['once_complete'] == 0) {
                        $score['once_complete'] = $reward;
                        $score['total_scores'] += $reward;
                        $score['today_scores'] += $reward;
                        $score->save();
                        $remark = "完善资料,赠送哇哇豆.($reward)";
                        $order_type = 8;
                        $coinLog = new CoinLog();
                        $coinLog->user_id = $userId;
                        $coinLog->order_id = F::generateOrderSn('');
                        $coinLog->order_type = $order_type;
                        $coinLog->nums = $reward;
                        $coinLog->type = 1;
                        $coinLog->remark = $remark;
                        $coinLog->detail = "";
                        $coinLog->status = 2;
                        $coinLog->createtime = date("Y-m-d H:i:s");
                        $coinLog->save();

                        //new rules: free coins only student.
                        $student_count = StuCount::findOne(['user_id' => $userId]);
                        $student_count['coin'] += $reward;
                        $student_count->save();
                    }
                }
            }
        }
    }

    /**
     * update user status 0=offline,1=online,2=busy
     */
    public function actionStatus()
    {
        if (Yii::$app->request->post()) {
            $postData = Yii::$app->request->post();
            if (isset($postData['user_id']))
                $userId = $postData['user_id'];
            else
                return ['status' => '6017', 'msg' => "There's no user Id"];

            $user = User::findOne(['user_id' => $userId]);
            $user->status = isset($postData['status']) ? $postData['status'] : 0;
            if ($user->save()) {
                if ($postData['status'] == 0)
                    $status = 'offline';
                elseif ($postData['status'] == 1)
                    $status = 'online';
                elseif ($postData['status'] == 2)
                    $status = 'busy';
                Yii::$app->redis->hset("SmartEdu.redis.hooray:logs:{$userId}", "whetherOnline", $status);
                return ['status' => '200', 'msg' => '保存成功'];
            } else {
                return ['status' => '6017', 'msg' => '保存失败'];
            }
        }
    }


    /**
     * teacher Subjects 更新教学科目
     * params @teacher_subjects : "teacher_subjects":{"2":["3","4"],"3":["3"],"4":["3"]}
     */
    public function actionTeacherSubjects()
    {
        $userId = Yii::$app->user->getId();
        $groupId = Yii::$app->user->identity->group_id;
        if ($groupId !== 2) {
            return ['status' => '2040', 'msg' => Yii::$app->params['u_2040']];
        }

        if (Yii::$app->request->post()) {
            /*
             * 开课教学
             */
            $post = Yii::$app->request->post();
            if (!isset($post['teacher_subjects']) || empty($post['teacher_subjects'])) {
                return ['status' => '5001', 'msg' => Yii::$app->params['s_5001']];
            }
            $teacherSubjects = $post['teacher_subjects'];
            VerifyTeaching::deleteAll('user_id = :user_id ', [':user_id' => $userId]);
            $user = User::findIdentity($userId);
            foreach ($teacherSubjects as $key => $subjectIds) {
                $stageId = $key;
                $stageName = "";
                if ($stageId == 2) {
                    $stageName = "小学";
                } elseif ($stageId == 3) {
                    $stageName = "初中";
                } elseif ($stageId == 4) {
                    $stageName = "高中";
                }

                foreach ($subjectIds as $subjectId) {
                    $subject = Subjects::findOne(['id' => $subjectId]);
                    $teacherSubject = new VerifyTeaching();

                    $teacherSubject->user_id = $userId;
                    $teacherSubject->user_name = $user->username;
                    $teacherSubject->stages_id = $stageId;
                    $teacherSubject->stages_name = $stageName;
                    $teacherSubject->subjects_id = $subjectId;
                    $teacherSubject->subjects_name = $subject->subject_name;
                    $teacherSubject->flag = 1;//1=待审核
                    $teacherSubject->verify_time = date("Y-m-d H:i:s");
                    $teacherSubject->createtime = date("Y-m-d H:i:s");

                    if ($teacherSubject->save() == false) {
                        return ['status' => '2993', 'msg' => Yii::$app->params['u_2993']];
                    }
                }
            }
            //verify4 = 1 待审核
            TchVerify::updateAll(['verify4' => 1], 'user_id = ' . $userId);
            return ['status' => '200', 'msg' => 'successful'];
        } else {
            $teacherVerify = TchVerify::find()
                ->select('verify4')
                ->where(['user_id' => $userId])
                ->one();

            $teacherSubjects = VerifyTeaching::find()
                ->select('stages_id,stages_name,subjects_id,subjects_name')
                ->where(['user_id' => $userId])
                ->orderBy(['stages_id' => SORT_ASC])
                ->asArray()
                ->all();

            $teacherSubjectsGroup = array('elementarySchool'=> array());
            if ($teacherSubjects) {
                foreach ($teacherSubjects as $teacherSubject) {
                    if ($teacherSubject["stages_id"] == 2) {
                        $teacherSubjectsGroup['elementarySchool'][] = $teacherSubject;
                    } elseif ($teacherSubject["stages_id"] == 3) {
                        $teacherSubjectsGroup['middleSchool'][] = $teacherSubject;
                    } elseif ($teacherSubject["stages_id"] == 4) {
                        $teacherSubjectsGroup['highSchool'][] = $teacherSubject;
                    }
                }
            }
            return array("is_check_teacher_subject" => $teacherVerify->verify4, 'teacherSubjects' => $teacherSubjectsGroup); //1待审核,2审核通过
        }
    }

    public function actionApplyverify()
    {
        $userId  = Yii::$app->user->identity->user_id;
        $groupId = Yii::$app->user->identity->group_id;
        if ($groupId !== 2) {
            return ['status' => '2040', 'msg' => Yii::$app->params['u_2040']];
        }
        $affected = TchVerify::updateAll(['apply_status' => 1], ['user_id' => $userId, 'apply_status' => 0]);
        if ($affected === 0 && null === TchVerify::findOne(['user_id' => $userId])) {
            $tchVerify               = new TchVerify();
            $tchVerify->user_id      = $userId;
            $tchVerify->apply_status = 1;
            $tchVerify->save();
        }
        return ['status' => '200'];
    }

    public function actionVerifyTeaching()
    {
        $user_id = Yii::$app->user->identity->user_id;
        $group_id = Yii::$app->user->identity->group_id;
        if ($group_id !== 2) {
            return ['status' => '2040', 'msg' => Yii::$app->params['u_2040']];
        }
        $rows = VerifyTeaching::find()
            ->select([
                "group_concat(DISTINCT subjects_id, '|', subjects_name order by subjects_id) subject",
                "group_concat(DISTINCT stages_id, '|', stages_name order by stages_id) stage"
            ])
            ->where(['user_id' => $user_id])
            ->groupBy('subjects_id')
            ->asArray()
            ->all();
        if (empty($rows)) {
            return ['status' => '2041', 'msg' => Yii::$app->params['u_2041']];
        }
        foreach ($rows as &$row) {
            list($subject_id, $subject_name) = explode('|', $row['subject']);
            $row['subject'] = compact('subject_id', 'subject_name');
            $stages = explode(',', $row['stage']);
            foreach ($stages as &$stage) {
                list($stage_id, $stage_name) = explode('|', $stage);
                $stage = compact('stage_id', 'stage_name');
            }
            if (count($stages) > 1) {
                $row['stage'] = [['stage_id' => '-1', 'stage_name' => '全部']];
                $row['stage'] = array_merge($row['stage'], $stages);
            } else {
                $row['stage'] = $stages;
            }
        }
        if (count($rows) > 1) {
            $list = VerifyTeaching::find()
                ->select(['stages_id stage_id', 'stages_name stage_name'])
                ->where(['user_id' => $user_id])
                ->groupBy('stages_id')
                ->orderBy('stages_id')
                ->asArray()
                ->all();
            $item = [];
            $item['subject'] = ['subject_id' => '-1', 'subject_name' => '全部'];
            if (count($list) > 1) {
                $item['stage'] = [['stage_id' => '-1', 'stage_name' => '全部']];
                $item['stage'] = array_merge($item['stage'], $list);
            } else {
                $item['stage'] = $list;
            }
            $data = array_merge([$item], $rows);
        } else {
            $data = $rows;
        }
        return $data;
    }

    public function actionUpdateTeacherInfo()
    {
        $postData = Yii::$app->request->post();
        $tags = isset($postData['tags']) ? $postData['tags'] : '';
        $teachingAge = isset($postData['teaching_age']) ? $postData['teaching_age'] : '';
        $userId = Yii::$app->user->identity->user_id;
        $groupId = Yii::$app->user->identity->group_id;
        if ($groupId !== 2) {
            return ['status' => '2048', 'msg' => Yii::$app->params['u_2048']];
        }
        $teachingAge = (int)$teachingAge;
        if ($tags !== '') {
            $tags = explode(';', $tags);
            $tags = json_encode($tags, JSON_UNESCAPED_UNICODE);
        }
        TeacherInfo::updateAll(['tags' => $tags, 'teaching_age' => $teachingAge], ['user_id' => $userId]);
        return ['status' => '200'];
    }

    /**
     * check token
     * params accessToken
     */
    public function actionCheckToken()
    {
        $data = array(
            'access_token' => '',
            'expires' => '',
            'isExpire' => true
        );

        if (Yii::$app->request && Yii::$app->request->get("access-token")) {
            $accessToken = AccessTokens::findOne(Yii::$app->request->get("access-token"));
            if ($accessToken) {
                if (time() > strtotime($accessToken->expires)) {
                    $isExpire = true;
                } else {
                    $isExpire = false;
                }

                $data = array(
                    'access_token' => $accessToken->access_token,
                    'expires' => $accessToken->expires,
                    'isExpire' => $isExpire
                );
            }
        }

        return $data;
    }

    public function actionInvites()
    {
        $uid = Yii::$app->user->identity->user_id;
        return Invites::find()->select('user_id,username,progress,created_at')->where([
            'inviter_id' => $uid,
            'progress' => 1
        ])->asArray()->all();
    }

    public function actionInviteBonus()
    {
        $uid  = Yii::$app->user->identity->user_id;
        $data = InviterBonus::find()
                            ->from(InviterBonus::tableName() . ' b')
                            ->select('count(i.id) num,bonus')
                            ->leftJoin(Invites::tableName() . ' i', [
                                'AND',
                                'b.inviter_id = i.inviter_id',
                                'i.progress = 1'
                            ])
                            ->where(['b.inviter_id' => $uid])
                            ->asArray()->groupBy('b.inviter_id')
                            ->one();
        if (null === $data) {
            $bonus = [];
            foreach ([1, 3, 5, 10] as $num) {
                $bonus[] = [
                    'num' => $num,
                    'reach' => 0,
                    'bonus' => 0
                ];
            }
            return $bonus;
        }
        $bonus = json_decode($data['bonus'], true);
        foreach ($bonus as $num => &$flag) {
            $flag = [
                'num' => $num,
                'reach' => $data['num'] >= (int)$num ? 1 : 0,
                'bonus' => $flag
            ];
        }
        return array_values($bonus);
    }
}
