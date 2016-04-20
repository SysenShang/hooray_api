<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 10/22/15
 * Time: 3:36 PM
 */

namespace mbandroid\modules\v1\controllers;

use common\components\RedisStorage;
use common\models\AskOrder;
use common\models\F;
use common\models\Question;
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


class MyAskAnswerController extends ActiveController
{
    public $modelClass = 'common\models\Question';

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
            'only' => ['Create'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['Create'],
                    'roles' => ['@'],
                ],
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

    public function actionCreate()
    {
        $user_id = Yii::$app->user->identity->user_id;
        $group_id = Yii::$app->user->identity->group_id;
        $postData = Yii::$app->request->post();
        $type_id = isset($postData['type_id']) ? $postData['type_id'] : 0;//1全部 2未解答 3已解答 4：待评价
        $pageSize = isset($postData['page_size']) ? $postData['page_size'] : 20;
        if (empty($pageSize)) {
            $pageSize = 20;
        }

        $query = Question::find();
        $query->select([
            'q.question_id',
            'q.question_title',
            'q.question_detail',
            'q.add_time',
            'q.published_username',
            'q.published_nickname',
            'q.published_uid',
            'q.attach_info',
            'q.grade_id',
            'replies',
            'confrim',
            's_is_comment',
            'refund_status',
            'reward',
            'order_id',
            'order_sn',
            'grade_name',
            'subject_name',
            'answer_add_time',
            'answer_username',
            'answer_nickname',
            'classroom_id'
        ]);
        $query->from(Question::tableName() . ' q');
        $query->leftJoin(AskOrder::tableName() . ' a', 'a.question_id = q.question_id');
        if ($group_id == 1) {
            switch ($type_id) {
                case 1:
                    $query->where(['q.published_uid' => $user_id, 'q.status' => 1, 'a.s_is_del' => 0]);
                    $query->orderBy('q.add_time DESC');
                    break;
                case 2:
                    $query->where([
                        'AND',
                        ['q.published_uid' => $user_id, 'q.status' => 1, 'a.s_is_del' => 0],
                        ['IN', 'a.replies', [0, 2]]
                    ]);
                    $query->orderBy('q.add_time DESC');
                    break;
                case 3:
                    $query->where([
                        'q.published_uid' => $user_id,
                        'a.replies' => 1,
                        'q.status' => 1,
                        'a.s_is_del' => 0
                    ]);
                    $query->orderBy('a.answer_add_time DESC');
                    break;
                case 4:
                    $query->where([
                        'q.published_uid' => $user_id,
                        'a.replies' => 1,
                        'a.confrim' => 2,
                        'a.s_is_comment' => 0,
                        'q.status' => 1,
                        'a.s_is_del' => 0
                    ]);
                    $query->orderBy('a.answer_add_time DESC');
                    break;
                case 5:
                    $query->where([
                        'q.published_uid' => $user_id,
                        'a.replies' => 1,
                        'a.confrim' => 2,
                        'a.s_is_comment' => 1,
                        'q.status' => 1,
                        'a.s_is_del' => 0
                    ]);
                    $query->orderBy('a.answer_add_time DESC');
                    break;
                default:
                    return ['status' => '6047', 'msg' => Yii::$app->params['q_6047']];
            }
        }
        if ($group_id == 2) {
            switch ($type_id) {
                case 1:
                    $query->where([
                        'a.t_status' => 1,
                        'a.refund_status' => 4,
                        'a.first' => 0,
                        'a.answer_uid' => $user_id,
                        'q.status' => 1,
                        'a.t_is_del' => 0
                    ]);
                    $query->orderBy('q.add_time DESC');
                    break;
                case 2:
                    $query->where([
                        'AND',
                        [
                            'a.t_status' => 1,
                            'a.refund_status' => 4,
                            'a.first' => 0,
                            'a.answer_uid' => $user_id,
                            'q.status' => 1,
                            'a.t_is_del' => 0
                        ],
                        [
                            'IN',
                            'a.replies',
                            [0, 2]
                        ]
                    ]);
                    $query->orderBy('q.add_time DESC');
                    break;
                case 3:
                    $query->where([
                        'a.t_status' => 1,
                        'a.refund_status' => 4,
                        'a.first' => 0,
                        'a.answer_uid' => $user_id,
                        'a.replies' => 1,
                        'q.status' => 1,
                        'a.t_is_del' => 0
                    ]);
                    $query->orderBy('a.answer_add_time DESC');
                    break;
                case 4:
                    $query->where([
                        'a.answer_uid' => $user_id,
                        'a.replies' => 1,
                        'a.confrim' => 2,
                        'a.s_is_comment' => 0,
                        'q.status' => 1,
                        'a.t_is_del' => 0
                    ]);
                    $query->orderBy('a.answer_add_time DESC');
                    break;
                case 5:
                    $query->where([
                        'a.answer_uid' => $user_id,
                        'a.replies' => 1,
                        'a.confrim' => 2,
                        'a.s_is_comment' => 1,
                        'q.status' => 1,
                        'a.t_is_del' => 0
                    ]);
                    $query->orderBy('a.answer_add_time DESC');
                    break;
                default:
                    return ['status' => '6047', 'msg' => Yii::$app->params['q_6047']];
            }
        }
        $query->asArray()->all();
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);

        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();

        $data = [];
        foreach ($rows as $val) {
            $avatar = User::avatar($val['published_uid']);
            $v['avatar'] = $avatar['avatar'];
            $v['question_id'] = $val['question_id'] ? $val['question_id'] : "";
            $v['question_title'] = $val['question_title'] ? $val['question_title'] : "";
            $v['question_detail'] = $val['question_detail'] ? $val['question_detail'] : "";
            $v['stages_name'] = $val['grade_id'] ? F::stg_id($val['grade_id']) : "";
            $v['subject_name'] = $val['subject_name'] ? $val['subject_name'] : "";
            $v['grade_name'] = $val['grade_name'] ? $val['grade_name'] : "";
            $v['add_time'] = $val['add_time'] ? F::friendlyDate($val['add_time']) : "";
            $v['answer_add_time'] = $val['answer_add_time'] ? F::friendlyDate($val['answer_add_time']) : "";
            $v['answer_timestamp'] = strtotime($val['answer_add_time']);
            $v['published_username'] = $val['published_username'] ? $val['published_username'] : "";
            $v['published_uid'] = $val['published_uid'] ? $val['published_uid'] : "";
            $userinfo = RedisStorage::userinfo($v['published_uid']);
            $v['published_nickname'] = $userinfo['nickname'];
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
            $v['replies'] = $val['replies'] ? $val['replies'] : "";
            $v['confrim'] = $val['confrim'] ? $val['confrim'] : "";
            $v['s_is_comment'] = $val['s_is_comment'] ? $val['s_is_comment'] : "";
            $v['refund_status'] = $val['refund_status'] ? $val['refund_status'] : "";
            $v['reward'] = $val['reward'] ? $val['reward'] : "";
            $v['order_id'] = $val['order_id'] ? $val['order_id'] : "";
            $v['order_sn'] = $val['order_sn'] ? $val['order_sn'] : "";
            $v['answer_username'] = $val['answer_username'] ? $val['answer_username'] : "";
            $v['answer_nickname'] = $val['answer_nickname'] ? $val['answer_nickname'] : "";
            $v['classroom_id'] = $val['classroom_id'] ? $val['classroom_id'] : "";

            $data['item'][] = $v;
        }

        if ($pages->pageCount) {
            $data['total_number'] = $pages->pageCount ? "$pages->pageCount" : "";
        } else {
            $data['item'] = array();
        }
        return $data;
    }
}
