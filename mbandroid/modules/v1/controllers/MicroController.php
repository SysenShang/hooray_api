<?php
/**
 * Created by PhpStorm.
 * User: lvdongxiao
 * Date: 8/5/15
 * Time: 4:56 PM
 */
namespace mbandroid\modules\v1\controllers;

use common\components\JPushNotice;
use common\components\RedisStorage;
use common\models\Favorites;
use common\models\MicroCourse;
use common\models\MicroCourseOrder;
use common\models\MicroCourseViewCount;
use common\models\Stgsubjects;
use common\models\TchCount;
use common\models\TeacherInfo;
use common\models\User;
use yii;
use yii\behaviors\TimestampBehavior;
use yii\data\Pagination;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

class MicroController extends ActiveController
{
    public $modelClass = 'common\models\Micro';

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'authenticator' => [
                    'class' => CompositeAuth::className(),
                    'only' => [
                        'create',
                        'delete',
                        'search',
                        'new-search',
                        'update-viewnums',
                        'my-publish-micro-list',
                        'publish',
                        'modify',
                    ],
                    'authMethods' => [
                        HttpBasicAuth::className(),
                        HttpBearerAuth::className(),
                        QueryParamAuth::className(),
                    ]
                ],
                'access' => [
                    'class' => AccessControl::className(),
                    'only' => [
                        'create',
                        'delete',
                        'search',
                        'new-search',
                        'update-viewnums',
                        'my-publish-micro-list',
                        'publish',
                        'modify',
                        'get-micro-info',
                    ],
                    'rules' => [
                        // allow authenticated users
                        [
                            'allow' => true,
                            'actions' => [
                                'create',
                                'delete',
                                'search',
                                'new-search',
                                'update-viewnums',
                                'my-publish-micro-list',
                                'publish',
                                'modify',
                                'get-micro-info',
                            ],
                            'roles' => ['@'],
                        ],
                        [
                            'allow' => true,
                            'actions' => ['get-micro-info'],
                            'roles' => ['?'],
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

    public function actionUpdateViewnums()
    {
        $param = Yii::$app->request->post();
        $micro_id = isset($param['micro_id']) ? $param['micro_id'] : '';
        if (empty($micro_id)) {
            return ['status' => '9001', 'msg' => Yii::$app->params['mc_9001']];
        }
        $micro = MicroCourse::findOne(['id' => $micro_id]);
        $micro['viewnums'] += 1;
        if ($micro->save()) {
            return ['status' => '200'];
        } else {
            return ['status' => '9022', 'msg' => Yii::$app->params['mc_9022']];
        }
    }

    /**
     * 提交微课
     * @return bool
     */
    public function actionCreate()
    {
        $postData = Yii::$app->request->post();

        if (!isset($postData['name'],
                $postData['gradename'],
                $postData['coursename'],
                $postData['video_url']) ||
            empty($postData['video_url']) ||
            empty($postData['gradename']) ||
            empty($postData['coursename']) ||
            empty($postData['name'])
        ) {
            return ['status' => '9026', 'msg' => Yii::$app->params['mc_9026']];
        }
        $publish = isset($postData['publish']) ? $postData['publish'] : '0';
        $content = isset($postData['content']) ? $postData['content'] : '';
        $price = isset($postData['price']) ? $postData['price'] : '2';
        $duration = isset($postData['duration']) ? $postData['duration'] : '0';
        if (empty($price)) {
            $price = '2';
        }
        $video_url = $postData['video_url'];
        $grades_name = $postData['gradename'];
        $subjects_name = $postData['coursename'];

        $user_id = Yii::$app->user->identity->user_id;
        $group_id = Yii::$app->user->identity->group_id;
        if ($group_id != 2) {
            return ['status' => '9028', 'msg' => Yii::$app->params['mc_9028']];
        }
        $username = Yii::$app->user->identity->username;

        $subject = Stgsubjects::findOne(['grades_name' => $grades_name, 'subjects_name' => $subjects_name]);
        if (empty($subject)) {
            return ['status' => '9027', 'msg' => Yii::$app->params['mc_9027']];
        }

        $microCourse = new MicroCourse();
        $microCourse->name = $postData['name'];
        $microCourse->user_id = $user_id;
        $microCourse->realname = $username;
        $microCourse->stage_id = $subject->stages_id;
        $microCourse->stagename = $subject->stages_name;
        $microCourse->grade_id = $subject->grades_id;
        $microCourse->course_id = $subject->subjects_id;
        $microCourse->gradename = $grades_name;
        $microCourse->coursename = $subjects_name;
        $microCourse->video_url = $video_url;
        $microCourse->video_duration = $duration;
        $microCourse->content = $content;
        $microCourse->publish = $publish;
        $microCourse->isauth = 0;
        $microCourse->xstatus = 1;
        $microCourse->create_time = date('Y-m-d H:i:s');
        $microCourse->update_time = date('Y-m-d H:i:s');
        $microCourse->price = $price;

        if ($microCourse->save()) {
            $jpush = new JPushNotice();
            //按年级推送
            $jpush->send((string)($subject->grades_id - 4), [
                'type' => '1000',
                'title' => $username . '老师有新的微课《' . $postData['name'] . '》'
            ]);
            return ['status' => '200'];
        } else {
            return ['status' => '9025', 'msg' => Yii::$app->params['mc_9025']];
        }
    }

    /**
     * 编辑微课
     * @return array
     */
    public function actionModify()
    {
        $postData = Yii::$app->request->post();
        $micro_id = isset($postData['micro_id']) ? $postData['micro_id'] : '';
        if (empty($micro_id)) {
            return ['status' => '9001', 'msg' => Yii::$app->params['mc_9001']];
        }
        if (!isset($postData['name']) &&
            !isset($postData['price']) &&
            !isset($postData['content']) &&
            !isset($postData['gradename'], $postData['coursename'])
        ) {
            return ['status' => '9035', 'msg' => Yii::$app->params['mc_9035']];
        }
        $user_id = Yii::$app->user->identity->user_id;
        $group_id = Yii::$app->user->identity->group_id;
        if ($group_id != 2) {
            return ['status' => '9037', 'msg' => Yii::$app->params['mc_9037']];
        }
        $micro = MicroCourse::findOne(['id' => $micro_id]);
        if (empty($micro)) {
            return ['status' => '9038', 'msg' => Yii::$app->params['mc_9038']];
        }
        if ($micro->xstatus == 0) {
            return ['status' => '9039', 'msg' => Yii::$app->params['mc_9039']];
        }
        if ($micro->user_id != $user_id) {
            return ['status' => '9040', 'msg' => Yii::$app->params['mc_9040']];
        }
        if (isset($postData['name'])) {
            $name = trim($postData['name']);
            if ($micro->name != $name) {
                $micro->name = $name;
            }
        }
        if (isset($postData['content'])) {
            $content = trim($postData['content']);
            if ($micro->content != $content) {
                $micro->content = $content;
            }
        }
        if (isset($postData['price'])) {
            $price = trim($postData['price']);
            if (empty($price)) {
                if ($price == '') {
                    $price = 2;
                }
            }
            if ($micro->price != $price) {
                $micro->price = $price;
            }
        }
        if (isset($postData['gradename'], $postData['coursename'])) {
            $grades_name = trim($postData['gradename']);
            $subjects_name = trim($postData['coursename']);
            if (empty($grades_name) || empty($subjects_name)) {
                return ['status' => '9036', 'msg' => Yii::$app->params['mc_9036']];
            }
            if ($micro->gradename != $grades_name || $micro->coursename != $subjects_name) {
                $subject = Stgsubjects::findOne(['grades_name' => $grades_name, 'subjects_name' => $subjects_name]);
                if (empty($subject)) {
                    return ['status' => '9027', 'msg' => Yii::$app->params['mc_9027']];
                }
                $micro->stage_id = $subject->stages_id;
                $micro->stagename = $subject->stages_name;
                $micro->grade_id = $subject->grades_id;
                $micro->course_id = $subject->subjects_id;
                $micro->gradename = $grades_name;
                $micro->coursename = $subjects_name;
            }
        }
        $micro->update_time = date('Y-m-d H:i:s');
        if ($micro->update()) {
            return ['status' => '200'];
        } else {
            return ['status' => '9025', 'msg' => Yii::$app->params['mc_9025']];
        }
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        //
    }

    /**
     * 删除微课
     * @param $id
     * @return bool
     */
    public function actionDelete($id)
    {
        $postData = Yii::$app->request->post();
        $cid = $postData['id'];

        if ($cid) {
            $result = MicroCourse::updateAll(['xstatus' => 0], ['id' => $cid]);
            return $result ? true : false;
        } else {
            return false;
        }
    }

    /**
     * 查看微课信息
     * @return mixed
     */
    public function actionGetMicroInfo()
    {
        $param = Yii::$app->request->post();
        $id = isset($param['id']) ? $param['id'] : '';
        $userId = isset(Yii::$app->user->identity->user_id) ? Yii::$app->user->identity->user_id : '';
        $ip = Yii::$app->request->getUserIP();
        if (empty($id)) {
            return ['status' => '9001', 'msg' => Yii::$app->params['mc_9001']];
        }
        $now_time = date('Y-m-d H:i:s');
        $start_time = date('Y-m-d') . ' 00:00:00';
        $end_time = date('Y-m-d') . ' 23:59:59';
        $micro_count = MicroCourseViewCount::find()->where(['micro_id' => $id, 'user_id' => $userId])
            ->andWhere(['>', 'created_time', $start_time])
            ->andWhere(['<', 'created_time', $end_time])
            ->orderBy('updated_time desc')->one();
        $micro = MicroCourse::findOne(['id' => $id]);
        if ($micro_count) {
            $micro_count->updated_time = $now_time;
            $micro_count->ip = $ip;
        } else {
            //写入微课浏览表
            $micro_count = new MicroCourseViewCount();
            $micro_count->micro_id = $id;
            $micro_count->user_id = $userId;
            $micro_count->ip = $ip;
            $micro_count->created_time = $now_time;
            $micro_count->updated_time = $now_time;
            $micro_count->view_counts = 1;
            //更新微课表浏览次数
            $micro->viewnums += 1;
            $micro->save();
        }
        if (!$micro_count->save()) {
            return ['status' => '9022', 'msg' => Yii::$app->params['mc_9022']];
        }
        $info = MicroCourse::find()->where(['id' => $id])->asArray()->one();
        if ($info) {
            $fav = Favorites::findOne(['user_id' => $userId, 'resource_type' => 'weike', 'resource_id' => $id]);
            $info['is_favorite'] = $fav['status'] === 0 ? '1' : '0';
            $exists = MicroCourseOrder::find()->where(['user_id' => $userId, 'mc_id' => $id, 'isdel' => 0])
                ->andWhere(['>', 'valid_time', time()])
                ->count();
            $info['is_buy'] = $exists > 0 ? '1' : '0';
            $userinfo = RedisStorage::user($info['user_id']);
            $info['username'] = $userinfo['username'];
            $info['realname'] = $info['username'];
            $info['publish'] = $info['publish'] == '1' ? '公开' : '不公开';
            $info['isfop'] = $info['isfop'] == '1' ? '正在转码' : '转码完成';
            if ($info['isauth'] == '1') {
                $info['isauth'] = '审核通过';
            } elseif ($info['isauth'] == '0') {
                $info['isauth'] = '审核中';
            } else {
                $info['isauth'] = '审核未通过';
            }
        }
        $data = $info;
        return $data;
    }

    /**
     * 获取微课相关
     * @return mixed
     */
    public function actionGetMicroRelative()
    {
        $param = Yii::$app->request->get();
        $page = isset($param['page']) ? $param['page'] : 1;
        $param = Yii::$app->request->post();
        $micro_id = isset($param['micro_id']) ? $param['micro_id'] : '';
        if (empty($micro_id)) {
            return ['status' => '9001', 'msg' => Yii::$app->params['mc_9001']];
        }

        $microinfo = MicroCourse::findOne(['id' => $micro_id]);
        $query = MicroCourse::find();
        $where = [
            'stagename' => $microinfo->stagename,
            'gradename' => $microinfo->gradename,
            'coursename' => $microinfo->coursename,
        ];
        $query->where($where);
        $query->andWhere(['publish' => 1, 'xstatus' => 1, 'isauth' => 1, 'isfop' => 0]);

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10]);
        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        $pageCount = $pages->getPageCount();
        $data['page_count'] = $pageCount . '';
        $data['page'] = $page . '';
        $data['page_size'] = ($pages->limit) . '';
        $data['list'] = [];
        if ($page <= $pageCount) {
            foreach ($rows as &$row) {
                $userinfo = RedisStorage::user($row['user_id']);
                $row['username'] = $userinfo['username'];
                $row['realname'] = $row['username'];
            }
            $data['list'] = $rows;
        }
        return $data;
    }

    /**
     * 获取所有微课列表
     * @return mixed
     */
    public function actionGetMicroList()
    {
        $postData = Yii::$app->request->post();
        $pageSize = isset($postData['page_size']) ? $postData['page_size'] : 4;
        if (empty($pageSize)) {
            $pageSize = 4;
        }

        $subjectList = Yii::$app->params['subjects'];
        $data = [];
        foreach ($subjectList as $subject) {
            $query = MicroCourse::find();
            $query->where(['coursename' => $subject]);
            $query->andWhere(['publish' => 1, 'xstatus' => 1, 'isauth' => 1, 'isfop' => 0]);
            $countQuery = clone $query;
            $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);
            $rows = $query->offset($pages->offset)
                ->limit($pages->limit)
                ->orderBy(['id' => SORT_DESC])
                ->asArray()
                ->all();

            if (count($rows) < 4) {
                continue;
            }
            foreach ($rows as &$row) {
                $userinfo = RedisStorage::user($row['user_id']);
                $row['username'] = $userinfo['username'];
                $row['realname'] = $row['username'];
            }
            $list['subject']['subject_name'] = $subject;
            $list['list'] = $rows;
            $data[] = $list;
        }
        return $data;
    }

