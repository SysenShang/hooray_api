<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 10/20/15
 * Time: 2:57 PM
 */

namespace mbandroid\modules\v1\controllers;

use common\models\Friend;
use common\models\StuCount;
use common\models\TchCount;
use common\models\TeacherInfo;
use common\models\User;
use yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;


class FollwerController extends ActiveController
{
    public $modelClass = 'common\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
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
            'only' => ['Create', 'Delete', 'following', 'following-search'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['Create', 'Delete', 'following', 'following-search'],
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

    /*
     * 关注
     */
    public function actionCreate()
    {
        $postData = Yii::$app->request->post();
        $user_id = $postData['user_id'];
        $login_user_id = Yii::$app->user->id;
        $group_id = Yii::$app->user->identity->group_id;

        if ($user_id == $login_user_id) {
            $data['status'] = '2012';
            $data['msg'] = Yii::$app->params['u_2012'];
            return $data;
        }

        $user = User::findOne(['user_id' => $user_id]);
        if (!empty($user)) {
            $username = $user->username;
            $friend = new Friend();
            $friend->fromId = $login_user_id;
            $friend->toId = $user_id;
            $friend->title = $username;
            $friend->createdTime = date('Y-m-d H:i:s');

            $fri_rt = Friend::findOne(['fromId' => $login_user_id, 'toId' => $user_id]);
            if ($fri_rt) {
                $data['status'] = '2014';
                $data['msg'] = Yii::$app->params['u_2014'];
                return $data;
            }

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $tch_save = TchCount::updateAllCounters(['follower' => 1], ['user_id' => $user_id]);
                if ($group_id == 1) {
                    $stu_save = StuCount::updateAllCounters(['following' => 1], ['user_id' => $login_user_id]);

                } elseif ($group_id == 2) {
                    $stu_save = TchCount::updateAllCounters(['following' => 1], ['user_id' => $login_user_id]);
                }

                if ($tch_save && $friend->save() && $stu_save) {
                    $id = $friend->getPrimaryKey();
                    $transaction->commit();
                    $data = '';
                } else {
                    throw new yii\base\ErrorException;
                }

            } catch (yii\base\ErrorException $e) {
                $transaction->rollBack();
                $data['status'] = '2013';
                $data['msg'] = Yii::$app->params['u_2013'];
            }

        } else {
            $data['status'] = '2008';//用户不存在
            $data['msg'] = Yii::$app->params['u_2008'];
        }
        return $data;
    }


    /**
     *删除关注
     */
    public function actionDelete($id)
    {

        $login_user_id = Yii::$app->user->id;
        $login_group_id = Yii::$app->user->identity->group_id;

        $friend = Friend::findOne(['fromId' => $login_user_id, 'toId' => $id]);
        if (empty($friend)) {
            return ['status' => '2022', 'msg' => Yii::$app->params['u_2022']];
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($login_group_id == 1) {
                $lander = StuCount::findOne(['user_id' => $login_user_id]);
            } elseif ($login_group_id == 2) {
                $lander = TchCount::findOne(['user_id' => $login_user_id]);
            } else {
                return ['status' => '2020', 'msg' => Yii::$app->params['u_2020']];
            }
            if (empty($lander)) {
                return ['status' => '2008', 'msg' => Yii::$app->params['u_2008']];
            }
            $lander->following = ($lander->following - 1) > 0 ? ($lander->following - 1) : 0;
            if (!$lander->save()) {
                $transaction->rollBack();
                return ['status' => '2023', 'msg' => Yii::$app->params['u_2023']];
            }

            $user = User::findOne(['user_id' => $id]);
            if (empty($user)) {
                $transaction->rollBack();
                return ['status' => '2021', 'msg' => Yii::$app->params['u_2021']];
            }
            $group_id = $user->group_id;
            if ($group_id == 1) {
                $follower = StuCount::findOne(['user_id' => $id]);
            } elseif ($group_id == 2) {
                $follower = TchCount::findOne(['user_id' => $id]);
            } else {
                return ['status' => '2024', 'msg' => Yii::$app->params['u_2024']];
            }
            if (empty($follower)) {
                return ['status' => '2026', 'msg' => Yii::$app->params['u_2026']];
            }
            $follower->follower = ($follower->follower - 1) > 0 ? ($follower->follower - 1) : 0;
            if (!$follower->save()) {
                $transaction->rollBack();
                return ['status' => '2025', 'msg' => Yii::$app->params['u_2025']];
            }

            $result = Friend::deleteAll(['fromId' => $login_user_id, 'toId' => $id]);
            if (empty($result)) {
                $transaction->rollBack();
                return ['status' => '2015', 'msg' => Yii::$app->params['u_2015']];
            }
            $transaction->commit();
            return ['status' => '200'];
        } catch (yii\db\Exception $e) {
            $transaction->rollBack();
            return ['status' => '2015', 'msg' => Yii::$app->params['u_2015']];
        }
    }

