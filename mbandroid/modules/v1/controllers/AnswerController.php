<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 10/16/15
 * Time: 8:29 PM
 */

namespace mbandroid\modules\v1\controllers;

use common\components\CTask;
use common\components\JPushNotice;
use common\models\AnswerAttach;
use common\models\AskOrder;
use common\models\Comment;
use common\models\CreditRule;
use common\models\Question;
use common\models\QuestionPost;
use common\models\TchCount;
use yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\AccessControl;

class AnswerController extends ActiveController
{
    public $modelClass = 'common\models\Question';


    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'except' => ['index'],  // set actions for disable access!
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['Create', 'Evaluation', 'sub-eva'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['Create', 'Evaluation', 'sub-eva'],
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

        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view'], $actions['put'], $actions['head']);

        //触发解答后24小时自动确认和评价
        proc_close(proc_open('php '.__DIR__.'/../../../../console/yii ConfirmAnswer/auto &', [], $foo));

        return $actions;
    }

    /**
     * 触发开始答题通知
     * @author grg
     */
    public function actionBegin()
    {
        $postData = Yii::$app->request->post();
        $username = Yii::$app->user->identity->username;
        $question = Question::find()
                            ->select('question_title,published_uid')
                            ->where(['question_id' => $postData['question_id']])
                            ->asArray()
                            ->limit(1)
                            ->one();
        $jpush    = new JPushNotice();
        $msg      = $username . '老师开始解答你的问题：' . $question['question_title'];
        $jpush->send([$question['published_uid']], ['type' => '1000', 'title' => $msg]);
        //update answer begin time
        AskOrder::updateAll(['answer_begin_time' => date('Y-m-d H:i:s')], ['question_id' => $postData['question_id'], 'first' => 0]);
    }

