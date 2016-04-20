<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 9/6/15
 * Time: 10:51 AM
 */

namespace mbandroid\modules\v1\controllers;

use common\components\RedisStorage;
use common\models\AskOrder;
use common\models\F;
use common\models\Friend;
use common\models\MicroCourse;
use common\models\Question;
use common\models\QuestionPost;
use common\models\TchCount;
use common\models\TeacherInfo;
use common\models\User;
use common\models\VerifyTeaching;
use yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\rest\ActiveController;


class StarController extends ActiveController
{
    public $modelClass = 'common\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'except' => ['index', 'create', 'view', 'user', 'question'],  // set actions for disable access!
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
            'only' => ['Index', 'Create', 'delete', 'update', 'view', 'post', 'Answer', 'Search'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['Index', 'Create', 'Delete', 'update', 'view', 'post', 'Answer', 'Search'],
                    'roles' => ['@'],
                ],
                [
                    'allow' => true,
                    'actions' => ['view'],
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


    public function actionIndex()
    {
        $subjects = Yii::$app->params['subjects'];

        $s = [];
        foreach ($subjects as $val) {

            $query = VerifyTeaching::find()
                ->select(VerifyTeaching::tableName() . '.*' . ',username,avatarstatus,regdate,status,question_num,coureses_num,online_coureses_num,rating,comment_num,comment_sum_rating,CoursePositive,CourseModerate,CourseNegative,positive,moderate,negative,follower')
                ->leftJoin(User::tableName(), User::tableName() . '.user_id' . '=' . VerifyTeaching::tableName() . '.user_id')
                ->leftJoin(TchCount::tableName(), TchCount::tableName() . '.user_id' . '=' . User::tableName() . '.user_id')
                ->where(['group_id' => 2, 'xstatus' => 1, 'subjects_name' => $val, 'flag' => 2]);
            $query->andWhere([User::tableName() . '.type' => 0]);

            $query = $query->groupBy('user_id')
                ->orderBy('status DESC, (question_num + coureses_num * 10) DESC, rating DESC')
                ->limit(2)
                ->asArray()
                ->all();
            $ct_tch = count($query);
            if ($ct_tch == 2) {
                foreach ($query as $v) {
                    $d['feed_name'] = $val;
                    $d['feed']['user_id'] = $v['user_id'];
                    $d['feed']['username'] = $v['username'];
                    $d['feed']['rating'] = isset($v['rating']) ? (string)$v['rating'] : "1";
                    $d['feed']['follower'] = $v['follower'] ? $v['follower'] : "";
                    $d['feed']['status'] = $v['status'] ? (string)$v['status'] : '0';
                    $user_info = RedisStorage::userinfo($v['user_id'], 2);
//                    $d['feed']['nickname'] = $user_info['nickname'];
                    $d['feed']['avatar'] = $user_info['avatar'] ? $user_info['avatar'] : "";
                    $d['feed']['profile'] = $user_info['profile'] ? $user_info['profile'] : "";
                    $d['feed']['gender'] = $user_info['gender'] ? $user_info['gender'] : "男";
                    $d['feed']['characteristics'] = $user_info['characteristics'] ? $user_info['characteristics'] : "";

                    $comment_nums = $v['comment_num'];
                    $comment_sum_rating = $v['comment_sum_rating'];
                    $comment_rating = $comment_nums < 1 ? 0 : number_format($comment_sum_rating / $comment_nums, 1, '.', '');

                    $commentRating = '5.0';
                    if ($comment_rating <= 5 && $comment_rating > 0) {
                        $commentRating = "$comment_rating";
                    } elseif ((int)$comment_rating === 0) {
                        $commentRating = '0.0';
                    }

                    $d['feed']['commentRating'] = $commentRating;

                    $numerator = $v['positive'];
                    $denominator = $v['positive'] + $v['moderate'] + $v['negative'];
                    if ($denominator == 0) {
                        $teacherRating = 0;
                    } else {
                        $teacherRating = $numerator / $denominator;
                        $teacherRating = round($teacherRating, 2);
                    }
                    $d['feed']['teacherPopularity'] = "$teacherRating";
                    $s[] = $d;
                }
            }
        }

        $d1 = [];
        foreach ($s as $v) {
            $d1[$v['feed_name']][] = $v['feed'];
        }
        $data = [];
        foreach ($d1 as $k => $v) {
            $d2['feed_name'] = $k;
            $d2['feed'] = $v;
            $data[] = $d2;
        }
        return $data;
    }

    /**
     * 名师堂老师列表
     * @return array
     */
    public function actionCreate()
    {
        $param = Yii::$app->request->get();
        $page = isset($param['page']) ? $param['page'] : 1;
        $postData = Yii::$app->request->post();
        $subjectName = isset($postData['subject_name']) ? $postData['subject_name'] : '';
        $stageName = isset($postData['stage_name']) ? $postData['stage_name'] : '';
        $pageSize = isset($postData['page_size']) ? $postData['page_size'] : 20;
        $data = $this->getStarList($page, $pageSize, $stageName, $subjectName);
        $data['item'] = [];
        if (array_key_exists('new_item', $data) && count($data['new_item']) !== 0) {
            foreach ($data['new_item'] as &$row) {
                $data['item'][] = [
                    'feed_name' => '',
                    'feed' => [
                        'user_id' => $row['user_id'],
                        'username' => $row['username'],
                        'rating' => $row['rating'],
                        'follower' => $row['follower'],
                        'status' => $row['status'],
                        'avatar' => $row['avatar'],
                        'profile' => $row['profile'],
                        'gender' => $row['gender'],
                        'characteristics' => $row['characteristics'],
                        'commentRating' => $row['commentRating'],
                        'teacherPopularity' => $row['teacherPopularity'],
                    ],
                ];
            }
        }
        return $data;
    }

    protected function getStarList($page = 1,
                                   $pageSize = 20,
                                   $stageName = '',
                                   $subjectName = '',
                                   $keyword = '')
    {
        $condition = [
            'v.user_id',
            'v.verify_time',
            'u.username',
            'u.status',
            't.nickname',
            't.avatar',
            't.profile',
            't.gender',
            't.characteristics',
            't.tags',
            't.teaching_age',
            'c.rating',
            'c.follower',
            'c.comment_num',
            'c.comment_sum_rating',
            'c.positive',
            'c.moderate',
            'c.negative',
            'c.question_num',
            'c.coureses_num',
            "group_concat(DISTINCT stages_id, '_', stages_name, '|', subjects_id, '_', subjects_name order by stages_id) stage_subject",
        ];
        $query = VerifyTeaching::find()
            ->select($condition)
            ->from(VerifyTeaching::tableName() . ' v')
            ->leftJoin(TeacherInfo::tableName() . ' t', 't.user_id=v.user_id')
            ->leftJoin(TchCount::tableName() . ' c', 'c.user_id=v.user_id')
            ->leftJoin(User::tableName() . ' u', 'u.user_id=v.user_id')
            ->where(['u.group_id' => 2, 'u.xstatus' => 1, 'v.flag' => 2, 'u.type' => 0])
            ->andWhere('v.subjects_id != 13')
            ->groupBy('user_id')
            ->orderBy('u.status DESC, (c.question_num + c.coureses_num * 10) DESC, c.rating DESC');
        if ($stageName != '全部' && $stageName != '') {
            $query->andWhere(['v.stages_name' => $stageName]);
        }
        if ($subjectName != '全部' && $subjectName != '') {
            $query->andWhere(['v.subjects_name' => $subjectName]);
        }
        if ($keyword != '') {
            $query->andWhere(['or', ['like', 'u.username', $keyword], ['like', 't.nickname', $keyword]]);
        }
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);
        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();
        $pageCount = $pages->getPageCount();
        $data['page'] = $page . '';
        $data['page_size'] = ($pages->limit) . '';
        $data['total_number'] = $pages->pageCount . '';
        $data['new_item'] = [];
        if ($page <= $pageCount) {
            foreach ($rows as &$row) {
                $row['tags'] = json_decode($row['tags'], true);
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

                $stageList = explode(',', $row['stage_subject']);
                $newStageList = [];
                foreach ($stageList as $item) {
                    list($stage, $subject) = explode('|', $item);
                    $newStageList[$stage][] = $subject;
                }
                $finalStageList = [];
                foreach ($newStageList as $key => $item) {
                    list($stage_id, $stage_name) = explode('_', $key);
                    $newItem = compact('stage_id', 'stage_name');
                    foreach ($item as $subject) {
                        list($subject_id, $subject_name) = explode('_', $subject);
                        $newItem['subjects'][] = compact('subject_id', 'subject_name');
                    }
                    $finalStageList[] = $newItem;
                }
                $row['stage_subject'] = $finalStageList;
            }
            $data['new_item'] = $rows;
        }
        return $data;
    }

