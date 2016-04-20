<?php

/**
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 12/23/15
 * Time: 5:18 PM
 * File: CTask.php
 */

namespace common\components;

use yii;
use common\models\Task;
use common\models\CoinLog;
use common\models\TchCount;
use common\models\StuCount;
use common\models\F;

class CTask
{
    public static function done($uid, $group_id, $type)
    {
        $userTask = Task::find()->where(['user_id' => $uid])->limit(1)->one();
        if (null === $userTask) {
            $CoinLog = new CoinLog();
            $CoinLog->registerTask($uid, $group_id);
            $userTask = Task::find()->where(['user_id' => $uid])->limit(1)->one();
        }
        if ($group_id == 1) {
            $today = date('Y-m-d');
            $items = [
                'lastdate_checkin',
                'lastdate_ask',
                'lastdate_judge',
                'lastdate_share',
                //'lastdate_weike',
                'lastdate_buyweike',
                'lastdate_judgeweike'
            ];
            foreach ($items as $item) {
                $key = str_replace('lastdate', 'everyday', $item);
                if (substr($userTask[$item], 0, 10) !== $today) {
                    $userTask[$key] = 0;
                }
            }
            if (substr($userTask['lastdate_checkin'], 0, 10) < date('Y-m-d', strtotime('last day'))) {
                $userTask['serial_checkin'] = 0;
            }
            if (substr($userTask['updated_at'], 0, 10) !== $today) {
                $userTask['today_scores'] = 0;
            }
            self::filter($type, $userTask, $uid, $group_id);
        }
    }

    private static function filter($type, $userTask, $uid, $group_id)
    {
        $today  = date('Y-m-d');
        $remark = null;
        $tasks  = Yii::$app->params['tasks'];
        if (in_array($type, ['checkin', 'ask', 'judge', 'share', 'buyweike', 'judgeweike'], true)) {
            if ($type === 'checkin') {
                $orderType   = 10;
                $reward      = $tasks[1]['lists'][0]['value'];
                $lastCheckin = substr($userTask['lastdate_checkin'], 0, 10);

                $checkinLastday = $lastCheckin === date('Y-m-d', strtotime('last day'));
                if ($checkinLastday && $userTask['everyday_checkin'] === 0) {
                    if ($userTask['serial_checkin'] === ($tasks[1]['lists'][6]['target'] - 1)) {
                        $_task = clone $userTask;
                        $_task['total_scores'] += $reward;
                        $_task['today_scores'] += $reward;

                        $remark = "用户连续签到,赠送哇哇豆($reward).";
                        $reward = $tasks[1]['lists'][6]['value'];
                        self::writeLog($reward, $remark, $orderType, $uid, $group_id);
                        $_task->save();
                    } elseif ($userTask['serial_checkin'] >= Yii::$app->params['task']['serial_checkin_target']) {
                        $userTask['serial_checkin'] -= $tasks[1]['lists'][6]['target'];
                    }
                    $userTask['serial_checkin'] += 1;
                } elseif ($lastCheckin !== $today) {
                    $userTask['serial_checkin'] = 1;
                }
            }
            $conf      = [
                'checkin' => 0,
                'ask' => 1,
                'judge' => 2,
                'share' => 3,
                'buyweike' => 4,
                'judgeweike' => 5,
            ];
            $reward    = $tasks[1]['lists'][$conf[$type]]['value'];
            $orderType = 8;
            if ($userTask['everyday_' . $type] === 0) {
                $userTask['everyday_' . $type] = $reward;
                $userTask['total_scores'] += $reward;
                $userTask['today_scores'] += $reward;
                $userTask['lastdate_' . $type] = date('Y-m-d H:i:s');

                $remark = $tasks[1]['lists'][$conf[$type]]['label'] . ",赠送哇哇豆($reward).";
                self::writeLog($reward, $remark, $orderType, $uid, $group_id);
            }
            $userTask->save();
        }
    }

    private static function writeLog($reward, $remark, $order_type, $uid, $group_id)
    {
        $coinLog             = new CoinLog();
        $coinLog->user_id    = $uid;
        $coinLog->order_id   = F::generateOrderSn(null);
        $coinLog->order_type = $order_type;
        $coinLog->nums       = $reward;
        $coinLog->type       = 1;
        $coinLog->remark     = $remark;
        $coinLog->detail     = '';
        $coinLog->status     = 2;
        $coinLog->createtime = date('Y-m-d H:i:s');
        if ($coinLog->save()) {
            $jpush = new JPushNotice();
            $jpush->send([
                $uid
            ], [
                'type' => '1000',
                'title' => '完成任务:' . $remark
            ]);
        }
        if ($group_id === 2) {
            $teacher_count = TchCount::find()->where(['user_id' => $uid])->limit(1)->one();
            if (null !== $teacher_count) {
                $teacher_count['coin'] += $reward;
                $teacher_count->save();
            }
        } else {
            $student_count = StuCount::find()->where(['user_id' => $uid])->limit(1)->one();
            if (null !== $student_count) {
                $student_count['coin'] += $reward;
                $student_count->save();
            }
        }
    }
}