    /**
     * 获取最新的微课列表
     * @return mixed
     */
    public function actionGetNewestMicroList()
    {
        $param = Yii::$app->request->get();
        $page = isset($param['page']) ? $param['page'] : 1;
        $postData = Yii::$app->request->post();
        $pageSize = isset($postData['page_size']) ? $postData['page_size'] : 20;
        $feed_id = isset($postData['feed_id']) ? intval($postData['feed_id']) : '';
        $slip_direction = isset($postData['slip_direction']) ? $postData['slip_direction'] : 'down';

        $query = MicroCourse::find();
        $query->where(['publish' => 1, 'xstatus' => 1, 'isauth' => 1, 'isfop' => 0]);
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

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);
        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['create_time' => SORT_DESC, 'id' => SORT_DESC])
            ->asArray()
            ->all();

        $pageCount = $pages->getPageCount();
        $data['page_count'] = $pageCount . '';
        $data['page'] = $page . '';
        $data['page_size'] = ($pages->limit) . '';
        $data['data'] = [];
        if ($page <= $pageCount) {
            foreach ($rows as $row) {
                $temp = [];
                $temp['feed_id'] = $row['id'];
                $userinfo = RedisStorage::user($row['user_id']);
                $row['username'] = $userinfo['username'];
                $row['realname'] = $row['username'];
                $temp['feed'] = $row;
                $data['data'][] = $temp;
            }
        }
        return $data;
    }

    /**
     * 获取热点微课列表
     */
    public function actionGetTopMicroList()
    {
        $param = Yii::$app->request->get();
        $page = isset($param['page']) ? $param['page'] : 1;
        $postData = Yii::$app->request->post();
        $pageSize = isset($postData['page_size']) ? $postData['page_size'] : 20;
//        $feed_id = isset($postData['feed_id']) ? intval($postData['feed_id']) : '';
//        $slip_direction = isset($postData['slip_direction']) ? $postData['slip_direction'] : 'down';

        $query = MicroCourse::find();
        $query->where(['publish' => 1, 'xstatus' => 1, 'isauth' => 1, 'isfop' => 0]);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);