    /**
     * 名师堂老师详情
     */
    public function actionUser()
    {
        $postData = Yii::$app->request->post();
        $user_id = $postData['user_id'];

        $condition = [
            'u.user_id',
            'u.username',
            'u.status',
            'u.regdate',
            't.nickname',
            't.avatar',
            't.profile',
            't.gender',
            't.characteristics',
            't.tags',
            't.teaching_age',
            'c.rating',
            'c.follower',
            'c.comment_num',
            'c.comment_sum_rating',
            'c.positive',
            'c.moderate',
            'c.negative',
            'c.question_num',
            'c.coureses_num',
        ];

        $row = User::find()
            ->select($condition)
            ->from(User::tableName() . ' u')
            ->leftJoin(TeacherInfo::tableName() . ' t', 't.user_id=u.user_id')
            ->leftJoin(TchCount::tableName() . ' c', 'c.user_id=u.user_id')
            ->where(['u.user_id' => $user_id, 'u.group_id' => 2, 'u.xstatus' => 1])
            ->asArray()
            ->one();
        if (count($row) === 0) {
            return [];
        }
        $row['tags'] = json_decode($row['tags'], true);
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
        $row['commentCount'] = $row['comment_num'];

        $numerator = $row['positive'];
        $denominator = $row['positive'] + $row['moderate'] + $row['negative'];
        if ($denominator == 0) {
            $teacherRating = 0;
        } else {
            $teacherRating = $numerator / $denominator;
            $teacherRating = round($teacherRating, 2);
        }
        $row['teacherPopularity'] = $teacherRating . '';
        $data = $row;

        $rows = VerifyTeaching::find()
            ->select([
                'stages_id stage_id',
                'stages_name stage_name',
                "group_concat(DISTINCT subjects_id, '|',subjects_name order by subjects_id) subjects",
            ])
            ->where(['user_id' => $user_id, 'flag' => 2])
            ->andWhere('subjects_id != 13')
            ->groupBy('stages_id')
            ->orderBy('stages_id')
            ->asArray()
            ->all();
        foreach ($rows as &$row) {
            $subjects = explode(',', $row['subjects']);
            foreach ($subjects as &$subject) {
                list($subject_id, $subject_name) = explode('|', $subject);
                $subject = compact('subject_id', 'subject_name');
            }
            $row['subjects'] = $subjects;
        }
        $data['stage_subject'] = $rows;

        //是否关注
        $login_user_id = Yii::$app->user->id;
        $follow = Friend::findOne(['fromId' => $login_user_id, 'toId' => $user_id]);
        $data['isFocus'] = $follow ? "1" : "0";//是否已经关注


        $ask = AskOrder::find()
            ->from(AskOrder::tableName() . ' a')
            ->where(['a.replies' => 1, 'a.answer_uid' => $user_id])
            ->orderBy('a.answer_add_time DESC')
            ->asArray();

        $monthAsk = clone $ask;
        $condition = [
            'a.order_id',
            'q.attach_info',
            'q.question_id',
            'a.answer_add_time',
            'q.grade_id',
            'q.subject_id',
            'q.subject_name',
            'q.question_title',
            'q.question_detail'
        ];
        $ask->select($condition);
        $ask->leftJoin(Question::tableName() . ' q', 'q.question_id=a.question_id');
        $ask->andWhere(['q.status' => 1]);

        $month = date('Y-m', time());
        $days = date("t", strtotime($month));
        $start = $month . '-01';
        $end = $month . '-' . $days;

        $allOrderCount = '0';
        $monthAskNum['month_question_num'] = '0';

        if ($ask) {
            $dateProvider = new ActiveDataProvider([
                'query' => $ask,
                'pagination' => [
                    'pageSize' => 3,
                ],
            ]);
            $post = $dateProvider->getModels();
            $allOrderCount = $dateProvider->getTotalCount();

            if ($post) {
                foreach ($post as $v) {
                    $rt['question_id'] = $v['question_id'];
                    $rt['question_title'] = isset($v['question_title']) ? $v['question_title'] : "";
                    $rt['question_detail'] = isset($v['question_detail']) ? $v['question_detail'] : "";
                    $rt['answer_add_time'] = F::friendlyDate($v['answer_add_time']);
                    $attach_info = json_decode($v['attach_info']);
                    if ($attach_info) {
                        $rt['attach_info'] = $attach_info;
                    } else {
                        $rt['attach_info'] = array(array("imgUrl" => "", "voice_url" => "", "voice_length" => ""));
                    }
                    $rt['stage_name'] = F::stg_id($v['grade_id']);
                    $rt['subject_name'] = $v['subject_name'];
                    $data['item'][] = $rt;
                }
            } else {
                $data['item'] = array();
            }

            $monthAsk->select('count(*) as month_question_num');
            $monthAsk->andWhere(['between', 'answer_add_time', $start, $end]);
            $monthAskNum = $monthAsk->one();
        } else {
            $data['item'] = array();
        }
        $data['question_num'] = "$allOrderCount";
        $data['month_question_num'] = $monthAskNum['month_question_num'];

        $microNum['micro_num'] = 0;
        $monthMicroNum['micro_num'] = 0;

        $micro = MicroCourse::find();
        $micro->select('count(*) as micro_num');
        $micro->where(['user_id' => $user_id, 'xstatus' => 1, 'publish' => 1, 'isauth' => 1]);
        $micro->asArray();
        $monthMicro = clone $micro;
        $microNum = $micro->one();
        if ($microNum['micro_num'] != 0) {
            $monthMicro->andWhere(['between', 'create_time', $start, $end]);
            $monthMicroNum = $monthMicro->one();
        }
        $data['micro_courses_num'] = $microNum['micro_num'];
        $data['month_micro_courses_num'] = $monthMicroNum['micro_num'];
        return $data;
    }

