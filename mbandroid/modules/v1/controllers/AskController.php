<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 7/28/15
 * Time: 7:38 PM
 */
namespace mbandroid\modules\v1\controllers;

use common\components\CTask;
use common\components\Invite;
use common\components\JPushNotice;
use common\components\RedisStorage;
use common\models\AskAttach;
use common\models\AskOrder;
use common\models\CoinLog;
use common\models\Comment;
use common\models\CommonOrder;
use common\models\F;
use common\models\Favorites;
use common\models\Question;
use common\models\QuestionPost;
use common\models\Stgsubjects;
use common\models\StuCount;
use common\models\StudentInfo;
use common\models\User;
use common\models\VerifyTeaching;
use yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;

class  AskController extends ActiveController
{
    public $modelClass = 'common\models\Question';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'except' => ['create', 'view'],  // set actions for disable access!
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
            'only' => ['Delete', 'Update', 'Post', 'pad'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['Delete', 'Update', 'Post', 'pad'],
                    'roles' => ['@'],
                ]
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

    protected function getAskList($type = '')
    {
        $param = Yii::$app->request->get();
        $page = isset($param['page']) ? $param['page'] : 1;
        $postData = Yii::$app->request->post();
        $pageSize = isset($postData['page_size']) ? $postData['page_size'] : 20;
        $order_type = isset($postData['order_type']) ? $postData['order_type'] : 1;

        if ($type === 'pad') {
            $group_id = Yii::$app->user->identity->group_id;
            if ($group_id !== 2) {
                return ['status' => '6045', 'msg' => Yii::$app->params['q_6045']];
            }
        }
        $query = Question::find();
        $query->from(Question::tableName() . ' q');
        $query->select([
            'q.question_id',
            'question_title',
            'answer_add_time',
            'question_detail',
            'add_time',
            'subject_name',
            'published_uid',
            'published_username',
            'published_nickname',
            'attach_info',
            'grade_name',
            'replies',
            'order_sn',
            'order_id'
        ]);
        $query->leftJoin(AskOrder::tableName() . ' a', 'q.question_id=a.question_id');
        $query->leftJoin(Stgsubjects::tableName() . ' s', 's.grades_id=q.grade_id and s.subjects_id=q.subject_id');
        $query->where(['status' => 1, 'refund_status' => 4]);
        if ($type === 'pad') {
            $user_id = Yii::$app->user->identity->user_id;
            if (!isset($postData['subject_id'], $postData['stage_id'])) {
                return ['status' => '6046', 'msg' => Yii::$app->params['q_6046']];
            }
            $subject_id = $postData['subject_id'];
            $stage_id = $postData['stage_id'];
            if ($subject_id === '' || (int)$subject_id === -1) {
                if ($stage_id === '' || (int)$stage_id === -1) {
                    $rows = VerifyTeaching::find()
                        ->select([
                            'subjects_id subject_id',
                            'group_concat(DISTINCT stages_id order by stages_id) stage_id',
                        ])
                        ->where(['user_id' => $user_id])
                        ->groupBy('subjects_id')
                        ->asArray()
                        ->all();
                    if (count($rows) === 0) {
                        return [];
                    }
                    $condition = ['or'];
                    foreach ($rows as &$row) {
                        $condition[] = [
                            'q.subject_id' => $row['subject_id'],
                            's.stages_id' => explode(',', $row['stage_id']),
                        ];
                    }
                    $query->andWhere($condition);
                } else {
                    $row = VerifyTeaching::find()
                        ->select([
                            'group_concat(DISTINCT subjects_id order by subjects_id) subject_id',
                        ])
                        ->where(['user_id' => $user_id, 'stages_id' => $stage_id])
                        ->asArray()
                        ->one();
                    if (count($row) === 0) {
                        return [];
                    } else {
                        $subject_id = explode(',', $row['subject_id']);
                        $query->andWhere(['q.subject_id' => $subject_id, 's.stages_id' => $stage_id]);
                    }
                }
            } else {
                if ($stage_id === '' || (int)$stage_id === -1) {
                    $row = VerifyTeaching::find()
                        ->select([
                            'group_concat(DISTINCT stages_id order by stages_id) stage_id',
                        ])
                        ->where(['user_id' => $user_id, 'subjects_id' => $subject_id])
                        ->asArray()
                        ->one();
                    if (count($row) === 0) {
                        return [];
                    } else {
                        $stage_id = explode(',', $row['stage_id']);
                    }
                }
                $query->andWhere(['q.subject_id' => $subject_id, 's.stages_id' => $stage_id]);
            }
        } else {
            //compatible the old API
            if (isset($postData['subject']) && $postData['subject'] !== '全部') {
                $query->andWhere(['q.subject_name' => $postData['subject']]);
            }
            if (isset($postData['stage']) && $postData['stage'] !== '全部') {
                $query->andWhere(['s.stages_name' => $postData['stage']]);
            }
            //switch the new API
            if (isset($postData['subject_id']) && !empty($postData['subject_id'])) {
                $query->andWhere(['q.subject_id' => $postData['subject_id']]);
            }
            if (isset($postData['stage_id']) && !empty($postData['stage_id'])) {
                $query->andWhere(['s.stages_id' => $postData['stage_id']]);
            }
        }
        $query->andWhere('s.grades_id != 0');
        if ($order_type == 3) {
            $query->andWhere('a.replies = 1');
            $query->orderBy('answer_add_time desc');
        } else {
            $query->andWhere('a.replies = 0 AND a.order_status = 1');
            $query->orderBy('add_time desc');
        }
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);

        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();