//        $rows = $query->offset($pages->offset)
//            ->limit($pages->limit)
//            ->orderBy(['viewnums' => SORT_DESC])
//            ->asArray()
//            ->all();

        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['create_time' => SORT_DESC, 'id' => SORT_DESC])
            ->asArray()
            ->all();

        $pageCount = $pages->getPageCount();
        $data['page_count'] = $pageCount . '';
        $data['page'] = $page . '';
        $data['page_size'] = ($pages->limit) . '';
        $data['data'] = [];
        if ($page <= $pageCount) {
            foreach ($rows as $row) {
                $temp = [];
                $temp['feed_id'] = $row['id'];
                $userinfo = RedisStorage::user($row['user_id']);
                $row['username'] = $userinfo['username'];
                $row['realname'] = $row['username'];
                $temp['feed'] = $row;
                $data['data'][] = $temp;
            }
        }
        return $data;
    }

    /**
     * 你喜欢的微课列表
     */
    public function actionYourLikeList()
    {
        $param = Yii::$app->request->get();
        $page = isset($param['page']) ? $param['page'] : 1;
        $postData = Yii::$app->request->post();
        $pageSize = isset($postData['page_size']) ? $postData['page_size'] : 20;
        $feed_id = isset($postData['feed_id']) ? intval($postData['feed_id']) : '';
        $slip_direction = isset($postData['slip_direction']) ? $postData['slip_direction'] : 'down';

        $query = MicroCourse::find();
        $query->where(['publish' => 1, 'xstatus' => 1, 'isauth' => 1, 'isfop' => 0]);
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

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);
        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        $pageCount = $pages->getPageCount();
        $data['page_count'] = $pageCount . '';
        $data['page'] = $page . '';
        $data['page_size'] = ($pages->limit) . '';
        $data['data'] = [];
        if ($page <= $pageCount) {
            foreach ($rows as $row) {
                $temp = [];
                $temp['feed_id'] = $row['id'];
                $userinfo = RedisStorage::user($row['user_id']);
                $row['username'] = $userinfo['username'];
                $row['realname'] = $row['username'];
                $temp['feed'] = $row;
                $data['data'][] = $temp;
            }
        }
        return $data;
    }

    /**
     * 根据阶段和科目获取微课列表
     * @return mixed
     */
    public function actionGetMicroListByCourse()
    {
        $param = Yii::$app->request->get();
        $page = isset($param['page']) ? $param['page'] : 1;
        $postData = Yii::$app->request->post();
        $pageSize = isset($postData['page_size']) ? intval($postData['page_size']) : 20;
        $feed_id = isset($postData['feed_id']) ? intval($postData['feed_id']) : '';
        $slip_direction = isset($postData['slip_direction']) ? $postData['slip_direction'] : 'down';
        $stagename = isset($postData['stagename']) ? $postData['stagename'] : '';
        $coursename = isset($postData['coursename']) ? $postData['coursename'] : '';
        $price = isset($postData['price']) ? $postData['price'] : '筛选';

        $query = MicroCourse::find();
        $where = [];
        if (!empty($stagename) && $stagename != '全部') {
            $where['stagename'] = $stagename;
        }
        if (!empty($coursename) && $coursename != '全部') {
            $where['coursename'] = $coursename;
        }
        if (!empty($where)) {
            $query->where($where);
        }
        if (!empty($feed_id)) {
            if ($slip_direction == 'up') {
                $query->andWhere(['>', 'id', $feed_id]);
            } else {
                $query->andWhere(['<', 'id', $feed_id]);
            }
        }
        if ($price !== '筛选') {
            if ($price === '免费') {
                $query->andWhere(['=', 'price', 0]);
            } else {
                $query->andWhere(['>', 'price', 0]);
            }
        }
        $query->andWhere(['publish' => 1, 'xstatus' => 1, 'isauth' => 1, 'isfop' => 0]);

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);
        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        $pageCount = $pages->getPageCount();
        $data['page_count'] = $pageCount . '';
        $data['page'] = $page . '';
        $data['page_size'] = ($pages->limit) . '';
        $data['data'] = [];
        if ($page <= $pageCount) {
            foreach ($rows as $row) {
                $temp = [];
                $temp['feed_id'] = $row['id'];
                $userinfo = RedisStorage::user($row['user_id']);
                $row['username'] = $userinfo['username'];
                $row['realname'] = $row['username'];
                $temp['feed'] = $row;
                $data['data'][] = $temp;
            }
        }
        return $data;
    }

    /**
     * 搜索微课
     * @return mixed
     */
    public function actionSearch()
    {
        $param = Yii::$app->request->get();
        $page = isset($param['page']) ? $param['page'] : 1;
        $postData = Yii::$app->request->post();
        $keyword = isset($postData['keyword']) ? trim($postData['keyword']) : '';
        $pageSize = isset($postData['page_size']) ? intval($postData['page_size']) : 20;
        $publish = isset($postData['publish']) ? $postData['publish'] : 'online';
        $filterDatetime = isset($postData['filter_datetime']) ? $postData['filter_datetime'] : '';
        $query = MicroCourse::find();
        $query->where(['like', 'name', $keyword]);
        $query->andWhere(['xstatus' => 1, 'isfop' => 0]);
        if ($publish === 'offline') {
            $query->andWhere(['publish' => 0, 'isauth' => [0, 1]]);
        } else {
            $query->andWhere(['publish' => 1, 'isauth' => 1]);
        }
        switch ($filterDatetime) {
            case 'earlier':
                $createTime = ['<', 'create_time', date('Y-m-d', strtotime('-1 year'))];
                break;
            case 'withinYear':
                $createTime = ['>=', 'create_time', date('Y-m-d', strtotime('-1 year'))];
                break;
            case 'withinMonth':
                $createTime = ['>=', 'create_time', date('Y-m-d', strtotime('-1 month'))];
                break;
            case 'withinWeek':
                $createTime = ['>=', 'create_time', date('Y-m-d', strtotime('-1 week'))];
                break;
            default:
                $createTime = '';
        }
        if (!empty($createTime)) {
            $query->andWhere($createTime);
        }
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);
        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        $pageCount = $pages->getPageCount();
        $data['page_count'] = $pageCount . '';
        $data['page'] = $page . '';
        $data['page_size'] = ($pages->limit) . '';
        $data['data'] = [];
        if ($page <= $pageCount) {
            foreach ($rows as &$row) {
                $temp = [];
                $temp['feed_id'] = $row['id'];
                $userinfo = RedisStorage::user($row['user_id']);
                $row['username'] = $userinfo['username'];
                $row['realname'] = $row['username'];
                $row['publish'] = $row['publish'] == '1' ? '公开' : '不公开';
                $row['isfop'] = $row['isfop'] == '1' ? '正在转码' : '转码完成';
                if ($row['isauth'] == '1') {
                    $row['isauth'] = '审核通过';
                } elseif ($row['isauth'] == '0') {
                    $row['isauth'] = '审核中';
                } else {
                    $row['isauth'] = '审核未通过';
                }
                $temp['feed'] = $row;
                $data['data'][] = $temp;
            }
        }
        return $data;
    }

    public function actionNewSearch()
    {
        $param = Yii::$app->request->get();
        $page = isset($param['page']) ? $param['page'] : 1;
        $postData = Yii::$app->request->post();
        $keyword = isset($postData['keyword']) ? trim($postData['keyword']) : '';
        $pageSize = isset($postData['page_size']) ? intval($postData['page_size']) : 20;
        $publish = isset($postData['publish']) ? $postData['publish'] : 'online';
        $filterDatetime = isset($postData['filter_datetime']) ? $postData['filter_datetime'] : '';
        $query = MicroCourse::find();
        $query->where(['like', 'name', $keyword]);
        $query->andWhere(['xstatus' => 1, 'isfop' => 0]);
        if ($publish === 'offline') {
            $query->andWhere(['publish' => 0, 'isauth' => [0, 1]]);
        } else {
            $query->andWhere(['publish' => 1, 'isauth' => 1]);
        }
        switch ($filterDatetime) {
            case 'earlier':
                $createTime = ['<', 'create_time', date('Y-m-d', strtotime('-1 year'))];
                break;
            case 'withinYear':
                $createTime = ['>=', 'create_time', date('Y-m-d', strtotime('-1 year'))];
                break;
            case 'withinMonth':
                $createTime = ['>=', 'create_time', date('Y-m-d', strtotime('-1 month'))];
                break;
            case 'withinWeek':
                $createTime = ['>=', 'create_time', date('Y-m-d', strtotime('-1 week'))];
                break;
            default:
                $createTime = '';
        }
        if (!empty($createTime)) {
            $query->andWhere($createTime);
        }
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);
        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        $pageCount = $pages->getPageCount();
        $data['page_count'] = $pageCount . '';
        $data['page'] = $page . '';
        $data['page_size'] = ($pages->limit) . '';
        $data['data'] = [];
        if ($page <= $pageCount) {
            foreach ($rows as &$row) {
                $userinfo = RedisStorage::user($row['user_id']);
                $row['username'] = $userinfo['username'];
                $row['realname'] = $row['username'];
                $row['publish'] = $row['publish'] == '1' ? '公开' : '不公开';
                $row['isfop'] = $row['isfop'] == '1' ? '正在转码' : '转码完成';
                if ($row['isauth'] == '1') {
                    $row['isauth'] = '审核通过';
                } elseif ($row['isauth'] == '0') {
                    $row['isauth'] = '审核中';
                } else {
                    $row['isauth'] = '审核未通过';
                }
            }
            $data['data'] = $rows;
        }
        return $data;
    }

    public function actionMyPublishMicroList()
    {
        $param = Yii::$app->request->get();
        $page = isset($param['page']) ? $param['page'] : 1;
        $postData = Yii::$app->request->post();
        $pageSize = isset($postData['page_size']) ? $postData['page_size'] : 20;
        $publish = isset($postData['publish']) ? $postData['publish'] : '';
        $filterDatetime = isset($postData['filter_datetime']) ? $postData['filter_datetime'] : '';
        $keyword = isset($postData['keyword']) ? trim($postData['keyword']) : '';
        $user_id = Yii::$app->user->identity->user_id;
        $query = MicroCourse::find();
        $query->where(['xstatus' => 1, 'user_id' => $user_id]);
        if (!empty($publish)) {
            if ($publish === 'online') {
                $query->andWhere(['publish' => 1, 'isauth' => 1]);
            } elseif ($publish === 'offline') {
                $query->andWhere(['or', ['publish' => 0, 'isauth' => [0, 1]], ['publish' => 1, 'isauth' => 0]]);
            } else {
                //
            }
        }
        if ($keyword !== '') {
            $query->andWhere(['like', 'name', $keyword]);
        }
        switch ($filterDatetime) {
            case 'earlier':
                $createTime = ['<', 'create_time', date('Y-m-d', strtotime('-1 year'))];
                break;
            case 'withinYear':
                $createTime = ['>=', 'create_time', date('Y-m-d', strtotime('-1 year'))];
                break;
            case 'withinMonth':
                $createTime = ['>=', 'create_time', date('Y-m-d', strtotime('-1 month'))];
                break;
            case 'withinWeek':
                $createTime = ['>=', 'create_time', date('Y-m-d', strtotime('-1 week'))];
                break;
            default:
                $createTime = '';
        }
        if (!empty($createTime)) {
            $query->andWhere($createTime);
        }
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);
        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['isauth' => SORT_DESC, 'id' => SORT_DESC])
            ->asArray()
            ->all();
        $pageCount = $pages->getPageCount();
        $data['page_count'] = $pageCount . '';
        $data['page'] = $page . '';
        $data['page_size'] = ($pages->limit) . '';
        $data['data'] = [];
        if ($page <= $pageCount) {
            foreach ($rows as $row) {
                $userinfo = RedisStorage::user($row['user_id']);
                $row['username'] = $userinfo['username'];
                $row['realname'] = $row['username'];
                $row['publish'] = $row['publish'] == '1' ? '公开' : '不公开';
                $row['isfop'] = $row['isfop'] == '1' ? '正在转码' : '转码完成';
                if ($row['isauth'] == '1') {
                    $row['isauth'] = '审核通过';
                } elseif ($row['isauth'] == '0') {
                    $row['isauth'] = '审核中';
                } else {
                    $row['isauth'] = '审核未通过';
                }
                $data['data'][] = $row;
            }
        }
        return $data;
    }

    public function actionListByTeacher()
    {
        $param = Yii::$app->request->get();
        $page = isset($param['page']) ? $param['page'] : 1;
        $postData = Yii::$app->request->post();
        $pageSize = isset($postData['page_size']) ? $postData['page_size'] : 20;
        $userId = isset($postData['user_id']) ? $postData['user_id'] : '';
        if (empty($userId)) {
            return ['status' => '9001'];
        }
        return $this->getMicroList($page, $pageSize, $userId);
    }

    protected function getMicroList($page = 1,
                                    $pageSize = 20,
                                    $userId = '',
                                    $publish = 'online',
                                    $xstatus = 1,
                                    $keyword = '',
                                    $filterDatetime = '')
    {
        $query = MicroCourse::find()
            ->select(['m.*', 'u.username', 't.nickname'])
            ->distinct()
            ->from(MicroCourse::tableName() . ' m')
            ->leftJoin(User::tableName() . ' u', 'u.user_id = m.user_id')
            ->leftJoin(TeacherInfo::tableName() . ' t', 't.user_id = m.user_id');
        $query->where(['m.xstatus' => $xstatus, 'm.user_id' => $userId]);
        if (!empty($publish)) {
            if ($publish === 'online') {
                $query->andWhere(['m.publish' => 1, 'm.isauth' => 1]);
            } elseif ($publish === 'offline') {
                $query->andWhere([
                    'or',
                    ['m.publish' => 0, 'm.isauth' => [0, 1]],
                    ['m.publish' => 1, 'm.isauth' => 0]
                ]);
            } else {
                //
            }
        }
        if ($keyword !== '') {
            $query->andWhere(['like', 'name', $keyword]);
        }
        switch ($filterDatetime) {
            case 'earlier':
                $query->andWhere(['<', 'm.create_time', date('Y-m-d', strtotime('-1 year'))]);
                break;
            case 'withinYear':
                $query->andWhere(['>=', 'm.create_time', date('Y-m-d', strtotime('-1 year'))]);
                break;
            case 'withinMonth':
                $query->andWhere(['>=', 'm.create_time', date('Y-m-d', strtotime('-1 month'))]);
                break;
            case 'withinWeek':
                $query->andWhere(['>=', 'm.create_time', date('Y-m-d', strtotime('-1 week'))]);
                break;
            default:
                //
        }
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);
        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['m.id' => SORT_DESC])
            ->asArray()
            ->all();
        $pageCount = $pages->getPageCount();
        $data['page_count'] = $pageCount . '';
        $data['page'] = $page . '';
        $data['page_size'] = ($pages->limit) . '';
        $data['data'] = [];
        if ($page <= $pageCount) {
            foreach ($rows as $row) {
                $row['realname'] = $row['username'];
                $row['publish'] = $row['publish'] == '1' ? '公开' : '不公开';
                $row['isfop'] = $row['isfop'] == '1' ? '正在转码' : '转码完成';
                if ($row['isauth'] == '1') {
                    $row['isauth'] = '审核通过';
                } elseif ($row['isauth'] == '0') {
                    $row['isauth'] = '审核中';
                } else {
                    $row['isauth'] = '审核未通过';
                }
                $data['data'][] = $row;
            }
        }
        return $data;
    }

    /**
     * 更改微课公开状态
     * @param $postData ['micro_id'] 微课id
     * @param $postData ['publish'] online 公开; offline 不公开
     * @return ['status' => '200', 'msg' => 'ok']
     */
    public function actionPublish()
    {
        $postData = Yii::$app->request->post();
        $microId = isset($postData['micro_id']) ? $postData['micro_id'] : '';
        $publish = isset($postData['publish']) ? $postData['publish'] : '';
        if (empty($publish)) {
            return ['status' => '9030', 'msg' => Yii::$app->params['mc_9030']];
        }
        if ($publish == 'online') {
            $publish = 1;
        } elseif ($publish == 'offline') {
            $publish = 0;
        } else {
            return ['status' => '9030', 'msg' => Yii::$app->params['mc_9030']];
        }
        if (empty($microId)) {
            return ['status' => '9001', 'msg' => Yii::$app->params['mc_9001']];
        }
        $userId = Yii::$app->user->identity->user_id;
        $micro = MicroCourse::findOne(['id' => $microId, 'user_id' => $userId]);
        if (empty($micro)) {
            return ['status' => '9029', 'msg' => Yii::$app->params['mc_9029']];
        }
        if ($micro->publish == $publish) {
            if ($publish == 1) {
                return ['status' => '9031', 'msg' => Yii::$app->params['mc_9031']];
            } else {
                return ['status' => '9032', 'msg' => Yii::$app->params['mc_9032']];
            }
        }
        $micro->publish = $publish;
        if ($micro->save()) {
            return ['status' => '200', 'msg' => 'ok'];
        } else {
            return ['status' => '9033', 'msg' => Yii::$app->params['mc_9033']];
        }
    }

}