    /**
     * 名师堂中全部及时问
     * @return bool
     */
    public function actionQuestion()
    {
        $postData = Yii::$app->request->post();
        $user_id = $postData['user_id'];

        $ask = AskOrder::find()
            ->select([AskOrder::tableName() . '.order_id', Question::tableName() . '.attach_info', Question::tableName() . '.question_id', AskOrder::tableName() . '.answer_add_time', Question::tableName() . '.grade_id', Question::tableName() . '.subject_id', Question::tableName() . '.subject_name', Question::tableName() . '.question_title', Question::tableName() . '.question_detail'])
            ->leftJoin(Question::tableName(), AskOrder::tableName() . '.question_id' . '=' . Question::tableName() . '.question_id')
            ->where([AskOrder::tableName() . '.replies' => 1, 'answer_uid' => $user_id, Question::tableName() . '.status' => 1])
            ->orderBy('answer_add_time DESC');
        $ask->asArray();

        if ($ask) {
            $dateProvider = new ActiveDataProvider([
                'query' => $ask,
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);
            $post = $dateProvider->getModels();

            foreach ($post as $v) {
                $rt['question_id'] = $v['question_id'];
                $rt['question_title'] = isset($v['question_title']) ? $v['question_title'] : "";
                $rt['question_detail'] = isset($v['question_detail']) ? $v['question_detail'] : "";
                $rt['answer_add_time'] = F::friendlyDate($v['answer_add_time']);
                $attach_info = json_decode($v['attach_info']);
                if ($attach_info) {
                    $rt['attach_info'] = $attach_info;
                } else {
                    $rt['attach_info'] = array(array("imgUrl" => "", "voice_url" => "", "voice_length" => ""));
                }
                $rt['stage_name'] = F::stg_id($v['grade_id']);
                $rt['subject_name'] = $v['subject_name'];
                $data['item'][] = $rt;
            }
            $total = $dateProvider->pagination->getPageCount();
            $data['total_number'] = "$total";

        } else {
            $data['item'] = array();
        }

        return $data;
    }

    /*
     * 名师堂查看答案
     */
    public function actionAnswer()
    {
        $postData = Yii::$app->request->post();
        $user_id = Yii::$app->user->id;
        $question_id = $postData['question_id'];

        $question_post = QuestionPost::findOne(['qid' => $question_id, 'first' => 2]);

        if ($question_post) {
            $attach_info = Json::decode($question_post->attach_info);
            $img_url = isset($attach_info['img_url']) ? $attach_info['img_url'] : "";
            $voice_url = isset($attach_info['voice_url']) ? $attach_info['voice_url'] : "";
            $voice_length = isset($attach_info['voice_length']) ? $attach_info['voice_length'] : "";
            $img_url = isset($attach_info['thumbnail']) ? $attach_info['thumbnail'] : "";
            $src = isset($attach_info['src']) ? $attach_info['src'] : "";

            $arr_att_info['imgUrl'] = $img_url;
            $arr_att_info['voice_url'] = $voice_url;
            $arr_att_info['voice_length'] = $voice_length;
            $arr_att_info['vedio_url'] = $src;

            $data['attach_info'] = Json::encode($arr_att_info);
            $data['question_answer'] = $question_post->question_answer;
        } else {
            $data = '';
        }
        return $data;
    }

    /**
     * 老师搜索
     */
    public function actionSearch()
    {
        $param = Yii::$app->request->get();
        $page = isset($param['page']) ? $param['page'] : 1;
        $postData = Yii::$app->request->post();
        $subjectName = isset($postData['subject_name']) ? $postData['subject_name'] : '';
        $stageName = isset($postData['stage_name']) ? $postData['stage_name'] : '';
        $pageSize = isset($postData['page_size']) ? $postData['page_size'] : 20;
        $keyword = isset($postData['keyword']) ? $postData['keyword'] : '';
        if ($keyword == '') {
            return [];
        }
        $data = $this->getStarList($page, $pageSize, $stageName, $subjectName, $keyword);
        $data['item'] = [];
        if (array_key_exists('new_item', $data) && count($data['new_item']) !== 0) {
            foreach ($data['new_item'] as &$row) {
                $data['item'][] = [
                    'user_id' => $row['user_id'],
                    'username' => $row['username'],
                    'rating' => $row['rating'],
                    'follower' => $row['follower'],
                    'status' => $row['status'],
                    'avatar' => $row['avatar'],
                    'profile' => $row['profile'],
                    'gender' => $row['gender'],
                    'characteristics' => $row['characteristics'],
                    'commentRating' => $row['commentRating'],
                    'teacherPopularity' => $row['teacherPopularity'],
                ];
            }
        }
        return $data;
    }
}
