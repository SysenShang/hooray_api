<?php
namespace mbandroid\modules\v1\controllers;


use common\models\AskOrder;
use common\models\Question;
use common\models\Scrap;
use common\models\Stgsubjects;
use common\models\TchVerify;
use common\models\TeacherInfo;
use common\models\VerifyTeaching;
use yii;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;

class OrderAskController extends ActiveController
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
            'only' => ['Index', 'Create', 'Delete', 'Update', 'View', 'Post'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['Index', 'Create', 'Delete', 'Update', 'View', 'Post'],
                    'roles' => ['@'],
                ],
                [
                    'allow' => true,
                    'actions' => ['View'],
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

    public function actionCreate()
    {
        $postData = Yii::$app->request->post();
        $user_id = Yii::$app->user->identity['user_id'];
        $username = Yii::$app->user->identity['username'];

        $tchVerify = TchVerify::findOne(['user_id' => $user_id]);
        if (empty($tchVerify) || ($tchVerify->verify4 != 2) || ($tchVerify->verify5 != 2)) {
            return ['status' => '6001', 'msg' => Yii::$app->params['q_6001']];
        }

        $question_id = $postData['question_id'];
        if (empty($question_id)) {
            return ['status' => '6036', 'msg' => Yii::$app->params['q_6036']];
        }

        $question = Question::findOne(['question_id' => $question_id, 'status' => 1]);
        if (empty($question)) {
            return ['status' => '6037', 'msg' => Yii::$app->params['q_6037']];
        }
        if ($question['published_uid'] == $user_id) {
            return ['status' => '6002', 'msg' => Yii::$app->params['q_6002']];
        }

        $order = AskOrder::findOne(['question_id' => $question_id, 'first' => 0]);
        if (empty($order)) {
            return ['status' => '6038', 'msg' => Yii::$app->params['q_6038']];
        }
        if (!empty($order['answer_uid'])) {
            return ['status' => '6003', 'msg' => Yii::$app->params['q_6003']];
        }

        $verify = VerifyTeaching::find()
            ->from(VerifyTeaching::tableName() . ' v')
            ->leftJoin(Stgsubjects::tableName() . ' s', 's.stages_id = v.stages_id and s.subjects_id = v.subjects_id')
            ->where([
                'user_id' => $user_id,
                'v.subjects_id' => $question->subject_id,
                's.grades_id' => $question->grade_id
            ])
            ->count();
        if (empty($verify)) {
            return ['status' => '6039', 'msg' => Yii::$app->params['q_6039']];
        }

        $tchinfo = TeacherInfo::findOne(['user_id' => $user_id]);
        $nickname = isset($tchinfo['nickname']) ? $tchinfo['nickname'] : "";

        //问答超过次数
        $scrap_count = Scrap::find()->where(['first' => 0, 'answer_uid' => $user_id, 'TO_DAYS(add_time)' => new Expression('TO_DAYS(NOW())')])->count();
        if ($scrap_count >= Yii::$app->params['tchmaxjiedan']) {
            return ['status' => '6004', 'msg' => Yii::$app->params['q_6004']];
        }

        $query = AskOrder::find();
        $query->select(['q.question_id']);
        $query->from(AskOrder::tableName() . ' a');
        $query->leftJoin(Question::tableName() . ' q', 'q.question_id = a.question_id');
        $query->where(['answer_uid' => $user_id, 'order_status' => 2, 'replies' => 2, 'q.status' => 1]);
        $count = $query->count();
        if ($count > 0) {
            return ['status' => '6041', 'msg' => Yii::$app->params['q_6041']];
        }

        //更新订单
        $order->answer_uid = $user_id;
        $order->answer_nickname = $nickname;
        $order->answer_username = $username;
        $order->acquire_time = date('Y-m-d H:i:s');
        $order->replies = '2';
        $order->order_status = '2';
        if ($order->save()) {
            return ['status' => '200', 'msg' => '开始解答问题'];
        } else {
            return ['status' => '6017', 'msg' => Yii::$app->params['q_6017']];
        }
    }

    /**
     * insert classroom Id
     * @param $id
     * @return array
     */
    public function actionUpdate($id)
    {
        $postData = yii::$app->request->post();
        $classroomId = $postData['classroom_id'];
        $order = AskOrder::findOne(['order_id' => $id]);
        $order->classroom_id = $classroomId;

        if ($order->save()) {
            return ['status' => '200', 'msg' => '保存成功'];
        } else {
            return ['status' => '6017', 'msg' => '保存失败！'];
        }
    }
}
