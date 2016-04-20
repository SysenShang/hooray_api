<?php

namespace console\Controllers;

use common\components\NeteaseIm;
use common\models\TeachSchedule;
use common\models\TimeSms;
use common\models\TimeIm;
use yii;
use yii\console\Controller;

/**
 * Class TimeTaskController
 * @package console\Controllers
 */
class TimeTaskController extends Controller
{
    public function actionDo()
    {
        //if (PHP_OS === 'Darwin') {
        //    proc_close(proc_open('osascript -e \'display notification "TimeTask/do" with title "PHP"\'', [], $foo));
        //}
        set_time_limit(3000);
        $idx = 100;
        while (--$idx > 0) {
            $transaction = Yii::$app->db->beginTransaction(yii\db\Transaction::SERIALIZABLE);
            try {
                $time = time();
                $sql  = TimeSms::find()->where([
                    'BETWEEN',
                    'at_time',
                    date('Y-m-d H:i:s', $time - 30),
                    date('Y-m-d H:i:s', $time + 30)
                ])->limit(150)->createCommand()->getRawSql();
                $task = TimeSms::findBySql($sql . ' FOR UPDATE')->asArray()->all();
                foreach ($task as $item) {
                    $result = NeteaseIm::sendMsg(unserialize($item['mobile']), $item['msg']);
                    if ($result) {
                        TimeSms::deleteAll(['id' => $item['id']]);
                    }
                }
                $transaction->commit();
            } catch (yii\db\Exception $e) {
                $transaction->rollBack();
            }
            sleep(1);
            $transaction = Yii::$app->db->beginTransaction(yii\db\Transaction::SERIALIZABLE);
            try {
                $time = time();
                $sql  = TimeIm::find()->where([
                    'BETWEEN',
                    'at_time',
                    date('Y-m-d H:i:s', $time - 30),
                    date('Y-m-d H:i:s', $time + 300)
                ])->limit(150)->createCommand()->getRawSql();
                $task = TimeIm::findBySql($sql . ' FOR UPDATE')->asArray()->all();
                foreach ($task as $item) {
                    $data = $item;
                    unset($data['id']);
                    $result = NeteaseIm::toggleChatRoom($data);
                    if ($result) {
                        TimeIm::deleteAll(['id' => $item['id']]);
                    }
                }
                $transaction->commit();
            } catch (yii\db\Exception $e) {
                $transaction->rollBack();
            }
            sleep(1);
        }
        return true;
    }

    public function actionProcess()
    {
        $data = TeachSchedule::find()
                             ->select('teacher_id,date,begintime,endtime,chatroom_id')
                             ->where('chatroom_id is not null')
                             ->asArray()
                             ->all();
        $now  = time();
        foreach ($data as $item) {
            NeteaseIm::toggleChatRoom([
                'roomid' => $item['chatroom_id'],
                'operator' => $item['teacher_id'],
                'valid' => 'false'
            ]);
            if (strtotime($item['date'] . ' ' . $item['begintime']) > $now) {
                $time = strtotime('-20 minutes', strtotime($item['date'] . ' ' . $item['begintime']));
                NeteaseIm::toggleChatRoom([
                    'roomid' => $item['chatroom_id'],
                    'operator' => $item['teacher_id'],
                    'valid' => 'true'
                ], date('Y-m-d H:i:s', $time));
                $time = strtotime('+20 minutes', strtotime($item['date'] . ' ' . $item['endtime']));
                NeteaseIm::toggleChatRoom([
                    'roomid' => $item['chatroom_id'],
                    'operator' => $item['teacher_id'],
                    'valid' => 'false'
                ], date('Y-m-d H:i:s', $time));
            }
            $result = NeteaseIm::getChatRoom([
                'roomid' => $item['chatroom_id']
            ]);
            echo json_encode($result);
        }
    }
}
