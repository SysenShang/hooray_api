<?php
/**
 * Created by PhpStorm.
 * User: chenwei
 * Date: 10/30/15
 * Time: 2:00 PM
 */
namespace mbandroid\modules\v1\controllers;

use yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use common\models;
use common\models\Advertisements;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class  TopAdverController extends ActiveController
{
    public $modelClass = 'common\models\Advertisement';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'except' => ['index'],  // set actions for disable access!
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
                'Index' => ['get', 'post'],
            ],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['Index'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['Index'],
                    'roles' => ['@'],
                ],
                [
                    'allow' => true,
                    'actions' => ['Index'],
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
     * 主页广告接口
     * @return array
     */

    /* 获取广告图片 */
    public function actionIndex()
    {
        $a_list = Advertisements::find()->where(['in', 'id', [10, 11, 12, 13, 14, 19]])->all();
        $info = [];
        $data['id'] = $a_list[0]['id'];
        $data['title'] = '首页';
        $data['image'] = $a_list[0]['image_url'];
        $data['android'] = $a_list[1]['image_url'];
        $data['pad'] = $a_list[4]['image_url'];
        $data['iphone'] = $a_list[2]['image_url'];
        $data['ipad'] = $a_list[3]['image_url'];
        $data['ipad_stand'] = $a_list[5]['image_url'];
        $data['sort'] = $a_list[0]['position'];
        $info[] = $data;
        return $info;
    }
}

?>
