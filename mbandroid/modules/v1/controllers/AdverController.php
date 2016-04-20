<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 10/25/15
 * Time: 2:00 PM
 */
namespace mbandroid\modules\v1\controllers;

use common\models;
use common\models\Advertisements;
use yii;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;

class  AdverController extends ActiveController
{
    public $modelClass = 'common\models\Advertisements';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'except' => ['index', 'create'],  // set actions for disable access!
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
                'create' => ['post'],
            ],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['Index'],
            'rules' => [
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
     * 广告接口
     * @return array
     */

    /* 获取广告图片 */
    public function actionIndex()
    {
        $query = Advertisements::find();
        $query->select('*');
        $query->where(['block' => 2]);
        $Advertisements = $query->asArray()->all();
        $data = [];
        foreach ($Advertisements as $key => $adver) {
            // id默认从1开始
            $data[$key]['id'] = $key + 1;
            $data[$key]['title'] = $adver['title'];
            $data[$key]['image'] = $adver['image_url'];
            $data[$key]['target'] = $adver['position'];
            $data[$key]['click'] = $adver['is_click'];
        }
        return $data;
    }

    /**首页广告
     *
     * @return array
     */

    /*  查看首页广告 */
    public function actionCreate()
    {
        $postData = Yii::$app->request->post();
        $block = isset($postData['block']) ? $postData['block'] : 'index';
        $device = isset($postData['device']) ? $postData['device'] : 'android';
        $advertisements = Advertisements::find()->where(['like', 'block', $block])->andFilterWhere(['device' => $device])->asArray()->all();
        $fillArray = [];
        foreach ($advertisements as $val) {
            $fillArray[] = $val['block'];
        }
        $blockValue = array_flip(array_values(array_unique($fillArray)));
        $data = [];
        foreach ($blockValue as $key => $val) {
            $data[$key] = [];
        }
        foreach ($advertisements as $key => $val) {
            if (in_array($val['block'], $blockValue)) {
                array_push($data[$val['block']], $advertisements[$key]);
            }
        }
        return $data;
    }
}