    /**
     * 我的关注列表
     * @return array
     */
    public function actionFollowing()
    {
        return $this->following();
    }

    /**
     * 关注的老师搜索
     * @return array
     */
    public function actionFollowingSearch()
    {
        return $this->following(true);
    }

    /**
     * 关注列表查询
     * @param bool $isKeyword
     * @return array
     */
    protected function following($isKeyword = false)
    {
        $param = Yii::$app->request->get();
        $page = isset($param['page']) ? $param['page'] : 1;
        $userId = Yii::$app->user->identity->user_id;
        $condition = [
            'f.fromId user_id',
            'f.toId teacher_id',
            'f.createdTime create_time',
            'u.username',
            'u.status',
            't.nickname',
            't.avatar',
            't.profile',
            't.gender',
            't.characteristics',
            'c.rating',
            'c.follower',
            'c.comment_num',
            'c.comment_sum_rating',
            'c.positive',
            'c.moderate',
            'c.negative',
        ];
        $query = Friend::find()
            ->select($condition)
            ->distinct()
            ->from(Friend::tableName() . ' f')
            ->leftJoin(TeacherInfo::tableName() . ' t', 't.user_id=f.toId')
            ->leftJoin(TchCount::tableName() . ' c', 'c.user_id=f.toId')
            ->leftJoin(User::tableName() . ' u', 'u.user_id=f.toId')
            ->where(['fromId' => $userId]);
        if ($isKeyword) {
            $keyword = isset($param['keyword']) ? $param['keyword'] : '';
            $keyword = trim($keyword);
            if ($keyword == '') {
                return ['status' => '2047'];
            }
            $query->andWhere(['or', ['like', 'u.username', $keyword], ['like', 't.nickname', $keyword]]);
        }
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['createdTime' => SORT_DESC])
            ->asArray()
            ->all();
        $pageCount = $pages->getPageCount();
        $data['page_count'] = $pageCount . '';
        $data['page'] = $page . '';
        $data['page_size'] = ($pages->limit) . '';
        $data['data'] = [];
        if ($page <= $pageCount) {
            foreach ($rows as & $row) {
                $commentNum = intval($row['comment_num']);
                $commentSumRating = intval($row['comment_sum_rating']);
                $commentRating = $commentNum < 1 ? 0 : number_format($commentSumRating / $commentNum, 1, '.', '');

                if ($commentRating <= 5 && $commentRating > 0) {
                    $commentRating = "$commentRating";
                } elseif ($commentRating == 0) {
                    $commentRating = "0.0";
                } else {
                    $commentRating = "5.0";
                }
                $row['commentRating'] = $commentRating;

                $numerator = $row['positive'];
                $denominator = $row['positive'] + $row['moderate'] + $row['negative'];
                if ($denominator == 0) {
                    $teacherRating = 0;
                } else {
                    $teacherRating = $numerator / $denominator;
                    $teacherRating = round($teacherRating, 2);
                }
                $row['teacherPopularity'] = $teacherRating . '';
            }
            $data['data'] = $rows;
        }
        return $data;
    }
}