    /**
     *  回答问题
     */
    public function actionCreate()
    {
        $postData = Yii::$app->request->post();

        $question_id     = $postData['question_id'];
        $question_answer = isset($postData['question_answer']) ? $postData['question_answer'] : "";
        $date            = date('Y-m-d H:i:s');
        $user_id         = Yii::$app->user->id;
        $username        = Yii::$app->user->identity['username'];

        if(isset($postData['post_type']) && $postData['post_type'] == "3") {
            $question=Question::findOne(['question_id' => $question_id]);
            $question_post                     = new QuestionPost();
            $question_post->qid                = $question_id;
            $question_post->first              = isset($postData['post_type']) ? $postData['post_type'] : "2"; // 2 回答题目,3 Teacher's append answer
            $question_post->published_uid      = $user_id;
            $question_post->published_nickname = $username;
            $question_post->published_username = $username;
            $question_post->grade_id           = $question->grade_id;
            $question_post->subject_id         = $question->subject_id;
            $question_post->question_type_id   = $question->question_type_id;
            $question_post->question_type_name = $question->question_type_name;
            $question_post->grade_name         = $question->grade_name;
            $question_post->subject_name       = $question->subject_name;
            $question_post->reward             = $question->reward;
            $question_post->question_title     = $question->question_title;
            $question_post->question_answer    = $question_answer;
            $question_post->update_time        = $question_post->add_time = $date;

            if ($question_post->save()) {
                $data['status'] = '200';
                $data['msg']    = 'ok';
                return $data;
            }
        } else {
            $ask_order = AskOrder::findOne(['answer_uid' => $user_id, 'question_id' => $question_id]);

            if (!$ask_order) {
                $data['status'] = '6006';
                $data['msg']    = Yii::$app->params['q_6006'];
                return $data;
            }
            if ($ask_order->replies == 1) {
                $data['status'] = '6007';
                $data['msg']    = Yii::$app->params['q_6007'];
                return $data;
            }

            $question = $ask_order->question;
            if (!$question || $question->status == 0) {
                $data['status'] = '6005';
                $data['msg']    = Yii::$app->params['q_6005'];
                return $data;
            }

            $question_post                     = new QuestionPost();
            $question_post->qid                = $question_id;
            $question_post->first              = '2'; // 2 回答题目
            $question_post->published_uid      = $user_id;
            $question_post->published_nickname = $username;
            $question_post->published_username = $username;
            $question_post->grade_id           = $question->grade_id;
            $question_post->subject_id         = $question->subject_id;
            $question_post->question_type_id   = $question->question_type_id;
            $question_post->question_type_name = $question->question_type_name;
            $question_post->grade_name         = $question->grade_name;
            $question_post->subject_name       = $question->subject_name;
            $question_post->reward             = $question->reward;
            $question_post->question_title     = $question->question_title;
            $question_post->question_answer    = $question_answer;
            $question_post->update_time        = $question_post->add_time = $date;

            $answer_attach                = new AnswerAttach();
            $answer_attach->add_time      = $date;
            $answer_attach->published_uid = $user_id;
            $answer_attach->question_id   = $question_id;
            $answer_attach->order_id      = (string)$ask_order['order_id'];
            $answer_attach->file_name     = '';

            $attach_info = [];
            $has_attach  = 0;
            if (isset($postData['img_url'])) {
                $answer_attach->file_location = $postData['img_url'];
                $answer_attach->file_size     = isset($postData['img_size']) ? (string)$postData['img_size'] : '0';
                $answer_attach->file_type     = '2';

                $has_attach              = 1;
                $attach_info['img_url']  = $postData['img_url'];
                $attach_info['img_size'] = isset($postData['img_size']) ? (string)$postData['img_size'] : '0';
            }
            if (isset($postData['voice_url'])) {
                $answer_attach->file_location = $postData['voice_url'];
                $answer_attach->file_size     = isset($postData['voice_size']) ? (string)$postData['voice_size'] : '0';
                $answer_attach->file_type     = '1';

                $has_attach                  = 1;
                $attach_info['voice_url']    = $postData['voice_url'];
                $attach_info['voice_size']   = isset($postData['voice_size']) ? (string)$postData['voice_size'] : '0';
                $attach_info['voice_length'] = isset($postData['voice_length']) ? (string)$postData['voice_length'] : '';
            }
            if (isset($postData['video_url'])) {
                $answer_attach->file_location = $postData['video_url'];
                $answer_attach->file_size     = isset($postData['video_size']) ? (string)$postData['video_size'] : '0';
                $answer_attach->file_type     = '3';

                $has_attach                  = 1;
                $attach_info['video_url']    = $postData['video_url'];
                $attach_info['video_size']   = isset($postData['video_size']) ? (string)$postData['video_size'] : '0';
                $attach_info['video_length'] = isset($postData['video_length']) ? (string)$postData['video_length'] : '';
            }

            $question_post->has_attach  = $has_attach;
            $question_post->attach_info = $has_attach ? json_encode($attach_info) : '';

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!($question_post->save())) {
                    $transaction->rollBack();
                    return ['status' => '6019', 'msg' => Yii::$app->params['q_6019']];
                }
                if (!($answer_attach->save())) {
                    $transaction->rollBack();
                    return ['status' => '6020', 'msg' => Yii::$app->params['q_6020']];
                }
                $result = AskOrder::updateAll([
                    'answer_add_time' => $date,
                    'replies' => 1
                ], ['question_id' => $question_id, 'first' => 0]);
                if (!$result) {
                    $transaction->rollBack();
                    return ['status' => '6025', 'msg' => Yii::$app->params['q_6025']];
                }
                $transaction->commit();
                $jpush = new JPushNotice();
                $jpush->send([
                    $question['published_uid']
                ], [
                        'type' => '3009',
                        'question_id' => $question_id,
                        'order_id' => $ask_order['order_id'],
                        'question_title' => $question['question_title'],
                        'question_detail' => $question['question_detail'],
                        'attach_info' => $question['attach_info'],
                        'title' => '您的提问《' . $question['question_title'] . '》已被解答！',
                        'time' => $date
                    ]);
                return ['status' => '200', 'msg' => 'ok'];
            }  catch (yii\db\Exception $e) {
                $transaction->rollBack();
                return ['status' => '6018', 'msg' => Yii::$app->params['q_6018']];
            }
        }
    }

    /*
     * 问题评价
     */
    public function actionEvaluation()
    {
        $data = Yii::$app->params['CommentTeaching'];
        return $data;
    }

    /**
     * 问题评价提交
     */
    public function actionSubEva()
    {
        $postData = Yii::$app->request->post();
        $question_rating = isset($postData['question_rating']) ? abs(intval($postData['question_rating'])) : "";
        $question_id = isset($postData['question_id']) ? $postData['question_id'] : "";
        $content = isset($postData['content']) ? $postData['content'] : "";
        $comment_teaching = isset($postData['comment_teaching']) ? $postData['comment_teaching'] : "";
        $order_id = isset($postData['order_id']) ? $postData['order_id'] : "";
        $date = date('Y-m-d H:i:s');
        $group_id = Yii::$app->user->identity->group_id;

        if (empty($question_rating) || empty($question_id) || empty($comment_teaching) || empty($order_id) || $question_rating > 5) {
            Yii::$app->response->statusCode = 400;
        }

        $user_id = Yii::$app->user->id;
        $username = Yii::$app->user->identity->username;

        $ask_order = AskOrder::find()
            ->select(Question::tableName() . '.question_id,question_title,answer_uid,answer_username,confrim,s_is_comment')
            ->leftJoin(Question::tableName(), AskOrder::tableName() . '.question_id' . '=' . Question::tableName() . '.question_id')
            ->where(['order_id' => $order_id, 'first' => 0])->asArray()->one();

        if ($ask_order['confrim'] == 1 || $ask_order['s_is_comment'] == 1) {
            $data['status'] = '6014';
            $data['msg'] = Yii::$app->params['q_6014'];

            return $data;
        }
        if(Yii::$app->params['teacher_comment']['isFixStar']) {
            $question_rating = Yii::$app->params['teacher_comment']['star'];
        }

        $comment = new Comment();
        $comment->student_id = "$user_id";
        $comment->student_name = "$username";
        $comment->target = $question_id;
        $comment->content = "$content";
        $comment->rating = "0";
        $comment->comment_rating = intval($question_rating);
        $comment->describe_teacher = "$comment_teaching";
        $comment->create_time = $date;
        $comment->update_time = $date;
        $comment->teacher_id = "$ask_order[answer_uid]";
        $comment->teacher_name = "$ask_order[answer_username]";
        $comment->comment_style = '3'; //学生评价老师

        if ($comment->save()) {
            AskOrder::updateAll(['s_is_comment' => 1], ['order_id' => $order_id]);
            $creditRule = new CreditRule();
            $creditRule->studentComment($user_id, $question_id, $group_id);

            CTask::done($user_id, 1, 'judge');

            $rating = intval($postData['question_rating']);
            $tch_count = TchCount::findOne(['user_id' => $ask_order['answer_uid']]);
            $tch_count->AskPositive = $tch_count->AskPositive + 1;
            $tch_count->comment_num = $tch_count->comment_num + 1;
            $tch_count->comment_sum_rating = $tch_count->comment_sum_rating + $rating;

            $coefficient = 0.5;
            if (isset(Yii::$app->params['teacherLevelCoefficient'][$rating])) {
                $coefficient = Yii::$app->params['teacherLevelCoefficient'][$rating];
            }
            $tch_count->credits += Yii::$app->params['teacherLevelCardinality'] * $coefficient;
            $level = TchCount::getRatingByCredits($tch_count->credits);
            $tch_count->rating = $level;

            if ($tch_count->update()) {
                $data['status'] = "200";
                $data['msg'] = "ok";
            } else {
                $data['status'] = '6015';
                $data['msg'] = Yii::$app->params['q_6015'];
            }

        } else {
            $data['status'] = '6015';
            $data['msg'] = Yii::$app->params['q_6015'];
        }
        return $data;
    }
}
