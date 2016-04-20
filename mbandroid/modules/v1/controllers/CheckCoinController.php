<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 10/22/15
 * Time: 10:52 AM
 */

namespace mbandroid\modules\v1\controllers;

use common\models\StuCount;
use common\models\TchCount;
use common\models\CoinLog;
use yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\db\Query;

class CheckCoinController extends ActiveController
{
    public $modelClass = 'common\models\StuCount';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'except' => ['default-coins'],
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'except' => ['default-coins'],
            'rules' => [
                // allow authenticated users
                [
                    'allow' => true,
                    'actions' => [
                        'index',
                    ],
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
     * 查询哇哇豆
     * @isQueryIncome 是否查询收
     * @createtime 查询收入日期   */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $groupId = Yii::$app->user->identity->group_id;
        $getData= Yii::$app->request->get();
        $isQueryIncome = isset($getData["isQueryIncome"]) ? $getData["isQueryIncome"] : null;
        $createtime = isset($getData["createtime"]) ? $getData["createtime"] : null;

        if ($isQueryIncome) {
            $queryRefund   = new Query;
            $queryRefund->from(CoinLog::tableName());
            $queryRefund->select("nums");
            $queryRefund->where(['user_id' => $userId, 'status' => 2, 'type' => 0, 'order_type' => [7, 11]]);
            $data['coinRefund'] = $queryRefund->sum('nums');

            $query   = new Query;
            $query->from(CoinLog::tableName());
            $query->select("nums");
            $query->where(['user_id' => $userId, 'status' => 2, 'type' => 1, 'order_type' => [7, 11]]);
            $data['coin'] = $query->sum('nums')- $data['coinRefund'];
            if ($data['coin'] < 0) {
                $data['coin'] = 0;
            }
            if ($createtime) {
                $query->andWhere("createtime >= '{$createtime}' ");
                $queryRefund->andWhere("createtime >= '{$createtime}' ");
                $data['currentMonthCoin'] = $query->sum('nums') - $queryRefund->sum('nums');
                if ($data['currentMonthCoin'] < 0) {
                    $data['currentMonthCoin'] = 0;
                }
            }
        } else {
            if ($groupId == 1) {
                $stu_count = StuCount::findOne(['user_id' => $userId]);
                $data['coin'] = "$stu_count->coin";
            } else {
                $tch_count = TchCount::findOne(['user_id' => $userId]);
                $data['coin'] = "$tch_count->coin";
            }
        }

        return $data;
    }

    /**
     * 控制即时问  指定老师  微课 1V1辅导 最低哇哇豆
     */
    public function actionDefaultCoins()
    {
        $postData = Yii::$app->request->post();
        $module = isset($postData['module']) ? $postData['module'] : "";

        return array(
            'defaultCoins' => isset(Yii::$app->params['defaultCoins'][$module]) ? Yii::$app->params['defaultCoins'][$module] : 0,
            'currentModule' =>$module
        );
    }

}
