<?php

/**
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 4/5/16
 * Time: 10:57 AM
 * File: Invite.php
 */

namespace common\components;

use common\models\CoinLog;
use common\models\CommonOrder;
use common\models\InviterBonus;
use common\models\Invites;
use common\models\StuCount;
use common\models\TchCount;
use common\models\User;
use yii;

class Invite
{
    public static function done($userId, $username, $inviteCode)
    {
        $inviter = User::find()->select('user_id,username')->where(['invite_code' => $inviteCode])->asArray()->one();
        if (null === $inviter) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction(yii\db\Transaction::SERIALIZABLE);
        try {
            $invite = new Invites();

            $invite->user_id    = $userId;
            $invite->username   = $username;
            $invite->inviter_id = $inviter['user_id'];
            $invite->progress   = 0;
            $invite->save();
            $updated = InviterBonus::updateAllCounters(['num' => 1], ['inviter_id' => $inviter['user_id']]);
            if (0 === $updated) {
                $bonus = new InviterBonus();

                $bonus->num          = 1;
                $bonus->inviter_id   = $inviter['user_id'];
                $bonus->inviter_name = $inviter['username'];
                $bonus->bonus        = json_encode([
                    '1' => 0,//奖励条件(人数) => 奖励是否发放
                    '3' => 0,
                    '5' => 0,
                    '10' => 0
                ]);
                $bonus->save();
            }
            $detail = [
                'user_id' => $userId,
                'inviter_id' => $inviter['user_id']
            ];
            self::bonus($inviter['user_id'], '您邀请注册了一位用户,奖励哇哇豆(20).', $detail);
            $transaction->commit();
            return true;
        } catch (yii\db\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    private static function bonus($userId, $remark, $detail)
    {
        $bonus   = 20;
        $date    = date('Y-m-d H:i:s');
        $orderId = '60' . hexdec(uniqid('', false));

        $transaction = Yii::$app->db->beginTransaction(yii\db\Transaction::SERIALIZABLE);
        try {
            $affected = StuCount::updateAllCounters(['coin' => $bonus], ['user_id' => $userId]);
            if (1 !== $affected) {
                $affected = TchCount::updateAllCounters(['coin' => $bonus], ['user_id' => $userId]);
                if (1 !== $affected) {
                    $transaction->rollBack();
                    return ['status' => '9014'];
                }
            }

            $coinlog = new CoinLog();

            $coinlog->user_id    = $userId;
            $coinlog->order_id   = $orderId;
            $coinlog->order_type = 4;
            $coinlog->nums       = $bonus;
            $coinlog->type       = 1;
            $coinlog->remark     = $remark;
            $coinlog->detail     = serialize($detail);
            $coinlog->status     = 2;
            $coinlog->createtime = $date;
            if (!$coinlog->save()) {
                $transaction->rollBack();
                return ['status' => '9015'];
            }

            $commonOrder = new CommonOrder();

            $commonOrder->user_id    = $userId;
            $commonOrder->order_id   = $orderId;
            $commonOrder->order_type = 5;
            $commonOrder->price      = $bonus;
            $commonOrder->title      = $remark;
            $commonOrder->data       = json_encode($detail);
            $commonOrder->status     = 3;
            $commonOrder->createtime = $date;
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
     * @param $userId
     * @param $step
     * 0:注册
     * @author grg
     */
    public static function step($userId, $step)
    {
        $affected = Invites::updateAll([
            'progress' => $step
        ], [
            'user_id' => $userId,
            'progress' => 0
        ]);
        if (1 === $affected) {
            self::bonus($userId, '您被邀请注册,奖励哇哇豆(20).', []);
        }
    }
}
