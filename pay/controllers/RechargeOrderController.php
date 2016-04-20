<?php

namespace pay\controllers;

use Yii;
use common\models\RechargeOrder;
use common\models\StuCount;
use common\models\TchCount;
use pay\models\RechargeOrder as RechargeOrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\Pagination;

/**
 * RechargeOrderController implements the CRUD actions for RechargeOrder model.
 */
class RechargeOrderController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all RechargeOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RechargeOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $userId = Yii::$app->user->getId();
        $groupId = Yii::$app->user->identity->group_id;

        if ($groupId == 2) {
            $userCount = TchCount::findOne($userId);
        } else {
            $userCount = StuCount::findOne($userId);
        }

        return $this->render('index', [
            'rechargeOrders' => $dataProvider->getModels(),
            'pagination' => $dataProvider->getPagination(),
            'userCount' => $userCount,
        ]);
    }

    /**
     * Finds the RechargeOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RechargeOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RechargeOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
