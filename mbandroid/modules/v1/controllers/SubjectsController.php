<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 7/28/15
 * Time: 8:00 PM
 */
namespace mbandroid\modules\v1\controllers;


use common\models\Stgsubjects;
use common\models\Subject;
use common\models\Subjects;
use yii;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;


class SubjectsController extends ActiveController
{
    public $modelClass = 'common\models\Subjects';

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'authenticator' => [
                    'class' => CompositeAuth::className(),
                    'except' => ['Index', 'Create', 'grades', 'stages'],  // set actions for disable access!
                    'authMethods' => [
                        HttpBasicAuth::className(),
                        HttpBearerAuth::className(),
                        QueryParamAuth::className(),
                    ]
                ],
                'access' => [
                    'class' => AccessControl::className(),
                    'only' => ['Index', 'Create', 'Stages'],
                    'rules' => [
                        // allow authenticated users
                        [
                            'allow' => true,
                            'actions' => ['Index', 'Create', 'Stages', 'Grades'],
                            'roles' => ['@'],
                        ],
                        // everything else is denied
                    ],
                    'denyCallback' => function () {
                        throw new \Exception('您无权访问该页面');
                    },
                ],
            ]
        );
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);

        return $actions;
    }

    /**
     * 获取科目列表
     * @return array
     */
    public function actionIndex()
    {
        $query = Subjects::find()->all();
        $data = [];
        foreach ($query as $sub) {
            $data[] = $sub->subject_name;
        }
        return $data;
    }

    /**
     * 获取阶段列表
     * @return array
     */
    public function actionCreate()
    {
        $postData = Yii::$app->request->post();
        $subect_name = $postData['subject'];
        $subject = Subject::find()->where(['in', 'fid', [1, 2, 3]])->all();
        $data = [];
        foreach ($subject as $val) {
            if ($val->subject_name == $subect_name) {
                $stage_id[$val->fid] = $val->fid;
                if ($val->fid == 1) {
                    $data[] = '小学';
                } elseif ($val->fid == 2) {
                    $data[] = '初中';
                } elseif ($val->fid == 3) {
                    $data[] = '高中';
                } else {
                    $data[] = '其他';
                };
            }
        }
        return $data;
    }

    /**
     * 获取联动微课集的年级科目列表
     * @return array
     */
    public function actionStages()
    {
        $rows = Stgsubjects::find()
            ->select([
                "group_concat(DISTINCT stages_id, '|', stages_name order by stages_id) stage",
                "group_concat(DISTINCT subjects_id, '|', subjects_name order by subjects_id) subject",
            ])
            ->where('grades_id != 0 and subjects_id != 13 and enable = 1 and xtype = 1')
            ->groupBy('stages_id')
            ->orderBy('stages_id')
            ->asArray()
            ->all();
        if (count($rows) === 0) {
            return [];
        }
        $data = [];
        if (count($rows) > 1) {
            $subjectList = Stgsubjects::find()
                ->select(['subjects_id', 'subjects_name'])
                ->where('grades_id != 0 and subjects_id != 13 and enable = 1 and xtype = 1')
                ->groupBy('subjects_id')
                ->orderBy('subjects_id')
                ->asArray()
                ->all();
            if (count($subjectList) === 0) {
                return [];
            }
            if (count($subjectList) > 1) {
                $subjectList = array_merge([['subjects_id' => '-1', 'subjects_name' => '全部']], $subjectList);
            }
            $item = ['stages_id' => '-1', 'stages_name' => '全部', 'subjects' => $subjectList];
            $data[] = $item;
        }
        foreach ($rows as $row) {
            list($stages_id, $stages_name) = explode('|', $row['stage']);
            $item = compact('stages_id', 'stages_name');
            $subjects = explode(',', $row['subject']);
            foreach ($subjects as $subject) {
                list($subjects_id, $subjects_name) = explode('|', $subject);
                $item['subjects'][] = compact('subjects_id', 'subjects_name');
            }
            if (count($item['subjects']) > 1) {
                $item['subjects'] = array_merge([['subjects_id' => '-1', 'subjects_name' => '全部']], $item['subjects']);
            }
            $data[] = $item;
        }
        return $data;
    }

    /**
     * 获取年级科目列表
     * @return array
     */
    public function actionGrades()
    {
        $rows = Stgsubjects::find()
            ->select([
                "group_concat(DISTINCT grades_id, '|', grades_name order by grades_id) grade",
                "group_concat(DISTINCT subjects_id, '|', subjects_name order by subjects_id) subject",
            ])
            ->where('grades_id != 0 and subjects_id != 13 and enable = 1 and xtype = 1')
            ->groupBy('grades_id')
            ->orderBy('grades_id')
            ->asArray()
            ->all();
        $data = [];
        foreach ($rows as $row) {
            list($grades_id, $grades_name) = explode('|', $row['grade']);
            $item = compact('grades_id', 'grades_name');
            $subjects = explode(',', $row['subject']);
            foreach ($subjects as $subject) {
                list($subjects_id, $subjects_name) = explode('|', $subject);
                $item['subjects'][] = compact('subjects_id', 'subjects_name');
            }
            $data[] = $item;
        }
        return $data;
    }
}
