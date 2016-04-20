<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 10/13/15
 * Time: 5:57 PM
 */

namespace mbandroid\modules\v1\controllers;

use common\components\Emay;
use common\models\User;
use common\models\F;
use yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class BackController extends ActiveController
{
    public $modelClass = 'common\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'except' => ['index', 'create', 'sms'],  // set actions for disable access!
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
     * 发送短信修改密码
     * @return mixed
     */
    public function actionSms()
    {
        $userAgent = Yii::$app->request->userAgent;
        $postData = Yii::$app->request->post();
        $telephone = $postData['telephone'];
        $allow_teacher = isset($postData['allow_teacher']) ? $postData['allow_teacher'] : 'on';
        $allow_student = isset($postData['allow_student']) ? $postData['allow_student'] : 'on';

        $user = User::findOne(['telephone' => $telephone]);

        if ($user) {
            if ($user->group_id == 1 && $allow_student == 'off') {
                return ['status' => '2045', 'msg' => Yii::$app->params['u_2045']];
            }
            if ($user->group_id == 2 && $allow_teacher == 'off') {
                return ['status' => '2046', 'msg' => Yii::$app->params['u_2046']];
            }

            $verification_code = F::randCode(6, 1);

            $session = Yii::$app->session;
            $session["_".$telephone] = $verification_code;
            $emay = new Emay();
            $smsStatus = $emay->sendBackSMS($telephone, $verification_code);

            if ($smsStatus != null && $smsStatus == '0') {
                $session = Yii::$app->session;
                $session[$telephone] = $verification_code;
                $data['user_id'] = $user->user_id;
            } else {
                $data['status'] = '2003';
                $data['msg'] = Yii::$app->params['u_2003'];
                return $data;
            }
        } else {
            $data['status'] = '2011';
            $data['msg'] = Yii::$app->params['u_2011'];
        }

        return $data;
    }

    /**
     * 更改用户密码
     * @return string
     * @throws yii\base\Exception
     * @throws yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        $postData = Yii::$app->request->post();
        $ver_code = $postData['ver_code'];
        $password = $postData['password'];
        $user_id = $postData['user_id'];
        $telephone = $postData['telephone'];
        $pwsafety = $postData['pwsafety'];

        $session = Yii::$app->session;

        $hash = Yii::$app->getSecurity()->generatePasswordHash($password);
        if ($session['_'.$telephone] == $ver_code) {
            $rt =  User::updateAll(['upassword'=>$hash,"pwsafety"=>$pwsafety],['user_id'=>$user_id]);
            if($rt){
                $data = "";
            }else{
                $data['status'] = '2004';
                $data['msg'] = Yii::$app->params['u_2004'];
            }
        }else{
            $data['status'] = '2004';
            $data['msg'] = Yii::$app->params['u_2004'];
        }
        return $data;
    }

}
