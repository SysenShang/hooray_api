<?php

namespace mbandroid\modules\v1\controllers;

use common\components\CTask;
use common\models\CoinLog;
use common\models\Task;
use yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\AccessControl;

class TaskController extends ActiveController
{
    public $modelClass = 'common\models\Task';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors                  = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        $behaviors['access']        = [
            'class' => AccessControl::className(),
            'only' => ['index'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index'],
                    'roles' => ['@'],
                ],
                // everything else is denied
            ],
            'denyCallback' => function () {
                throw new Exception('您无权访问该页面');
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
        $uid   = Yii::$app->user->getId();
        $score = Task::find()->where(['user_id' => $uid])->asArray()->limit(1)->one();
        if (null === $score) {
            $group_id = Yii::$app->user->identity->group_id;
            $CoinLog  = new CoinLog();
            $CoinLog->registerTask($uid, $group_id);
            $score = Task::find()->where(['user_id' => $uid])->asArray()->limit(1)->one();
        }
        $score['tasks'] = $this->task($uid);
        $today          = date('Y-m-d');
        if (substr($score['lastdate_checkin'], 0, 10) === $today) {
            $score['everyday_checkin'] = 5;
        } else {
            $score['everyday_checkin'] = 0;
        }
        if (substr($score['lastdate_ask'], 0, 10) === $today) {
            $score['everyday_ask'] = 5;
        } else {
            $score['everyday_ask'] = 0;
        }
        if (substr($score['lastdate_share'], 0, 10) !== $today) {
            $score['everyday_share'] = 0;
        } elseif ($score['everyday_share'] === 5) {
            $score['everyday_share'] = 1;
        }
        if (substr($score['lastdate_weike'], 0, 10) !== $today) {
            $score['everyday_weike'] = 0;
        } elseif ($score['everyday_weike'] === 5) {
            $score['everyday_weike'] = 3;
        }
        if (substr($score['updated_at'], 0, 10) !== $today) {
            $score['today_scores'] = 0;
        }
        $tasks  = Yii::$app->params['tasks'];

        $score['once_register'] = $tasks[0]['lists'][0]['value'];
        $score['once_complete'] = $tasks[0]['lists'][1]['value'];
        $score['serial_ask'] .= '/' . Yii::$app->params['task']['serial_ask_target'];
        $score['serial_checkin'] .= '/' . $tasks[1]['lists'][6]['target'];
        $score['everyday_weike'] .= '/' . Yii::$app->params['task']['everyday_weike_target'];
        $score['everyday_share'] .= '/' . Yii::$app->params['task']['everyday_share_target'];
        return $score;
    }

    private function task($uid)
    {
        $tasks = Yii::$app->params['tasks'];
        $uTask = Task::find()->where(['user_id' => $uid])->asArray()->limit(1)->one();
        if (null !== $uTask) {
            if ((int)$uTask['once_register'] > 0 && (int)$uTask['once_complete'] > 0) {
                unset($tasks[0]);
            } elseif ((int)$uTask['once_register'] > 0) {
                $tasks[0]['lists'][0]['completed'] = '已完成';
            } elseif ((int)$uTask['once_complete'] > 0) {
                $tasks[0]['lists'][1]['completed'] = '已完成';
            }
            //处理每日任务
            $today = date('Y-m-d');
            $items = [
                'lastdate_checkin' => [1, 0],
                'lastdate_ask' => [1, 1],
                'lastdate_judge' => [1, 2],
                'lastdate_share' => [1, 3],
                'lastdate_buyweike' => [1, 4],
                'lastdate_judgeweike' => [1, 5],
            ];
            foreach ($items as $key => $item) {
                $_key = str_replace('lastdate', 'everyday', $key);
                if (substr($uTask[$key], 0, 10) === $today) {
                    if (array_key_exists('target', $tasks[$item[0]]['lists'][$item[1]]) && $uTask[$_key] < $tasks[$item[0]]['lists'][$item[1]]['target']) {
                        $tasks[$item[0]]['lists'][$item[1]]['completed'] = $uTask[$_key] . '/' . $tasks[$item[0]]['lists'][$item[1]]['target'];
                    } else {
                        $tasks[$item[0]]['lists'][$item[1]]['completed'] = '已完成';
                    }
                }
            }
            //处理连续任务
            $items = [
                'serial_checkin' => [1, 6],
            ];
            if (substr($uTask['lastdate_checkin'], 0, 10) < date('Y-m-d', strtotime('last day'))) {
                $uTask['serial_checkin'] = 0;
            }
            foreach ($items as $key => $item) {
                if (array_key_exists('target', $tasks[$item[0]]['lists'][$item[1]]) && $uTask[$key] < $tasks[$item[0]]['lists'][$item[1]]['target']) {
                    $tasks[$item[0]]['lists'][$item[1]]['completed'] = $uTask[$key] . '/' . $tasks[$item[0]]['lists'][$item[1]]['target'];
                } else {
                    $tasks[$item[0]]['lists'][$item[1]]['completed'] = '已完成';
                }
            }
        }
        return array_values($tasks);
    }

    /**
     * 做任务
     * type array register|complete|checkin|share|weike
     * @author grg
     */
    public function actionDo()
    {
        $postData = Yii::$app->request->post();
        if (!array_key_exists('type', $postData)) {
            Yii::$app->response->statusCode = 400;
            return Yii::$app->response->isNotFound;
        }
        $uid      = Yii::$app->user->getId();
        $group_id = Yii::$app->user->identity->group_id;
        if (in_array($postData['type'], ['checkin', 'share'], true)) {
            CTask::done($uid, $group_id, $postData['type']);
        }
        return ['status' => '200'];
    }
}
