<?php
/**
 * Created by PhpStorm.
 * User: lvdongxiao
 * Date: 8/5/15
 * Time: 4:56 PM
 */
namespace mbandroid\modules\v1\controllers;


use common\components\CTask;
use common\models\MicroCourseComment;


use common\models\User;

use yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
use yii\helpers\ArrayHelper;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;


use yii\data\Pagination;
use yii\filters\AccessControl;

class MicroCommentController extends ActiveController
{
    public $modelClass = 'common\models\MicroCourseComment';


    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'authenticator' => [
                    'class' => CompositeAuth::className(),
                    'except' => ['index', 'list'],  // set actions for disable access!
                    'authMethods' => [
                        HttpBasicAuth::className(),
                        HttpBearerAuth::className(),
                        QueryParamAuth::className(),
                    ]
                ],
                'access'=>[
                    'class' => AccessControl::className(),
                    'except' => [
                        'list',
                    ],
                    'rules' => [
                        // allow authenticated users
                        [
                            'allow' => true,
                            'actions' => [
                                'create',
                                'delete',
                            ],
                            'roles' => ['@'],
                        ],
                        // everything else is denied
                    ],
                    'denyCallback' => function () {
                        throw new \Exception('您无权访问该页面');
                    },
                ],
                [
                    'class' => TimestampBehavior::className(),
                    'createdAtAttribute' => 'create_time',
                    'updatedAtAttribute' => 'update_time',
                    'value' => new Expression('NOW()'),
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
     * 提交微课评论
     * @return bool
     */
    public function actionCreate()
    {
        $micro_id  = Yii::$app->request->post('micro_id', '');
        $parent_id = Yii::$app->request->post('parent_id', 0);
        $title     = Yii::$app->request->post('title', '');
        $content   = Yii::$app->request->post('content', '');

        $user_id = Yii::$app->user->getId();

        $now = date('Y-m-d H:i:s');

        $MicroCourseComment = new MicroCourseComment();

        $MicroCourseComment->parent_id   = $parent_id;
        $MicroCourseComment->micro_id    = $micro_id;
        $MicroCourseComment->user_id     = $user_id;
        $MicroCourseComment->title       = $title;
        $MicroCourseComment->content     = $content;
        $MicroCourseComment->update_time = $now;
        $MicroCourseComment->create_time = $now;
        if ($MicroCourseComment->save()) {
            CTask::done($user_id, 1, 'judgeweike');
        }
        return '';
    }

    /**
     * 微课列表
     * @param $id
     * @return mixed
     */
    public function actionIndex()
    {
        $param = Yii::$app->request->post();
        $micro_id = isset($param['micro_id']) ? $param['micro_id'] : '';
        print($micro_id);
        echo "aaaaaaa";
        exit();
    }

    /**
     * 删除微课评论
     * @param $id
     */
    public function actionDelete()
    {
        $param = Yii::$app->request->post();
        $id = isset($param['id']) ? $param['id'] : '';
        if (!empty($id)) {
            $model = MicroCourseComment::findOne($id);
            return $model->delete();
        }
    }

    /**
     * 获取微课评论列表
     * @return mixed
     */
    public function actionList()
    {
        $param = Yii::$app->request->get();
        $page = isset($param['page']) ? $param['page'] : 1;
        $param = Yii::$app->request->post();
        $micro_id = isset($param['micro_id']) ? $param['micro_id'] : '';
        $pageSize = isset($param['page_size']) ? $param['page_size'] : 10;
        $feed_id = isset($param['feed_id']) ? $param['feed_id'] : '';
        $slip_direction = isset($param['slip_direction']) ? $param['slip_direction'] : 'down';

        $query = MicroCourseComment::find();
        $query->select(['id','parent_id','micro_id','user_id','title','content',
                        'update_time','create_time']);
        $query->where(['micro_id'=>$micro_id]);

        $andWhere = [];
        if (!empty($feed_id)) {
            if ($slip_direction == 'up') {
                $andWhere = ['>', 'id', $feed_id];
            } else {
                $andWhere = ['<', 'id', $feed_id];
            }
        }
        if (!empty($andWhere)) {
            $query->andWhere($andWhere);
        }

        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => $pageSize]);

        $list = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        $pageCount = $pages->getPageCount();
        $data['page_count'] = $pageCount . '';
        $data['page'] = $page  . '';
        $data['page_size'] = ($pages->limit)  . '';
        $data['micro_id'] = $micro_id;
        $data['data'] = [];
        if ($page <= $pageCount) {
            foreach ($list as $value) {
                $userinfo = User::getUserinfo($value['user_id']);
                $value['username'] = $userinfo['username'];
                $value['avatar'] = $userinfo['avatar'];
                $temp = [];
                $temp['feed_id'] = $value['id'];
                $temp['feed'] = $value;
                $data['data'][] = $temp;
            }
        }
        return $data;
    }


}

