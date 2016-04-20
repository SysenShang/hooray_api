<?php

namespace mbandroid\modules\v1\controllers;

use common\models\Area;
use common\models\School;
use yii\rest\ActiveController;
use yii;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\AccessControl;


class SchoolController extends ActiveController
{
    public $modelClass = 'common\models\School';

    public function behaviors()
    {
        $behaviors                  = parent::behaviors();
        $behaviors['authenticator'] = [
            'except' => ['index'],  // set actions for disable access!
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        $behaviors['access']        = [
            'class' => AccessControl::className(),
            'only' => ['Create'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['Create'],
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

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSearch()
    {
        $postData = Yii::$app->request->post();
        if (isset($postData['grade']) && !isset($postData['level'])) {
            switch(true) {
                case 1 <= $postData['grade'] && $postData['grade'] <= 6:
                    $postData['level'] = 1;
                    break;
                case 7 <= $postData['grade'] && $postData['grade'] <= 9:
                    $postData['level'] = 2;
                    break;
                case 10 <= $postData['grade'] && $postData['grade'] <= 12:
                    $postData['level'] = 3;
                    break;
            }
        }
        if (!isset($postData['level'])) {
            $postData['level'] = [1, 2, 3];
        }
        if (!isset($postData['area_id'])) {
            $postData['area_id'] = '11';//默认浙江
        }
        $area_ids = [$postData['area_id']];
        $areas    = Area::find()->select('id,level')->where(['parent_id' => $postData['area_id']])->asArray()->all();
        while ($areas) {
            $ids      = array_column($areas, 'id');
            $area_ids = array_merge($area_ids, $ids);
            $areas    = Area::find()->select('id,level')->where(['parent_id' => implode(',', $ids)])->asArray()->all();
        }
        return School::find()->select('id,name')->where([
            'like',
            'name',
            $postData['key']
        ])->andWhere(['level' => $postData['level']])->andWhere(['area_id' => $area_ids])->limit(500)->asArray()->all();
    }

    public function actionLists()
    {
        $query      = School::find()->select('id,name,created_at')->where([
            'status' => [0, 1],
        ]);
        $countQuery = clone $query;
        $pages      = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 50]);
        $schools      = $query->offset($pages->offset)->limit($pages->limit)->all();
        if ($pages->pageCount > 0) {
            $data['list'] = $schools;
        } else {
            $data['list'] = [];
        }
        $data['total_page'] = $pages->pageCount;
        return $data;
    }

    public function actionAdd()
    {
        //
    }
}
