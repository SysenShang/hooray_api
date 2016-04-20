<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 7/23/15
 * Time: 9:59 AM
 */
namespace mbandroid\controllers;

use common\components\NeteaseIm;
use common\components\RedisStorage;
use common\models\HoorayUser;
use common\models\LoginForm;
use common\models\User;
use common\models\UsersLoginHistory;
use filsh\yii2\oauth2server\Module;
use yii;
use yii\rest\ActiveController;

class OauthController extends ActiveController
{
    public function init()
    {
        $this->modelClass = 'common/models/User';
        parent::init();
    }

    public function actionToken()
    {
        $postData = Yii::$app->request->post();
        $session = Yii::$app->session;
        if ($postData) {
            $allow_teacher = isset($postData['allow_teacher']) ? $postData['allow_teacher'] : 'on';
            $allow_student = isset($postData['allow_student']) ? $postData['allow_student'] : 'on';
            $grant_type = $postData['grant_type'];
            if ($grant_type === 'password') {
                unset($session['__access_token']);
                $phone_parttern = '/^1(?:3[\d]|5[\d]|8[\d]|7[01678]|4[57])(-?)\d{4}\1\d{4}$/';
                if (array_key_exists('username', $postData) &&
                    null !== $postData['username'] &&
                    preg_match($phone_parttern, $postData['username'])
                ) {
                    $postData['telephone'] = $postData['username'];
                }
                if (array_key_exists('telephone', $postData) && null !== $postData['telephone']) {
                    $user = User::find()
                        ->select('username')
                        ->where([
                            'user_source' => 0,
                            'telephone' => trim($postData['telephone']),
                            'xstatus' => 1
                        ])
                        ->asArray()
                        ->all();
                    if (is_array($user) && count($user) === 1) {
                        $postData['username'] = $user[0]['username'];
                        if (strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
                            $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
                            $content = file_get_contents('php://input');
                            $_POST = json_decode($content, true);
                            $_POST['username'] = $postData['username'];
                        }
                    } else {
                        $data['status'] = '2019';
                        $data['msg'] = Yii::$app->params['u_2019'];
                        return $data;
                    }
                }
                $module = Module::getInstance();
                $userFrom = User::findOne(['xstatus' => 1, 'user_source' => 0, 'username' => trim($postData['username'])]);
                if (null !== $userFrom) {
                    if ($userFrom->xtype) {
                        return ['status' => '2018', 'msg' => Yii::$app->params['u_2018']];
                    }
                    $group_id = $userFrom->group_id;
                    if ($group_id == 1 && $allow_student == 'off') {
                        return ['status' => '2036', 'msg' => Yii::$app->params['u_2036']];
                    }
                    if ($group_id == 2 && $allow_teacher == 'off') {
                        return ['status' => '2037', 'msg' => Yii::$app->params['u_2037']];
                    }
                } else {
                    return ['status' => '2008', 'msg' => Yii::$app->params['u_2008']];
                }
                $model = new LoginForm();
                $post = ['LoginForm' => $postData];
                if ($model->load($post) && $model->login()) {
                    $user_id = Yii::$app->user->getId();
                    $lastLogin = $this->loginRecord($user_id);
                    $group_id = Yii::$app->user->identity->group_id;
                    User::updateUserStatus($user_id, 1);
                    $redis_user = HoorayUser::findOne(['user_id' => $user_id]);
                    if ($redis_user) {
                        HoorayUser::updateAll(['status' => 1], ['user_id' => $user_id]);//更新redis
                    } else {
                        RedisStorage::user($user_id);
                        RedisStorage::userinfo($user_id, $group_id);
                        RedisStorage::userlog($user_id, $group_id);
                        RedisStorage::usercount($user_id, $group_id);
                    }
                } else {
                    return ['status' => '2017', 'msg' => Yii::$app->params['u_2017']];
                }
                $response = $module->getServer()->handleTokenRequest();
                if ($response->getStatusCode() !== 200) {
                    return ['status' => '400', 'msg' => 'Bad Request'];
                }
                $responseData = $response->getParameters();
                $responseData['user_id'] = $user_id;
                $responseData['last_login'] = $lastLogin;
                unset($responseData['token_type'], $responseData['scope']);
                $session['__access_token'] = $responseData['access_token'];
                $session['__expires_in'] = $responseData['expires_in'];
                $session['__refresh_token'] = $responseData['refresh_token'];
                $session['__last_login'] = $lastLogin;
            } elseif ($grant_type === 'qq') {
                return ['status' => '2999', 'msg' => Yii::$app->params['u_2999']];
//                if (Yii::$app->user->isGuest) {
//                    //todo 需要验证 access_token (指来自 qq 的 access_token)
//                    unset($session['__access_token']);
//                    $client_id = $postData['client_id'];
//                    $scope = $postData['scope'];
//                    $user_id = $postData['user_id'];
//                    $user = User::findOne(['xstatus' => 1, 'user_source' => 0, 'user_id' => $user_id]);
//                    if (empty($user)) {
//                        return ['status' => '2008', 'msg' => Yii::$app->params['u_2008']];
//                    }
//                    if ($user->xtype != 1) {
//                        return ['status' => '2038', 'msg' => Yii::$app->params['u_2038']];
//                    }
//                    $group_id = $user->group_id;
//                    if ($group_id == 1 && $allow_student == 'off') {
//                        return ['status' => '2036', 'msg' => Yii::$app->params['u_2036']];
//                    }
//                    if ($group_id == 2 && $allow_teacher == 'off') {
//                        return ['status' => '2037', 'msg' => Yii::$app->params['u_2037']];
//                    }
//                    Yii::$app->user->login($user, 86400);
//                    $lastLogin = $this->loginRecord($user_id);
//                    User::updateAll(['status' => 1], ['user_id' => $user_id]);
//                    $module = Module::getInstance();
//                    $responseData = $module->getServer()->createAccessToken($client_id, $user_id, $scope);
//                    $responseData['user_id'] = $user_id;
//                    unset($responseData['token_type'], $responseData['scope']);
//                    $session['__id'] = $user_id;
//                    $session['__access_token'] = $responseData['access_token'];
//                    $session['__expires_in'] = $responseData['expires_in'];
//                    $session['__refresh_token'] = $responseData['refresh_token'];
//                    $session['__last_login'] = $lastLogin;
//                } else {
//                    $responseData['user_id'] = $session['__id'];
//                    $responseData['access_token'] = $session['__access_token'];
//                    $responseData['expires_in'] = $session['__expires_in'];
//                    $responseData['refresh_token'] = $session['__refresh_token'];
//                    $responseData['last_login'] = $session['__last_login'];
//                }
            } elseif ($grant_type === 'weixin') {
                return ['status' => '2999', 'msg' => Yii::$app->params['u_2999']];
//                if (Yii::$app->user->isGuest) {
//                    //todo 需要验证 access_token (指来自 weixin 的 access_token)
//                    unset($session['__access_token']);
//                    $client_id = $postData['client_id'];
//                    $scope = $postData['scope'];
//                    $user_id = $postData['user_id'];
//                    $user = User::findOne(['xstatus' => 1, 'user_source' => 0, 'user_id' => $user_id]);
//                    if (empty($user)) {
//                        return ['status' => '2008', 'msg' => Yii::$app->params['u_2008']];
//                    }
//                    if ($user->xtype != 2) {
//                        return ['status' => '2039', 'msg' => Yii::$app->params['u_2039']];
//                    }
//                    $group_id = $user->group_id;
//                    if ($group_id == 1 && $allow_student == 'off') {
//                        return ['status' => '2036', 'msg' => Yii::$app->params['u_2036']];
//                    }
//                    if ($group_id == 2 && $allow_teacher == 'off') {
//                        return ['status' => '2037', 'msg' => Yii::$app->params['u_2037']];
//                    }
//                    Yii::$app->user->login($user, 86400);
//                    $lastLogin = $this->loginRecord($user_id);
//                    User::updateAll(['status' => 1], ['user_id' => $user_id]);
//                    $module = Module::getInstance();
//                    $responseData = $module->getServer()->createAccessToken($client_id, $user_id, $scope);
//                    $responseData['user_id'] = $user_id;
//                    unset($responseData['token_type'], $responseData['scope']);
//                    $session['__id'] = $user_id;
//                    $session['__access_token'] = $responseData['access_token'];
//                    $session['__expires_in'] = $responseData['expires_in'];
//                    $session['__refresh_token'] = $responseData['refresh_token'];
//                    $session['__last_login'] = $lastLogin;
//                } else {
//                    $responseData['user_id'] = $session['__id'];
//                    $responseData['access_token'] = $session['__access_token'];
//                    $responseData['expires_in'] = $session['__expires_in'];
//                    $responseData['refresh_token'] = $session['__refresh_token'];
//                    $responseData['last_login'] = $session['__last_login'];
//                }
            } else {
                return ['status' => '5001', 'msg' => Yii::$app->params['s_5001']];
            }
        } else {
            $responseData['user_id'] = isset($session['__id']) ? $session['__id'] : '';
            if($session['__id']){
                $this->loginRecord($session['__id']);
            }
            $responseData['access_token'] = isset($session['__access_token']) ? $session['__access_token'] : '';
            $responseData['expires_in'] = isset($session['__expires_in']) ? $session['__expires_in'] : '';
            $responseData['refresh_token'] = isset($session['__refresh_token']) ? $session['__refresh_token'] : '';
            $responseData['last_login'] = isset($session['__last_login']) ? $session['__last_login'] : '';
        }
        $responseData['grade'] = '';
        $responseData['netease_im'] = ['accid' => (string)$responseData['user_id'], 'token' => ''];
        if (array_key_exists('user_id', $responseData) && '' !== (string)$responseData['user_id']) {
            $user = User::find()->select('username,grade')->where([
                'user_id' => trim($responseData['user_id']),
                'user_source' => 0,
                'xstatus' => 1
            ])->limit(1)->asArray()->one();
            if (null !== $user && '' !== (string)$responseData['access_token']) {
                $responseData['grade'] = (string)$user['grade'];
                $responseData['netease_im']['token'] = NeteaseIm::getToken($responseData['user_id'], $user['username']);
            }
        }
        return $responseData;
    }

    private function loginRecord($user_id)
    {
        $lastLogin = UsersLoginHistory::find()
            ->select('login_at')
            ->where(['user_id' => $user_id])
            ->orderBy('login_at DESC')
            ->limit(1)
            ->asArray()
            ->one();

        $history = new UsersLoginHistory();
        $history->user_id = $user_id;
        $history->ip = Yii::$app->request->getUserIP();
        $history->ua = Yii::$app->request->getUserAgent();
        $history->save();
        return null === $lastLogin ? '' : $lastLogin['login_at'];
    }
}
