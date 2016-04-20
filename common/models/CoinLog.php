<?php

namespace common\models;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "edu_coin_log".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $order_id
 * @property integer $order_type
 * @property integer $nums
 * @property integer $type
 * @property string $remark
 * @property string $detail
 * @property integer $status
 * @property string $createtime
 */
class CoinLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_coin_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_type', 'nums', 'type', 'status'], 'integer'],
            [['detail'], 'string'],
            [['createtime'], 'safe'],
            [['user_id'], 'string', 'max' => 18],
            [['order_id'], 'string', 'max' => 64],
            [['remark'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'order_id' => 'Order ID',
            'order_type' => 'Order Type',
            'nums' => 'Nums',
            'type' => 'Type',
            'remark' => 'Remark',
            'detail' => 'Detail',
            'status' => 'Status',
            'createtime' => 'Createtime',
        ];
    }

    /**
     * 注册赠送哇哇豆
     * 老师注册成功后，将不再赠送20个哇豆,只送给学生
     */
    public function registerTask($user_id, $group_id)
    {
        $tasks  = Yii::$app->params['tasks'];
        $reward = $tasks[0]['lists'][0]['value'];
        $capacity = $group_id === 2 ? '老师' : '学生';

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $task = Task::findOne(['user_id' => $user_id]);
            if (null !== $task) {
                return false;
            }
            $score                  = new Task();
            $score['user_id']       = $user_id;
            //register free coins only for student,but still create one edu_task record.
            if ($group_id === 1) {
                $score['once_register'] = $reward;
                $score['total_scores'] = $reward;
                $score['today_scores'] = $reward;
            }

            if (!$score->save()) {
                $transaction->rollBack();
                return false;
            }
            //register free coins only for student
            if ($group_id === 1) {
                $exists = CoinLog::findOne(['user_id' => $user_id]);
                if (null !== $exists) {
                    //return false;
                }
                $coinLog = new CoinLog();
                $coinLog['user_id'] = $user_id;
                $coinLog['order_id'] = F::generateOrderSn('');
                $coinLog['order_type'] = 4;
                $coinLog['nums'] = $reward;
                $coinLog['type'] = 1;
                $coinLog['remark'] = "注册[$capacity]成功,赠送哇哇豆($reward).";
                $coinLog['detail'] = '';
                $coinLog['status'] = 2;
                $coinLog['createtime'] = date('Y-m-d H:i:s');
                if (!$coinLog->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }

            if ($group_id === 2) {
                $tchCount = TchCount::findOne(['user_id' => $user_id]);
                if (null === $tchCount) {
                    $tchCount = new TchCount();
                    $tchCount->user_id = $user_id;
                }
            } else {
                $stuCount = StuCount::findOne(['user_id' => $user_id]);
                if (null === $stuCount) {
                    $stuCount = new StuCount();
                    $stuCount->user_id = $user_id;
                }
                $stuCount->coin += $reward;
                if (!$stuCount->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }
            $transaction->commit();
        } catch (Exception $e) {
            return false;
        }
        //if (time() < strtotime('2015-11-09 23:59:59')) {
        //    $this->fixScore();
        //}
    }

    //public function fixScore()
    //{
    //    $reward = 50;
    //    $users  = Task::findAll(['once_register' => 0]);
    //    if (!empty($users)) {
    //        foreach ($users as $one) {
    //            $self = Task::findOne(['user_id' => $one['user_id']]);
    //
    //            $self['once_register'] = $reward;
    //            $self['total_scores'] += $reward;
    //            $self['today_scores'] += $reward;
    //            $self->save();
    //
    //            $user = User::findOne(['user_id' => $one['user_id']]);
    //
    //            $capacity = $user['group_id'] == 2 ? '老师' : '学生';
    //
    //            $coinLog = new CoinLog();
    //
    //            $coinLog['user_id']    = $one['user_id'];
    //            $coinLog['order_id']   = F::generateOrderSn('');
    //            $coinLog['order_type'] = 4;
    //            $coinLog['nums']       = $reward;
    //            $coinLog['type']       = 1;
    //            $coinLog['remark']     = "注册成功[$capacity],赠送哇哇豆($reward).";
    //            $coinLog['detail']     = '';
    //            $coinLog['status']     = 2;
    //            $coinLog['createtime'] = $user['regdate'];
    //            $coinLog->save();
    //            if ($user['group_id'] == 2) {
    //                $teacher_count = TchCount::findOne(['user_id' => $one['user_id']]);
    //                if (!empty($teacher_count)) {
    //                    $teacher_count['coin'] += $reward;
    //                    $teacher_count->save();
    //                }
    //            } else {
    //                $student_count = StuCount::findOne(['user_id' => $one['user_id']]);
    //                if (!empty($student_count)) {
    //                    $student_count['coin'] += $reward;
    //                    $student_count->save();
    //                }
    //            }
    //        }
    //
    //    }
    //}
}