        $pageCount = $pages->getPageCount();
        $data['total_number'] = $pageCount . '';
        $data['page'] = $page . '';
        $data['page_size'] = ($pages->limit) . '';
        if ($page <= $pageCount) {
            foreach ($rows as &$row) {
                $avatar = User::avatar($row['published_uid']);
                $row['avatar'] = $avatar['avatar'];
                $row['add_time'] = $row['add_time'] ? F::friendlyDate($row['add_time']) : '';
                $userinfo = RedisStorage::userinfo($row['published_uid']);
                $row['published_nickname'] = $userinfo['nickname'];
                $row['answer_timestamp'] = strtotime($row['answer_add_time']);
                $row['answer_add_time'] = $row['answer_add_time'] ? F::friendlyDate($row['answer_add_time']) : '';
                $attach_info = '';
                if (!empty($row['attach_info'])) {
                    $attach_info = json_decode($row['attach_info'], true);
                }
                if (!empty($attach_info)) {
                    if (!isset($attach_info[0])) {
                        $attach_info = [$attach_info];
                    }
                    $row['attach_info'] = $attach_info;
                } else {
                    $row['attach_info'] = [["imgUrl" => "", "voice_url" => "", "voice_length" => ""]];
                }
            }
            $data['item'] = $rows;
        } else {
            $data['item'] = [];
        }
        return $data;
    }

    /**
     * 获取问题列表
     * @return array
     */
    public function actionCreate()
    {
        return $this->getAskList();
    }

    /**
     * 在线答疑列表,老师专用
     * @return mixed
     */
    public function actionPad()
    {
        return $this->getAskList('pad');
    }

    /**
     * 删除我的及时问
     * @return array|bool
     */
    public function actionDelete()
    {
        $user_id = Yii::$app->user->identity->user_id;
        $group_id = Yii::$app->user->identity->group_id;
        $postData = Yii::$app->request->post();
        $question_id = isset($postData['question_id']) ? $postData['question_id'] : '';
        if (empty($question_id)) {
            return ['status' => '6028', 'msg' => Yii::$app->params['q_6028']];
        }
        $question = Question::findOne(['question_id' => $question_id]);
        $order = AskOrder::findOne(['question_id' => $question_id]);
        if (empty($order)) {
            return ['status' => '6032', 'msg' => Yii::$app->params['q_6032']];
        }
        if ($group_id == 1) {
            if ($question['published_uid'] != $user_id) {
                return ['status' => '6026', 'msg' => Yii::$app->params['q_6026']];
            }
            if ($order['s_is_del'] == 1) {
                return ['status' => '6033', 'msg' => Yii::$app->params['q_6033']];
            }
            $order['s_is_del'] = 1;
            if ($order->save()) {
                return ['status' => '200', 'msg' => 'ok'];
            } else {
                return ['status' => '6027', 'msg' => Yii::$app->params['q_6027']];
            }
        } else if ($group_id == 2) {
            if ($order['answer_uid'] != $user_id) {
                return ['status' => '6029', 'msg' => Yii::$app->params['q_6029']];
            }
            if ($order['t_is_del'] == 1) {
                return ['status' => '6034', 'msg' => Yii::$app->params['q_6034']];
            }
            $order['t_is_del'] = 1;
            if ($order->save()) {
                return ['status' => '200'];
            } else {
                return ['status' => '6030', 'msg' => Yii::$app->params['q_6030']];
            }
        } else {
            return ['status' => '6031', 'msg' => Yii::$app->params['q_6031']];
        }
    }

    /**
     * 问题详情和已经解答问题详情
     * @param $id
     */
    public function actionView($id)
    {
        $order_type = Yii::$app->request->getQueryParam('order_type');
        $user_id = Yii::$app->user->identity['user_id'] ? Yii::$app->user->identity['user_id'] : "";

        $query = QuestionPost::find();
        $query->from(QuestionPost::tableName() . ' p');
        $query->select([
            'question_id',
            'post_id',
            'p.first',
            'question_title',
            'question_detail',
            'add_time',
            'published_uid',
            'published_username',
            'status',
            'grade_id',
            'grade_name',
            'subject_id',
            'subject_name',
            'attach_info',
            'question_answer',
            'replies',
            'answer_uid',
            'answer_nickname',
            'answer_username',
            's_is_comment',
            't_is_comment',
            'order_id',
            'answer_add_time',
        ]);
        $query->leftJoin(AskOrder::tableName() . ' o', 'o.question_id = p.qid');
        if ($order_type) {
            $query->where(['qid' => $id]);
        } else {
            $query->where(['p.first' => 0, 'qid' => $id])->limit(1);
        }
        $post = $query->asArray()->all();

        foreach ($post as $val) {
            if ($val['first'] == 0) {
                $v['post_id'] = $val['post_id'];
                $v['question_id'] = $val['question_id'];
                $v['order_id'] = $val['order_id'];
                $v['first'] = $val['first'];
                $v['grade_id'] = $val['grade_id'];
                $v['subject_id'] = $val['subject_id'];
                $v['question_title'] = isset($val['question_title']) ? $val['question_title'] : "";
                $v['question_detail'] = isset($val['question_detail']) ? $val['question_detail'] : "";
                $v['add_time'] = isset($val['add_time']) ? F::friendlyDate($val['add_time']) : "";
                $v['published_uid'] = $val['published_uid'];
                $v['published_username'] = $val['published_username'];
                $userinfo = RedisStorage::userinfo($v['published_uid']);
                $v['published_nickname'] = $userinfo['nickname'];
                $v['subject_id'] = $val['subject_id'];
                $v['grade_name'] = isset($val['grade_name']) ? $val['grade_name'] : "";
                $v['subject_name'] = isset($val['subject_name']) ? $val['subject_name'] : "";
                $attach_info = '';
                if (!empty($val['attach_info'])) {
                    $attach_info = json_decode($val['attach_info'], true);
                }
                if (!empty($attach_info)) {
                    if (!isset($attach_info[0])) {
                        $attach_info = [$attach_info];
                    }
                    $v['attach_info'] = $attach_info;
                } else {
                    $v['attach_info'] = [["imgUrl" => "", "voice_url" => "", "voice_length" => ""]];
                }
                $v['answer_uid'] = isset($val['answer_uid']) ? $val['answer_uid'] : "";
                $v['answer_username'] = isset($val['answer_username']) ? $val['answer_username'] : "";
                $v['answer_nickname'] = isset($val['answer_nickname']) ? $val['answer_nickname'] : "";
                $userinfo = RedisStorage::userinfo($v['answer_uid']);
                $v['answer_nickname'] = $userinfo['nickname'];
                $v['replies'] = isset($val['replies']) ? $val['replies'] : "";
                $v['s_is_comment'] = isset($val['s_is_comment']) ? $val['s_is_comment'] : "";
                $v['t_is_comment'] = isset($val['t_is_comment']) ? $val['t_is_comment'] : "";
                $v['answer_add_time'] = isset($val['add_time']) ? F::friendlyDate($val['answer_add_time']) : "";
                $v['answer_timestamp'] = strtotime($val['answer_add_time']);
            }
            if ($val['first'] == 2) {
                $v['answer_uid'] = isset($val['published_uid']) ? $val['published_uid'] : "";
                $v['answer_username'] = isset($val['published_username']) ? $val['published_username'] : "";
                $userinfo = RedisStorage::userinfo($v['answer_uid']);
                $v['answer_nickname'] = $userinfo['nickname'];
                $v['answer_head'] = isset($v['answer_uid']) ? User::avatar($v['answer_uid'])['avatar'] : "";
                $v['answer_add_time'] = isset($val['add_time']) ? F::friendlyDate($val['add_time']) : "";
                $v['answer_timestamp'] = strtotime($val['answer_add_time']);
                //$val['attach_info'] = "";
                //$val['attach_info'] = '[{"imgUrl":"","voice_url":"","voice_length":""}]';
                //$val['attach_info'] = '{"imgUrl":"hooray-ask_yjbkv7ztca_1449636141_ios.jpeg","voice_url":"","voice_length":""}';
                //$val['attach_info'] = '{"video_url":"hooray-ask_z36gyqrf27_1449456316","video_size":0,"video_length":""}';
                //$val['attach_info'] = '{"first":{"src":"http://hooray-ask.hihooray.net/hooray-ask_3bgcu52sdf_1423558074.mp4","thumbnail":"","id":"166"}}';
                if (!empty($val['attach_info']) && $val['attach_info'] != '""') {
                    $answer_attach = json_decode($val['attach_info'], true);
                    $attach = isset($answer_attach['first']) ? $answer_attach['first'] : (count($answer_attach) == 1 && isset($answer_attach[0]) ? $answer_attach[0] : $answer_attach);
                    if (!isset($attach['img_url']) && isset($attach['imgUrl'])) {
                        $attach['img_url'] = $attach['imgUrl'];
                    }
                    $v['answer_attach_info'] = $attach;
                    if (!isset($v['answer_attach_info']['imgUrl']) && isset($attach['img_url'])) {
                        $v['answer_attach_info']['imgUrl'] = $attach['img_url'];
                    }
                    if (!isset($v['answer_attach_info']['video_url']) && isset($attach['src'])) {
                        $v['answer_attach_info']['video_url'] = $attach['src'];
                    }
                    if (isset($v['answer_attach_info']['video_url'])) {
                        if (!empty($v['answer_attach_info']['video_url'])
                            && strpos($v['answer_attach_info']['video_url'], 'http://') === false
                        ) {
                            switch (true) {
                                case strpos($v['answer_attach_info']['video_url'], 'hooray-ask') === 0:
                                    $v['answer_attach_info']['video_url'] = 'http://hooray-ask.hihooray.net/' . $v['answer_attach_info']['video_url'];
                                    break;
                                case strpos($v['answer_attach_info']['video_url'], 'hooray-weike') === 0:
                                    $v['answer_attach_info']['video_url'] = 'http://hooray-weike.hihooray.net/' . $v['answer_attach_info']['video_url'];
                                    break;
                            }
                        }
                        //兼容
                        $v['answer_attach_info']['vedio_url'] = $v['answer_attach_info']['video_url'];
                    }
                } else {
                    $v['answer_attach_info'] = json_decode('{"imgUrl":"","img_url":"","voice_url":"","voice_length":"","video_url":"","video_length":"","vedio_url":"","vedio_length":""}', true);
                }
                $v['question_answer'] = isset($val['question_answer']) ? $val['question_answer'] : "";
            } elseif ($val['first'] == 3) {
                $v['teacher_append_answer'] = isset($val['question_answer']) ? $val['question_answer'] : "";
                $v['teacher_append_answer_datetime'] = isset($v['add_time']) ? F::friendlyDate($val['add_time']) : "";
            }
        }

        $v['teacher_append_answer'] = isset($v['teacher_append_answer']) ? $v['teacher_append_answer'] : "";

        $comment = Comment::find()
            ->select('title, content, comment_rating, describe_teacher, create_time')
            ->where(['target' => $id])
            ->asArray()
            ->one();

        $v['content'] = isset($comment['content']) ? $comment['content'] : "";
        $v['rating'] = isset($comment['comment_rating']) ? $comment['comment_rating'] : "";
        $v['comment_time'] = isset($comment['create_time']) ? F::friendlyDate($comment['create_time']) : "";
        $describe_teacher = isset($comment['describe_teacher']) ? $comment['describe_teacher'] : "";

        $describeArray = array();
        $TeachingArray = Yii::$app->params['CommentTeaching'];
        foreach ($TeachingArray as $describe) {
            if (strpos($describe_teacher, $describe['id']) !== false) { //找到对老师描述项目
                $describeArray[] = array("content" => $describe['content']);
            }
        }
        $v['describe'] = array("data" => $describeArray);

        $v['published_head'] = isset($v['published_uid']) ? User::avatar($v['published_uid'])['avatar'] : "";

        //是否收藏  1 收藏，0 未收藏
        if ($user_id) {
            $favorite = Favorites::findOne(['resource_id' => $id, 'user_id' => $user_id, 'resource_type' => 'ask', 'status' => 0]);
            if ($favorite) {
                $v['isfav'] = '1';
            } else {
                $v['isfav'] = '0';
            }
        } else {
            $v['isfav'] = '0';
        }
        return $v;
    }

    /**
     * 提交问题
     * @return array|string
     * @throws yii\db\Exception
     */
    public function actionPost()
    {
        $postData = Yii::$app->request->post();
//        $user_id = Yii::$app->user->getId();
        $user_id = Yii::$app->user->identity->user_id;
        $username = Yii::$app->user->identity->username;
        $nowtime = date('Y-m-d H:i:s');
        $group_id = Yii::$app->user->identity->group_id;
        //学生才能提问
        if ($group_id != 1) {
            $data['status'] = '6008';
            $data['msg'] = Yii::$app->params['q_6008'];
            return $data;
        }
        //判断哇哇豆不够
        $stu_coin = StuCount::find()->select('coin')->where(['user_id' => $user_id])->one();
        if ($stu_coin->coin < Yii::$app->params['mincoin']) {
            return ['status' => '6009', 'msg' => Yii::$app->params['q_6009']];
        }
        $data = json_encode($postData);
        $attach_info = '';
        if (!empty($postData['img_url']) || !empty($postData['voice_url']) || !empty($postData['voice_length'])) {
            $attach_info = json_encode([
                [
                    'imgUrl' => $postData['img_url'],
                    'voice_url' => $postData['voice_url'],
                    'voice_length' => $postData['voice_length']
                ]
            ]);
        }
        if (!isset($postData['answer_username'],
            $postData['img_url'],
            $postData['img_size'],
            $postData['voice_url'],
            $postData['voice_size'],
            $postData['voice_length'],
            $postData['subject_id'],
            $postData['question_type_id'],
            $postData['grade_name'],
            $postData['grade_id'],
            $postData['subject_name'],
            $postData['question_type_name'],
            $postData['reward'],
            $postData['question_title'],
            $postData['answer_uid'])
        ) {
            return ['status' => '6010', 'msg' => Yii::$app->params['q_6010']];
        }
        if ($postData['grade_name'] == '全部' || $postData['subject_name'] == '全部') {
            return ['status' => '6040', 'msg' => Yii::$app->params['q_6040']];
        }
        if (!empty($postData['answer_uid'])) {
            $verify = VerifyTeaching::find()
                ->from(VerifyTeaching::tableName() . ' v')
                ->leftJoin(Stgsubjects::tableName() . ' s', 's.stages_id = v.stages_id and s.subjects_id = v.subjects_id')
                ->where([
                    'user_id' => $postData['answer_uid'],
                    'v.subjects_id' => $postData['subject_id'],
                    's.grades_id' => $postData['grade_id'],
                ])
                ->count();
            if (empty($verify)) {
                return ['status' => '6043', 'msg' => Yii::$app->params['q_6043']];
            }
            $num = AskOrder::find()->where([
                'answer_uid' => $postData['answer_uid'],
                'replies' => [0, 2],
                'appoint_type_id' => 1
            ])->count();
            if ($num >= 5) {
                return ['status' => '6044'];
            }
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $Question = new Question ();
            $Question_post = new QuestionPost ();
            $post = new AskAttach();
            $Order = new AskOrder();
            $CommonOrder = new CommonOrder();

            $Question_post->published_uid = $Question->published_uid = $user_id;
            $Question_post->published_nickname = $Question->published_nickname = '';
            $Question_post->published_username = $Question->published_username = $username;
            $Question_post->grade_id = $Question->grade_id = $postData['grade_id'];
            $Question_post->subject_id = $Question->subject_id = $postData['subject_id'];
            $Question_post->question_type_id = $Question->question_type_id = $postData['question_type_id'];
            $Question_post->grade_name = $Question->grade_name = $postData['grade_name'];
            $Question_post->subject_name = $Question->subject_name = $postData['subject_name'];
            $Question_post->question_type_name = $Question->question_type_name = $postData['question_type_name'];
            $Question_post->reward = $Question->reward = $postData['reward'];
            $Question_post->expired_time = $Question->expired_time = date('Y-m-d H:i:s', time() + (20 * 365 * 24 * 3600));
            $Question_post->question_title = $Question->question_title = $postData['question_title'];
            $Question_post->question_detail = $Question->question_detail = "";
            $Question_post->attach_info = $Question->attach_info = $attach_info;
            $Question_post->add_time = $Question->add_time = $nowtime;
            $Question_post->update_time = $Question->update_time = $nowtime;
            $has_attach = '1';

            if (empty($postData ['img_url']) && empty($postData ['voice_url'])) {
                $has_attach = '0';
            }
            $Question_post->has_attach = $Question->has_attach = $has_attach;
            $Question->save();
            $qid = $Question->attributes['question_id'];

            $Question_post->qid = $qid;

            if (!empty($postData ['img_url'])) {
                $post->file_location = $postData ['img_url'];
                $post->file_name = '';
                $post->file_size = $postData ['img_size'];
                $post->file_type = '2';
                $post->add_time = $nowtime;
                $post->published_uid = $user_id;
                $post->question_id = $qid;
                $post->save();
            }

            if (!empty($postData ['voice_url'])) {
                $post->file_location = $postData ['voice_url'];
                $post->file_name = '';
                $post->file_size = $postData ['voice_size'];
                $post->file_type = '1';
                $post->add_time = $nowtime;
                $post->published_uid = $user_id;
                $post->question_id = $qid;
                $post->save();
            }

            $order_sn = F::generateOrderSn('qa');
            // 将题目写入订单中
            $Order->question_id = $qid;
            $Order->order_sn = $order_sn;
            $Order->order_time = $nowtime;
            $Order->refund_status = isset($postData ['reward']) ? 4 : 0;
            if (array_key_exists('answer_uid', $postData) && (string)$postData['answer_uid'] !== '') {
                $Order->answer_uid = $postData['answer_uid'];
                $redis_storage = new RedisStorage();
                $user_info = $redis_storage->user($postData['answer_uid']);
                $Order->answer_username = $user_info['username'];
                $Order->order_status = '2';
                $Order->replies = '2';
                $Order->appoint_type_id = '1';
                $Order->acquire_time = $nowtime;
            }

            //将题目写入公共的订单中
            $CommonOrder->order_id = $order_sn;
            $CommonOrder->user_id = $user_id;
            $CommonOrder->title = $postData ['question_title'];
            $CommonOrder->order_type = '0';
            $CommonOrder->price = $postData ['reward'];
            $CommonOrder->createtime = $nowtime;
            $CommonOrder->status = '3';
            $CommonOrder->data = $data;

            //更新哇哇豆日志
            $coin_log = new CoinLog();
            $coin_log->user_id = $user_id;
            $coin_log->order_id = $order_sn;
            $coin_log->order_type = '0';
            $coin_log->nums = $postData ['reward'];
            $coin_log->type = '0';
            $coin_log->remark = "$username 提问消费哇哇豆.($postData[reward])";

            //更新学生的哇哇豆
            $stu_coin_rt = StuCount::updateAllCounters(
                [
                    'coin' => '-' . $postData ['reward'],
                    'lock_coin' => '-' . $postData['reward']
                ],
                [
                    'and',
                    ['user_id' => $user_id],
                    ['>=', 'coin', $postData['reward']]
                ]
            );

            if ($Question_post->save() &&
                $Order->save() &&
                $CommonOrder->save() &&
                $coin_log->save() &&
                $stu_coin_rt
            ) {

                $transaction->commit();

                CTask::done($user_id, 1, 'ask');
                //发送消息
                $jpush = new JPushNotice();
                $jpush->send(array(
                    $user_id
                ), array(
                    'type' => '3001',
                    'title' => '您的提问《' . $postData['question_title'] . '》已发布成功，系统已扣费' . $postData ['reward'] . '哇哇豆!',
                    'time' => date('Y-m-d H:i:s')
                ));
                Invite::step($user_id, 1);

                //发消息给老师
                if (!empty($postData['answer_uid'])) {
                    $user = StudentInfo::findOne(['user_id' => $user_id]);
                    $nickname = '';
                    if (!empty($user)) {
                        $nickname = $user->nickname;
                    }
                    $push_username = $nickname !== '' ? $nickname : $username;
                    $jpush->send(
                        [$postData['answer_uid']],
                        [
                            'type' => '3032',
                            'title' => '有学生向您提问',
                            'username' => $push_username,
                            'grade_name' => $postData['grade_name'],
                            'subject_name' => $postData['subject_name'],
                        ]
                    );
                }
                return ['order_sn' => $order_sn];
            } else {
                $transaction->rollBack();
                return ['status' => '6010', 'msg' => Yii::$app->params['q_6010']];
            }
        } catch (yii\base\ErrorException $e) {
            $transaction->rollBack();
            return ['status' => '6010', 'msg' => Yii::$app->params['q_6010']];
        }
    }
}
