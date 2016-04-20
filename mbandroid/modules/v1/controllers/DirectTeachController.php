<?php

namespace mbandroid\modules\v1\controllers;

use common\components\JPushNotice;
use common\components\NeteaseIm;
use common\models\CoinLog;
use common\models\CommonOrder;
use common\models\StuCount;
use common\models\StudentInfo;
use common\models\TchCount;
use common\models\TeachAttachment;
use common\models\TeacherInfo;
use common\models\TeachMessage;
use common\models\TeachReserve;
use common\models\TeachSchedule;
use common\models\TimeSms;
use yii;
use yii\filters\AccessControl;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;

class DirectTeachController extends ActiveController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->modelClass = '';
        parent::init();
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['authMethods']    = [
            QueryParamAuth::className()
        ];
        $behaviors['verbFilter']['actions']['delete'] = ['POST', 'DELETE'];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => false,
                    'verbs' => ['GET', 'HEAD', 'PUT', 'PATCH', 'DELETE', 'OPTIONS']
                ],
                [
                    'allow' => true,
                    'roles' => ['@']
                ]
            ]
        ];

        return $behaviors;
    }

    public function actions()
    {
        return [];
    }

    /**
     * 老师设置开课信息
     * @return array|string
     * @author grg
     */
    public function actionSetting()
    {
        set_time_limit(300);
        $data = Yii::$app->request->post();

        $data['teacher_id'] = Yii::$app->user->getId();
        $data['username']   = Yii::$app->user->identity->username;
        //转化分钟格式的endtime
        if (array_key_exists('endtime', $data) && ctype_digit((string)$data['endtime'])) {
            $endtime = strtotime('+' . (int)$data['endtime'] . ' minutes', strtotime($data['begintime']));

            $data['endtime'] = date('H:i:s', $endtime);
        }
        if (array_key_exists('id', $data)) {
            $schedule = TeachSchedule::find()
                                     ->where(['id' => $data['id'], 'teacher_id' => $data['teacher_id']])
                                     ->limit(1)
                                     ->one();
            $data     = array_filter($data, function ($item) {
                return '' !== (string)$item;
            });
        } else {
            $schedule = new TeachSchedule();
            $params   = ['date', 'begintime', 'endtime', 'price'];
            foreach ($params as $param) {
                if (!array_key_exists($param, $data)) {
                    return ['status' => '6050'];
                }
            }
            if ($data['begintime'] > $data['endtime'] || strtotime($data['date'] . ' ' . $data['begintime']) < time()) {
                return ['status' => '6048'];
            }
            $today   = date('Y-m-d');
            $already = TeachSchedule::find()->select('date,begintime,endtime')->where([
                'teacher_id' => $data['teacher_id'],
                'date' => $data['date']
            ])->andWhere([
                'OR',
                '"' . $today . '" < `date`',
                '"' . $today . '" = `date` AND curtime() < begintime',//尚未开始的
                '"' . $today . '" = `date` AND curtime() BETWEEN begintime AND endtime AND num > 0'//进行中,且有人约课的
            ])->asArray()->all();
            if (null !== $already) {
                $cross      = false;
                $beginTime1 = strtotime($data['date'] . ' ' . $data['begintime']);
                $endTime1   = strtotime($data['date'] . ' ' . $data['endtime']);
                foreach ($already as $item) {
                    $beginTime2 = strtotime($item['date'] . ' ' . $item['begintime']);
                    $endTime2   = strtotime($item['date'] . ' ' . $item['endtime']);
                    if (self::isTimeCross($beginTime1, $endTime1, $beginTime2, $endTime2)) {
                        $cross = true;
                        break;
                    }
                }
                if ($cross) {
                    return ['status' => '6049'];
                }
            }
            $msg = '您开设的 ' . $data['date'] . ' ' . $data['begintime'] . ' 的VIP课程，';
            $msg .= '即将开始上课，请提前进入课堂准备。';
            $time = strtotime('-20 minutes', strtotime($data['date'] . ' ' . $data['begintime']));
            //定时短信通知
            NeteaseIm::sendMsg([Yii::$app->user->identity->telephone], $msg, date('Y-m-d H:i:s', $time));
            $times = 0;
            do {
                $times++;
                $result = NeteaseIm::createChatRoom([
                    'creator' => $data['teacher_id'],
                    'name' => $data['date'] . ' ' . $data['begintime'] . ' 的VIP课程专属聊天室'
                ]);
            } while (false === $result || $times === 10);
            if (false !== $result) {
                $data['chatroom_id'] = $result;
                //定时开关聊天室
                NeteaseIm::toggleChatRoom([
                    'roomid' => $result,
                    'operator' => $data['teacher_id'],
                    'valid' => 'true'
                ], date('Y-m-d H:i:s', $time));
                $time = strtotime('+20 minutes', strtotime($data['date'] . ' ' . $data['endtime']));
                NeteaseIm::toggleChatRoom([
                    'roomid' => $result,
                    'operator' => $data['teacher_id'],
                    'valid' => 'false'
                ], date('Y-m-d H:i:s', $time));
            } else {
                return ['status' => '400'];
            }
        }
        $schedule->setAttributes($data);
        $result = (string)$schedule->save();
        if (!array_key_exists('id', $data)) {
            $schedule->setAttributes(['code' => (string)crc32($schedule->id)]);
            $schedule->save();
        }
        return $result;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */
    private static function isTimeCross($beginTime1, $endTime1, $beginTime2, $endTime2)
    {
        return $beginTime2 > $beginTime1 ? $beginTime2 < $endTime1 : $endTime2 > $beginTime1;
    }

    /**
     * 某位老师的所有开课信息
     * @return array|yii\db\ActiveRecord[]
     * @author grg
     */
    public function actionSchedule()
    {
        $data         = Yii::$app->request->post();
        $with_student = array_key_exists('with_student', $data);
        unset($data['with_student']);
        if (!array_key_exists('teacher_id', $data)) {
            $data['teacher_id'] = Yii::$app->user->getId();
        }
        if (array_key_exists('date', $data) && preg_match('/^([\d]{4}-[\d]{2})$/', $data['date'])) {
            $numOfDay   = date('t', strtotime($data['date'] . '-01'));
            $dayOfMonth = range(1, $numOfDay);
            array_walk($dayOfMonth, function (&$item) use ($data) {
                $item = $data['date'] . '-' . str_pad($item, 2, 0, STR_PAD_LEFT);
            });
            $data['date'] = $dayOfMonth;
        }
        $schedule = TeachSchedule::find()
                                 ->where($data)
                                 ->andWhere('date >=' . date('Y-m-d'))
                                 ->orderBy('date,begintime')
                                 ->asArray()
                                 ->all();
        if (count($schedule) > 0) {
            $now  = time();
            $date = date('Y-m-d H:i:s');
            array_walk($schedule, function (&$item) use ($now, $date) {
                $beginTime = strtotime($item['date'] . ' ' . $item['begintime']);
                $coming    = $now >= $beginTime - 900;
                $endTime   = strtotime($item['date'] . ' ' . $item['endtime']);
                $over      = $now > $endTime + 900;

                $item['expire']    = (int)($now >= $beginTime);//与is_up同义,为了兼容,请保留
                $item['is_up']     = (int)($now >= $beginTime);
                $item['is_down']   = (int)($now > $endTime);
                $item['is_coming'] = (int)$coming;
                $item['is_over']   = (int)$over;
                $item['can_join']  = (int)($coming && !$over);

                $item['now']      = $date;
                $item['time']     = $now;
                $item['duration'] = ($endTime - $beginTime) / 60;
            });
            if ($with_student) {
                $schedule = array_column($schedule, null, 'id');
                array_walk($schedule, function (&$item) {
                    $item['students']  = [];
                    $item['total_msg'] = 0;
                    $item['messages']  = 0;
                });
                $scores = Yii::$app->params['directTeach']['scores'];
                $fields = 'r.id,schedule_id,stage_gid,stage_name,subject_gid,subject_name,';
                $fields .= 'student_id,r.username,avatar,judge_at,comment,count(m.id) msg,';
                $fields .= 'sum(IF(read_status=0 AND m.user_id = r.student_id,concat(1),concat(0))) new_msg,';
                $students = TeachReserve::find()
                                        ->from(TeachReserve::tableName() . ' r')
                                        ->select($fields . 'case when `comment` is null then "" else `score` end score')
                                        ->asArray()
                                        ->leftJoin(StudentInfo::tableName() . ' i', 'i.user_id = student_id')
                                        ->leftJoin(TeachMessage::tableName() . ' m', [
                                            'AND',
                                            'r.id = m.reserve_id'
                                        ])
                                        ->where(['schedule_id' => array_keys($schedule)])
                                        ->groupBy('r.id')
                                        ->all();
                if (count($students) > 0) {
                    foreach ($students as $key => $student) {
                        if ('' !== $student['score']) {
                            $student['label'] = $scores[$student['score']];
                        } else {
                            $student['label'] = '';
                        }
                        $schedule[$student['schedule_id']]['students'][] = $student;
                        $schedule[$student['schedule_id']]['total_msg'] += $student['msg'];
                        $schedule[$student['schedule_id']]['messages'] += $student['new_msg'];
                    }
                }
                $schedule = array_values($schedule);
            }
        }
        return $schedule;
    }

    /**
     * 对上一接口的结果按日期进行分组
     * @return array
     * @author grg
     */
    public function actionScheduleByDate()
    {
        $list = $this->actionSchedule();
        $data = array_flip(array_column($list, 'date'));
        array_walk($data, function (&$item) {
            $item = [];
        });
        foreach ($list as $item) {
            $data[$item['date']][] = $item;
        }
        return $data;
    }

    public function actionScheduleByTime()
    {
        $list = $this->actionScheduleByDate();
        foreach ($list as &$items) {
            $schedule = [
                'am' => [],
                'pm' => [],
                'night' => []
            ];
            foreach ($items as $item) {
                switch (true) {
                    case $item['begintime'] < '12:00':
                        $schedule['am'][] = $item;
                        break;
                    case $item['begintime'] >= '12:00' && $item['begintime'] <= '18:00':
                        $schedule['pm'][] = $item;
                        break;
                    case $item['begintime'] > '18:00':
                        $schedule['night'][] = $item;
                        break;
                }
            }
            $items = $schedule;
        }
        if (count($list) === 1) {
            $list = array_shift($list);
        }
        return $list;
    }

    /**
     * 对上一接口的结果进行合计
     * @return array
     * @author grg
     */
    public function actionScheduleCountByDay()
    {
        $list  = $this->actionScheduleByDate();
        $data  = [];
        $today = date('Y-m-d');
        $now   = date('Y-m-d H:i:s');
        foreach ($list as $date => $schedules) {
            $data[] = [
                'date' => $date,
                'count' => count($schedules),
                'new' => count(array_filter($schedules, function ($schedule) use ($now) {
                    $expire = $schedule['date'] . ' ' . $schedule['begintime'] < $now;
                    return !$expire && (int)$schedule['num'] < (int)$schedule['limit'];
                })),
                'expire' => $date < $today ? 1 : 0
            ];
        }
        return $data;
    }

    /**
     * 删除开课信息
     * @return array
     * @author grg
     */
    public function actionDelete()
    {
        $data = Yii::$app->request->post();
        if (!array_key_exists('id', $data)) {
            return ['status' => '400'];
        }
        $data['teacher_id'] = Yii::$app->user->getId();
        $data['num']        = 0;

        $affected = TeachSchedule::deleteAll($data);
        return $affected > 0 ? ['status' => '200'] : ['status' => '400'];
    }

    /**
     * 老师进入白板开始上课
     * @return array
     * @throws yii\db\Exception
     * @author grg
     */
    public function actionStart()
    {
        $data = Yii::$app->request->post();
        if (!array_key_exists('id', $data)) {
            return ['status' => '400'];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $scheduleId = $data['id'];
            unset($data['id']);
            $affected = TeachSchedule::updateAll($data, [
                'id' => $scheduleId,
                'teacher_id' => Yii::$app->user->getId()
            ]);
            if (1 !== $affected) {
                $transaction->rollBack();
                return ['status' => '400'];
            }
            if (array_key_exists('classroom_record', $data)) {
                TeachReserve::updateAll(['status' => 1], [
                    'schedule_id' => $scheduleId,
                    'status' => 0
                ]);
            }
            $transaction->commit();
            return ['status' => '200'];
        } catch (yii\db\Exception $e) {
            $transaction->rollBack();
            return ['status' => '400'];
        }
    }

    public function actionAttachment()
    {
        $data = Yii::$app->request->post();
        if (!array_key_exists('schedule_id', $data)) {
            return ['status' => '400'];
        }
        $data['user_id']  = Yii::$app->user->getId();
        $data['username'] = Yii::$app->user->identity->username;

        $attach = new TeachAttachment();
        $attach->setAttributes($data);
        return $attach->save();
    }

    public function actionAttachments()
    {
        $data = Yii::$app->request->post();
        if (!array_key_exists('schedule_id', $data)) {
            return ['status' => '400'];
        }
        $order = array_key_exists('order', $data) && strtoupper($data['order']) === 'ASC' ? 'ASC' : 'DESC';
        return TeachAttachment::find()->select('user_id,username,file_url')->where([
            'schedule_id' => $data['schedule_id']
        ])->orderBy('id ' . $order)->asArray()->all();
    }

    /**
     * 学生申请约课
     * @return array
     * @throws yii\db\Exception
     * @author grg
     */
    public function actionReserve()
    {
        $data = Yii::$app->request->post();
        if (!array_key_exists('schedule_id', $data)) {
            return ['status' => '400'];
        }
        $data['student_id'] = Yii::$app->user->getId();
        $data['username']   = Yii::$app->user->identity->username;
        $transaction        = Yii::$app->db->beginTransaction(yii\db\Transaction::SERIALIZABLE);
        try {
            $reserve = new TeachReserve();
            $reserve->setAttributes($data);
            if (!$reserve->save()) {
                throw new yii\db\Exception('');
            }
            $condition = '`id` =' . $data['schedule_id'] . ' AND `num` < `limit`';
            $condition .= ' AND concat(`date`, " ", begintime) > CURRENT_TIMESTAMP';
            $affected = TeachSchedule::updateAllCounters(['num' => 1], $condition);
            if (1 !== $affected) {
                $transaction->rollBack();
                return ['status' => '9041'];
            }
            $result = $this->pay($data['schedule_id'], $data['student_id']);
            if (is_array($result)) {
                $transaction->rollBack();
                return $result;
            } elseif (false === $result) {
                $transaction->rollBack();
                return ['status' => '400'];
            }
            $transaction->commit();
            $schedule = TeachSchedule::find()
                                     ->select('teacher_id,username,date,begintime,num')
                                     ->where(['id' => $data['schedule_id']])
                                     ->limit(1)
                                     ->asArray()
                                     ->one();
            $jpush    = new JPushNotice();
            $jpush->send([$data['student_id']], [
                'type' => 2000,
                'title' => '您预约的 ' . $schedule['username'] . ' 老师的 ' . $schedule['date'] . ' 的VIP课程，预约成功。'
            ]);
            $jpush->send([$schedule['teacher_id']], [
                'type' => 2000,
                'title' => $data['username'] . ' 预约了 ' . $schedule['date'] . ' 的VIP课程，人数已有 ' . $schedule['num']
            ]);

            $msg = '您预约的 ' . $schedule['username'] . ' 的VIP课程，';
            $msg .= '将于 ' . $schedule['date'] . ' ' . $schedule['begintime'] . ' 开始上课，请提前进入课堂等待。';
            $time = strtotime('-15 minutes', strtotime($schedule['date'] . ' ' . $schedule['begintime']));
            NeteaseIm::sendMsg([Yii::$app->user->identity->telephone], $msg, date('Y-m-d H:i:s', $time));

            return ['status' => '200'];
        } catch (yii\db\Exception $e) {
            $transaction->rollBack();
            return ['status' => '400'];
        }
    }

    /**
     * 取消预约或隐藏记录(已经上课)
     * @return array
     * @throws \Exception
     * @throws yii\db\Exception
     * @author grg
     */
    public function actionCancel()
    {
        $data = Yii::$app->request->post();
        if (!array_key_exists('schedule_id', $data)) {
            return ['status' => '400'];
        }
        $data['student_id'] = Yii::$app->user->getId();
        $transaction        = Yii::$app->db->beginTransaction(yii\db\Transaction::SERIALIZABLE);
        try {
            $reserve = TeachReserve::find()->where($data)->limit(1)->one();
            if (null === $reserve) {
                return ['status' => '400'];
            }
            $schedule  = TeachSchedule::find()
                                      ->select('date,begintime,username')
                                      ->where(['id' => $data['schedule_id']])
                                      ->asArray()
                                      ->limit(1)
                                      ->one();
            $begintime = $schedule['date'] . ' ' . $schedule['begintime'];
            if (0 === $reserve['status'] && $begintime >= date('Y-m-d H:i:s', strtotime('-15 minutes'))) {
                $coming = strtotime($schedule['date'] . ' ' . $schedule['begintime']) < time() + 6 * 3600;
                if ($coming) {
                    return ['status' => '9042'];
                }
                TeachMessage::deleteAll(['reserve_id' => $reserve['id']]);
                $reserve->delete();
                $affected = TeachSchedule::updateAllCounters(['num' => -1], ['id' => $data['schedule_id']]);
                if (1 !== $affected) {
                    $transaction->rollBack();
                    return ['status' => '400'];
                }
                $result = $this->pay($data['schedule_id'], $data['student_id'], true);
                if (is_array($result)) {
                    $transaction->rollBack();
                    return $result;
                } elseif (false === $result) {
                    $transaction->rollBack();
                    return ['status' => '400'];
                }
                $msg = '您预约的 ' . $schedule['username'] . ' 的VIP课程，';
                $msg .= '将于 ' . $schedule['date'] . ' ' . $schedule['begintime'] . ' 开始上课，请提前进入课堂等待。';
                $mobile = [Yii::$app->user->identity->telephone];
                TimeSms::deleteAll([
                    'mobile' => serialize($mobile),
                    'msg' => $msg
                ]);
            } elseif (1 === $reserve['status'] || $begintime < date('Y-m-d H:i:s', strtotime('-15 minutes'))) {
                //todo 隐藏之后对方又发来新的留言怎么办
                $reserve['status'] = -1;//隐藏
                $reserve->update();
            }

            $transaction->commit();
            return ['status' => '200'];
        } catch (yii\db\Exception $e) {
            $transaction->rollBack();
            return ['status' => '400'];
        }
    }

    /**
     * @param $classId
     * @param $userId
     * @param $refund
     * @return array|bool
     * @throws yii\db\Exception
     * @author grg
     */
    private function pay($classId, $userId, $refund = false)
    {
        $transaction = Yii::$app->db->beginTransaction(yii\db\Transaction::SERIALIZABLE);
        try {
            $schedule = TeachSchedule::find()
                                     ->select('id,price,teacher_id,username,date')
                                     ->where(['id' => $classId])
                                     ->asArray()
                                     ->limit(1)
                                     ->one();
            if ($schedule['price'] <= 0) {
                return $schedule['price'] === 0;
            }
            $schedule['student_id'] = $userId;

            $student = Yii::$app->user->identity->username;
            $teacher = $schedule['username'];
            $orderId = '60' . hexdec(uniqid('', false));
            $date    = date('Y-m-d H:i:s');
            $price   = $schedule['price'];
            $remark  = [
                '预约了' . $teacher . ' ' . $schedule['date'] . '的VIP课程,付款(' . $schedule['price'] . ').',
                $student . '预约了' . $schedule['date'] . '的VIP课程,付款(' . $schedule['price'] . ').',
                $student . '预约了' . $teacher . ' ' . $schedule['date'] . '的VIP课程,付款(' . $schedule['price'] . ').'
            ];

            $condition = 'user_id ="' . $userId . '"';
            if ($refund) {
                $remark = [
                    '取消' . $teacher . ' ' . $schedule['date'] . '的VIP课程,退款(' . $schedule['price'] . ').',
                    $student . '取消' . $schedule['date'] . '的VIP课程,退款(' . $schedule['price'] . ').',
                    $student . '取消' . $teacher . ' ' . $schedule['date'] . '的VIP课程,退款(' . $schedule['price'] . ').'
                ];
                $price  = -$price;
            } else {
                $condition .= ' AND coin >=' . $schedule['price'];
            }
            $affected = StuCount::updateAllCounters(['coin' => -$price], $condition);
            if (1 !== $affected) {
                $transaction->rollBack();
                return ['status' => '9010'];
            }
            $coinlog             = new CoinLog();
            $coinlog->user_id    = $userId;
            $coinlog->order_id   = $orderId;
            $coinlog->order_type = 11;
            $coinlog->nums       = $schedule['price'];
            $coinlog->type       = (int)$refund;
            $coinlog->remark     = $remark[0];
            $coinlog->detail     = serialize($schedule);
            $coinlog->status     = 2;
            $coinlog->createtime = $date;
            if (!$coinlog->save()) {
                $transaction->rollBack();
                return ['status' => '9013'];
            }
            $condition = 'user_id ="' . $schedule['teacher_id'] . '"';
            if ($refund) {
                $condition .= ' AND coin >=' . $schedule['price'];
            }
            $affected = TchCount::updateAllCounters(['coin' => $price], $condition);
            if (1 !== $affected) {
                $transaction->rollBack();
                return ['status' => '9014'];
            }
            $coinlog             = new CoinLog();
            $coinlog->user_id    = $schedule['teacher_id'];
            $coinlog->order_id   = $orderId;
            $coinlog->order_type = 11;
            $coinlog->nums       = $schedule['price'];
            $coinlog->type       = (int)!$refund;
            $coinlog->remark     = $remark[1];
            $coinlog->detail     = serialize($schedule);
            $coinlog->status     = 2;
            $coinlog->createtime = $date;
            if (!$coinlog->save()) {
                $transaction->rollBack();
                return ['status' => '9015'];
            }
            $commonOrder             = new CommonOrder();
            $commonOrder->order_id   = $orderId;
            $commonOrder->user_id    = $userId;
            $commonOrder->title      = $remark[2];
            $commonOrder->order_type = 4;
            $commonOrder->price      = $schedule['price'];
            $commonOrder->data       = json_encode($schedule);
            $commonOrder->createtime = $date;
            $commonOrder->status     = 3;
            if (!$commonOrder->save()) {
                $transaction->rollBack();
                return ['status' => '9003', 'msg' => Yii::$app->params['mc_9003']];
            }
            $transaction->commit();
            return true;
        } catch (yii\db\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * 某位学生已经约到的课/我的VIP课堂
     * @return array
     * @author grg
     */
    public function actionMyReserve($return = false)
    {
        $data               = Yii::$app->request->post();
        $data['student_id'] = Yii::$app->user->getId();

        $plusWhere = '';
        if (array_key_exists('status', $data)) {
            if ((int)$data['status'] === 1) {
                unset($data['status']);
                $plusWhere = ' AND (`status` = 1 OR concat(`date`, " ", endtime) < "' . date('Y-m-d H:i:s', strtotime('-15 minutes')) . '")';
            } elseif ((int)$data['status'] === 0) {
                $plusWhere = ' AND concat(`date`, " ", endtime) >= "' . date('Y-m-d H:i:s', strtotime('-15 minutes')) . '"';
            }
        }

        $pageSize = 20;
        if (array_key_exists('page', $data)) {
            $page = $data['page'];
            unset($data['page']);
        } else {
            $page = Yii::$app->request->get('page', 1);
        }
        $offset = ($page - 1) * $pageSize;

        $fields = 'r.id,schedule_id,code,date,begintime,endtime,classroom_id,classroom_record,chatroom_id,r.status,';
        $fields .= 'stage_gid,subject_gid,stage_name,subject_name,';
        $fields .= 'case when `comment` is null then "" else `score` end score,concat("") label,judge_at,comment,';
        $fields .= 'count(m.id) msg,sum(IF(read_status=0 AND m.user_id = s.teacher_id,concat(1),concat(0))) new_msg,';
        $query   = TeachReserve::find()
                               ->from(TeachReserve::tableName() . ' r')
                               ->select($fields . ',teacher_id,s.username teacher_name,avatar teacher_avatar,note')
                               ->asArray()
                               ->leftJoin(TeachSchedule::tableName() . ' s', 'r.schedule_id = s.id')
                               ->leftJoin(TeacherInfo::tableName() . ' t', 's.teacher_id = t.user_id')
                               ->leftJoin(TeachMessage::tableName() . ' m', [
                                   'AND',
                                   'r.id = m.reserve_id'
                               ])
                               ->where($data)
                               ->andWhere('r.status > -1' . $plusWhere)
                               ->groupBy('r.id');
        $stats   = clone $query;
        $reserve = $query->orderBy('r.status,date DESC,begintime DESC')->offset($offset)->limit($pageSize)->all();
        if (count($reserve) > 0) {
            $now    = time();
            $date   = date('Y-m-d H:i:s');
            $scores = Yii::$app->params['directTeach']['scores'];
            foreach ($reserve as $key => $item) {
                if ('' !== $item['score']) {
                    $reserve[$key]['label'] = $scores[$item['score']];
                }
                $beginTime = strtotime($item['date'] . ' ' . $item['begintime']);
                $coming    = $now >= $beginTime - 900;
                $endTime   = strtotime($item['date'] . ' ' . $item['endtime']);
                $over      = $now > $endTime + 900;

                $reserve[$key]['can_join'] = (int)($coming && !$over);

                $cancelable = $now < $beginTime - 6 * 3600;

                $reserve[$key]['cancelable'] = (int)$cancelable;

                $reserve[$key]['duration'] = ($endTime - $beginTime) / 60;
                $reserve[$key]['now']      = $date;
                $reserve[$key]['time']     = $now;

                $reserve[$key]['expire']    = (int)($now > $endTime);//与is_down同义,为了兼容,请保留
                $reserve[$key]['is_up']     = (int)($now >= $beginTime);
                $reserve[$key]['is_down']   = (int)($now > $endTime);
                $reserve[$key]['is_coming'] = (int)$coming;
                $reserve[$key]['is_over']   = (int)$over;
            }
        }
        if ($return) {
            $result = compact('reserve');
            $total  = $stats->select('concat("")')->count();

            $result['total_page'] = (int)ceil($total / $pageSize);
            return $result;
        } else {
            return $reserve;
        }
    }

    public function actionMyReservePage()
    {
        return $this->actionMyReserve(true);
    }

    /**
     * 学生点击上课
     * @return array
     * @throws yii\db\Exception
     * @author grg
     */
    public function actionDone()
    {
        $data = Yii::$app->request->post();
        if (!array_key_exists('schedule_id', $data)) {
            return ['status' => '400'];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $userId   = Yii::$app->user->getId();
            $affected = TeachReserve::updateAll(['status' => 1], [
                'schedule_id' => $data['schedule_id'],
                'student_id' => $userId
            ]);
            if (1 < $affected) {
                $transaction->rollBack();
                return ['status' => '400'];
            }
            $transaction->commit();
            $reserve = TeachReserve::find()
                                   ->from(TeachReserve::tableName() . ' r')
                                   ->select('classroom_id,classroom_record,chatroom_id')
                                   ->asArray()
                                   ->leftJoin(TeachSchedule::tableName(), 'schedule_id = r.id')
                                   ->where(['schedule_id' => $data['schedule_id'], 'student_id' => $userId])
                                   ->limit(1)
                                   ->one();
            if (count($reserve) === 0) {
                $transaction->rollBack();
                return ['status' => '400'];
            }
            return $reserve;
        } catch (yii\db\Exception $e) {
            $transaction->rollBack();
            return ['status' => '400'];
        }
    }

    /**
     * 学生对约课进行评价
     * @return array|null|string|yii\db\ActiveRecord
     * @author grg
     */
    public function actionJudge()
    {
        $data = Yii::$app->request->post();
        if (!array_key_exists('id', $data)) {
            return ['status' => '400'];
        }
        $data['student_id'] = Yii::$app->user->getId();

        $scores = Yii::$app->params['directTeach']['scores'];
        if (array_key_exists('score', $data)) {
            if (!array_key_exists($data['score'], array_keys($scores))) {
                $data['score'] = 0;
            }
            if (!array_key_exists('comment', $data) || null === $data['comment']) {
                $data['comment'] = '';
            }
            $affected = TeachReserve::updateAll([
                'score' => $data['score'],
                'comment' => $data['comment'],
                'judge_at' => date('Y-m-d H:i:s')
            ], ['id' => $data['id'], 'student_id' => $data['student_id'], 'comment' => null]);
            if ($affected === 1) {
                return '';
            }
        }

        $judge = TeachReserve::find()->select('score,comment,judge_at')->where($data)->andWhere([
            'NOT',
            ['comment' => null]
        ])->limit(1)->asArray()->one();
        if (null === $judge) {
            $judge['scores'] = $scores;
        } else {
            $judge['label'] = $scores[$judge['score']];
        }
        return $judge;
    }

    /**
     * 学生对已约的课设置备注,自己可见
     * @return array
     * @throws yii\db\Exception
     * @author grg
     */
    public function actionNote()
    {
        $data = Yii::$app->request->post();
        if (!array_key_exists('note', $data) || !array_key_exists('schedule_id', $data)) {
            return ['status' => '400'];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $affected = TeachReserve::updateAll(['note' => $data['note']], [
                'schedule_id' => $data['schedule_id'],
                'student_id' => Yii::$app->user->getId()
            ]);
            if (1 !== $affected) {
                $transaction->rollBack();
                return ['status' => '400'];
            }
            $transaction->commit();
            return ['status' => '200'];
        } catch (yii\db\Exception $e) {
            $transaction->rollBack();
            return ['status' => '400'];
        }
    }

    /**
     * 学生对已约的课和老师进行对话
     * @return array
     * @throws yii\db\Exception
     * @author grg
     */
    public function actionMessage()
    {
        $data = Yii::$app->request->post();
        if (!array_key_exists('content', $data) || !array_key_exists('reserve_id', $data)) {
            return ['status' => '400'];
        }
        $userId      = Yii::$app->user->getId();
        $username    = Yii::$app->user->identity->username;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $msg             = new TeachMessage();
            $msg->reserve_id = $data['reserve_id'];
            $msg->user_id    = $userId;
            $msg->username   = $username;
            $msg->content    = $data['content'];
            if (!$msg->save()) {
                $transaction->rollBack();
                return ['status' => '400'];
            }
            $transaction->commit();
            return ['status' => '200'];
        } catch (yii\db\Exception $e) {
            $transaction->rollBack();
            return ['status' => '400'];
        }
    }

    public function actionMessages()
    {
        $data = Yii::$app->request->post();
        if (!array_key_exists('reserve_id', $data)) {
            return ['status' => '400'];
        }
        $userId = Yii::$app->user->getId();

        $reserve = TeachReserve::find()->select('schedule_id,student_id')->where(['id' => $data['reserve_id']])->one();
        if (null === $reserve) {
            return ['status' => '400'];
        }
        $avatars = $this->avatar($reserve);
        $order   = array_key_exists('order', $data) && strtoupper($data['order']) === 'ASC' ? 'ASC' : 'DESC';
        $result  = TeachMessage::find()
                               ->select('user_id,username,concat("") avatar,content,created_at')
                               ->where(['reserve_id' => $data['reserve_id']])
                               ->asArray()
                               ->orderBy('id ' . $order)
                               ->limit(200)
                               ->all();
        //将对方的留言设为已读
        TeachMessage::updateAll(['read_status' => 1], [
            'AND',
            ['reserve_id' => $data['reserve_id']],
            ['NOT', ['user_id' => $userId]]
        ]);
        if (count($avatars) === 2) {
            foreach ($result as $key => $item) {
                $result[$key]['avatar'] = $avatars[$item['user_id']];
            }
        }
        return $result;
    }

    private function avatar($reserve)
    {
        $stuInfo  = StudentInfo::find()->select('avatar')->where(['user_id' => $reserve['student_id']])->one();
        $schedule = TeachSchedule::find()->select('teacher_id')->where(['id' => $reserve['schedule_id']])->one();
        if (null === $schedule) {
            return [];
        }
        $tchInfo = TeacherInfo::find()->select('avatar')->where(['user_id' => $schedule['teacher_id']])->one();

        $avatars[$reserve['student_id']]  = null !== $stuInfo ? $stuInfo['avatar'] : '';
        $avatars[$schedule['teacher_id']] = null !== $tchInfo ? $tchInfo['avatar'] : '';
        return $avatars;
    }
}
