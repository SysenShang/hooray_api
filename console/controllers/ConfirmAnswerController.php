<?php

namespace console\Controllers;

use yii;
use yii\console\Controller;
use common\models\AskOrder;
use common\models\CoinLog;
use common\models\CreditRule;
use common\models\Question;
use common\models\SysCoinLog;
use common\models\TchCount;
use common\models\Comment;
use common\components\JPushNotice;

/**
 * Class ConfirmAnswerController
 * @package console\Controllers
 */

/** @noinspection LongInheritanceChainInspection */
class ConfirmAnswerController extends Controller
{
    public function actionAuto()
    {
        set_time_limit(3600);
        $askOrders = AskOrder::find()
                             ->from(AskOrder::tableName() . ' o')
                             ->select('order_id,order_sn,answer_uid,answer_username,reward,o.question_id,confrim,' .
                                 's_is_comment,question_title,published_uid,published_username')
                             ->asArray()
                             ->leftJoin(Question::tableName() . ' q', 'o.question_id = q.question_id')
                             ->where('replies = 1 AND o.first = 0 AND s_is_comment = 0')
                             ->andWhere('answer_add_time < date_sub(now(),INTERVAL 1 DAY)')
                             ->limit(500)
                             ->all();
        foreach ($askOrders as $askOrder) {
            if ($askOrder['confrim'] === '2') {
                sleep(2);
                $this->judge($askOrder);
                continue;
            }
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $result = AskOrder::updateAll([
                    'confrim' => 2,
                    'order_status' => 3
                ], ['order_id' => $askOrder['order_id']]);
                if (!$result) {
                    $transaction->rollBack();
                    continue;
                }

                $tchCount = TchCount::find()->where(['user_id' => $askOrder['answer_uid']])->limit(1)->one();
                if (null === $tchCount) {
                    $tchCount            = new  TchCount();
                    $tchCount['user_id'] = $askOrder['answer_uid'];
                    $tchCount['rating']  = 0;
                }
                $rating     = Yii::$app->params['systemEduCoinRate'][$tchCount['rating']];
                $coin       = $askOrder['reward'];
                $commission = floor($coin * $rating);//系统佣金
                $income     = $coin - $commission;//教师收入
                $tchCount['coin'] += $income;
                $tchCount['question_num'] += 1;
                if (!$tchCount->save()) {
                    $transaction->rollBack();
                    continue;
                }
                unset($tchCount);

                $this->coinLog($askOrder, $income, $commission);

                $creditRule = new CreditRule();
                $creditRule->teacherQuestion($askOrder['answer_uid'], $askOrder['question_id']);
                unset($creditRule);

                $transaction->commit();
                sleep(2);
                $this->judge($askOrder);
            } catch (yii\db\Exception $e) {
                echo $e->getMessage();
                $transaction->rollBack();
                continue;
            }
        }
        return true;
    }

    private function coinLog($askOrder, $income, $commission)
    {
        if ((int)$income <= 0) {
            return true;
        }
        //记录哇哇豆LOG中
        $coinLog             = new CoinLog();
        $coinLog->user_id    = $askOrder['answer_uid'];
        $coinLog->order_id   = $askOrder['order_sn'];
        $coinLog->order_type = 0;
        $coinLog->nums       = $income;
        $coinLog->type       = 1;
        $coinLog->remark     = "系统确认了解答,获得哇哇豆($income).";
        if (!$coinLog->save()) {
            return false;
        }
        unset($coinLog);

        //保存日志
        $sysLog             = new SysCoinLog();
        $sysLog->user_id    = $askOrder['answer_uid'];
        $sysLog->order_id   = $askOrder['order_sn'];
        $sysLog->order_type = 0;
        $sysLog->nums       = $commission;
        $sysLog->remark     = '解答问题佣金.';
        $sysLog->createtime = date('Y-m-d H:i:s');
        if (!$sysLog->save()) {
            return false;
        }
        unset($sysLog);

//        $jpush = new JPushNotice();
//        $jpush->send([$askOrder['answer_uid']], [
//            'type' => '3004',
//            'question_title' => $askOrder['question_title'],
//            'question_id' => $askOrder['question_id'],
//            'order_id' => $askOrder['order_sn'],
//            'title' => "您解答的《{$askOrder['question_title']}》，已经确认，系统已向您账户转入{$income}哇哇豆."
//        ]);
        return true;
    }

    private function judge($askOrder)
    {
        $score    = 5;//默认好评
        $content  = '默认好评.';
        $describe = mt_rand(1, 4);
        $date     = date('Y-m-d H:i:s');
        $groupId  = 1;

        if (Yii::$app->params['teacher_comment']['isFixStar']) {
            $score = Yii::$app->params['teacher_comment']['star'];
        }
        if (null === $askOrder['published_uid']) {//处理遗留不完整数据
            AskOrder::updateAll(['s_is_comment' => 1], ['order_id' => $askOrder['order_id']]);
            return false;
        }
        $comment = new Comment();

        $comment->student_id       = $askOrder['published_uid'];
        $comment->student_name     = $askOrder['published_username'];
        $comment->target           = $askOrder['question_id'];
        $comment->content          = $content;
        $comment->rating           = '';
        $comment->comment_rating   = $score;
        $comment->describe_teacher = (string)$describe;
        $comment->create_time      = $date;
        $comment->update_time      = $date;
        $comment->teacher_id       = $askOrder['answer_uid'];
        $comment->teacher_name     = $askOrder['answer_username'];
        $comment->comment_style    = 3; //学生评价老师

        if ($comment->save() && AskOrder::updateAll(['s_is_comment' => 1], ['order_id' => $askOrder['order_id']])) {

            $creditRule = new CreditRule();
            $creditRule->studentComment($askOrder['published_uid'], $askOrder['question_id'], $groupId);


            $tchCount = TchCount::find()->where(['user_id' => $askOrder['answer_uid']])->limit(1)->one();
            if (null === $tchCount) {
                return false;
            }
            $tchCount['AskPositive'] += 1;
            $tchCount['comment_num'] += 1;
            $tchCount['comment_sum_rating'] += $score;

            $coefficient = 0.5;
            if (isset(Yii::$app->params['teacherLevelCoefficient'][$score])) {
                $coefficient = Yii::$app->params['teacherLevelCoefficient'][$score];
            }
            $tchCount['credits'] += Yii::$app->params['teacherLevelCardinality'] * $coefficient;
            $level = TchCount::getRatingByCredits($tchCount['credits']);
            $tchCount['rating'] = $level;

            if (!$tchCount->save()) {
                return false;
            }

            //发送消息
//            $jpush = new JPushNotice();
//            $jpush->send([
//                $askOrder['answer_uid']
//            ], [
//                'type' => '3006',
//                'question_id' => $askOrder['question_id'],
//                'question_title' => $askOrder['question_title'],
//                'title' => $askOrder['published_username'] . '同学对《' . $askOrder['question_title'] . '》的解答给了评价.',
//                'content' => $content
//            ]);
        } else {
            return false;
        }
        return true;
    }
}
