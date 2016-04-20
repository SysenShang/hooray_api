<?php

namespace common\models;

use common\components\JPushNotice;
use Yii;

/**
 * This is the model class for table "edu_credit_rule".
 *
 * @property integer $rid
 * @property string $rulename
 * @property string $action
 * @property integer $cycletype
 * @property integer $cycletime
 * @property integer $rewardnum
 * @property integer $coin
 * @property integer $credits
 * @property integer $group_id
 * @property double $rates
 */
class CreditRule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_credit_rule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cycletype', 'cycletime', 'rewardnum', 'coin', 'credits', 'group_id'], 'integer'],
            [['rates'], 'number'],
            [['rulename', 'action'], 'string', 'max' => 30],
            [['action'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rid' => 'Rid',
            'rulename' => 'Rulename',
            'action' => 'Action',
            'cycletype' => 'Cycletype',
            'cycletime' => 'Cycletime',
            'rewardnum' => 'Rewardnum',
            'coin' => 'Coin',
            'credits' => 'Credits',
            'group_id' => 'Group ID',
            'rates' => 'Rates',
        ];
    }

    //学生注册
    public function studentRegister($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '1';
        $createarray ['rid'] = '5';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['userbasic'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();

        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send([
                $uid
            ], [
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo['message']['rating']
            ]);
        }
    }

    //学生登录
    public function studentLogin($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '1';
        $createarray ['rid'] = '9';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['userbasic'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send([
                $uid
            ], [
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating']
            ]);
        }

        if ($creditinfo ['error'] == '200') {
            $jpush->send([
                $uid
            ], [
                'type' => '1005',
                'title' => '恭喜您成功登录，送您' . $creditinfo['message']['coin'] . '个哇哇豆！'
            ]);
        }

    }

    //学生验证邮箱
    public function studentVerifyEmail($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '1';
        $createarray ['rid'] = '6';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['userbasic'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send([
                $uid
            ], [
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating']
            ]);
        }
    }

    //学生反馈
    public function studentFeedback($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '1';
        $createarray ['rid'] = '7';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['userbasic'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send([
                $uid
            ], [
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating']
            ]);
        }
    }

    //学生提问
    public function studentQuestionSubmit($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '1';
        $createarray ['rid'] = '13';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['question'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send(array(
                $uid
            ), array(
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating'],
                'time' => date('Y-m-d H:i:s')
            ));
        }
    }

    //学生试听云课
    public function studentOlCouresesAudition($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '1';
        $createarray ['rid'] = '10';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['onlineCourses'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send(array(
                $uid
            ), array(
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating'],
                'time' => date('Y-m-d H:i:s')
            ));
        }
    }

    //学生购买云课
    public function studentOlCouresesBuy($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '1';
        $createarray ['rid'] = '12';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['onlineCourses'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send(array(
                $uid
            ), array(
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating'],
                'time' => date('Y-m-d H:i:s')
            ));
        }
    }

    //学生购买微课
    public function studentCouresesBuy($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '1';
        $createarray ['rid'] = '11';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['courses'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send(array(
                $uid
            ), array(
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating'],
                'time' => date('Y-m-d H:i:s')
            ));
        }
    }

    //学生充值
    public function studentRecharge($wawadou, $uid, $relatedid = "0")
    {
        if ($wawadou >= 100) {
            $createarray ['wawadou'] = 100;
        } else {
            $createarray ['wawadou'] = $wawadou;
        }
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '1';
        $createarray ['rid'] = '8';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['userbasic'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send(array(
                $uid
            ), array(
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating'],
                'time' => date('Y-m-d H:i:s')
            ));
        }
    }

    //学生评论
    public function studentComment($uid, $relatedid = "0",$group_id)
    {
        $createarray['user_id'] = $uid;
        $createarray['group_id'] = '1';
        $createarray['rid'] = '14';
        $createarray['relatedid'] = $relatedid;
        $createarray['plate'] = Yii::$app->params['credit_plate']['comment'];
        $createarray['group_id'] = $group_id;
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send(array(
                $uid
            ), array(
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating'],
                'time' => date('Y-m-d H:i:s')
            ));
        }
    }


    //老师注册
    public function teacherRegister($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '2';
        $createarray ['rid'] = '15';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params['credit_plate'] ['userbasic'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send(array(
                $uid
            ), array(
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating'],
                'time' => date('Y-m-d H:i:s')
            ));
        }
    }

    //老师登录
    public function teacherLogin($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '2';
        $createarray ['rid'] = '20';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['userbasic'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send(array(
                $uid
            ), array(
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating'],
                'time' => date('Y-m-d H:i:s')
            ));
        }
    }

    //老师邮箱验证
    public function teacherVerifyEmail($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '2';
        $createarray ['rid'] = '16';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['userbasic'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send(array(
                $uid
            ), array(
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating'],
                'time' => date('Y-m-d H:i:s')
            ));
        }
    }

    //老师反馈
    public function teacherFeedback($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '2';
        $createarray ['rid'] = '17';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['userbasic'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send(array(
                $uid
            ), array(
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating'],
                'time' => date('Y-m-d H:i:s')
            ));
        }
    }

    //老师解答问题
    public function teacherQuestion($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '2';
        $createarray ['rid'] = '21';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['question'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send(array(
                $uid
            ), array(
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating'],
                'time' => date('Y-m-d H:i:s')
            ));
        }
    }

    //老师完成课程
    public function teacherOnlineCoureses($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '2';
        $createarray ['rid'] = '22';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['onlineCourses'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send(array(
                $uid
            ), array(
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating'],
                'time' => date('Y-m-d H:i:s')
            ));
        }
    }

    //老师完成身份认证
    public function teacherIdentity($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '2';
        $createarray ['rid'] = '18';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['userbasic'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send(array(
                $uid
            ), array(
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating'],
                'time' => date('Y-m-d H:i:s')
            ));
        }
    }

    //老师完成开课认证
    public function teacherVerifyCoureses($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '2';
        $createarray ['rid'] = '19';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['userbasic'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send(array(
                $uid
            ), array(
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating'],
                'time' => date('Y-m-d H:i:s')
            ));
        }
    }

    //首次提现
    public function teacherWithdraw($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '2';
        $createarray ['rid'] = '24';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['userbasic'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send(array(
                $uid
            ), array(
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating'],
                'time' => date('Y-m-d H:i:s')
            ));
        }
    }

    //发布微课
    public function teacherCoureses($uid, $relatedid = "0")
    {
        $createarray ['user_id'] = $uid;
        $createarray ['group_id'] = '2';
        $createarray ['rid'] = '23';
        $createarray ['relatedid'] = $relatedid;
        $createarray ['plate'] = Yii::$app->params ['credit_plate'] ['userbasic'];
        $creditinfo = self::addCredit($createarray);
        $jpush = new JPushNotice ();
        // 等级是否改变
        if (isset ($creditinfo ['message'] ['rating'])) {
            $jpush->send(array(
                $uid
            ), array(
                'type' => '1006',
                'title' => '恭喜！取得了新的等级',
                'rating' => $creditinfo ['message'] ['rating'],
                'time' => date('Y-m-d H:i:s')
            ));
        }
    }


    /*
     * 等级与积分关系
     */
    private function rating($credit)
    {
        $rule = array(
            '1' => array(0, 500),
            '2' => array(501, 2000),
            '3' => array(2001, 6000),
            '4' => array(6001, 12000),
            '5' => array(12001, 20000),
            '6' => array(20001, 30000),
            '7' => array(30001, 50000),
            '8' => array(50001, 80000),
            '9' => array(80001, 130000),
            '10' => array(130001),
        );
        $r = 1;
        foreach ($rule as $k => $v) {
            if ($k != 10) {
                if ($credit >= $v['0'] && $credit <= $v['1']) {
                    $r = $k;
                    break;
                }
            }
            if ($k == 10) {
                if ($credit >= $v['0']) {
                    break;
                }
            }
        }
        return $k;
    }

    /*
     * 查询积分规则
     */
    private function FindRule($rid)
    {
        $res = CreditRule::findOne(['rid' => $rid]);
        return $res;
    }

    /*
     * rule_log 存在检查
     */
    private function FindRuleLog($uid, $rid)
    {
        $res = CreditRuleLog::findOne(['uid' => $uid, 'rid' => $rid]);
        return $res;
    }

    /*
     * 检查周期内数量是否超过
     */
    private function IsNums($ruleres, $rulelogres)
    {
        $cycletype = $ruleres['cycletype'];
        switch ($cycletype) {
            //永远不限制
            case 0:
                return true;
                break;
            //永远仅 n次
            case 1:
                if ($ruleres['rewardnum'] > $rulelogres['total']) {
                    return true;
                } else {
                    return false;
                }
                break;
            //每天仅n次
            case 2:
                if ($ruleres['rewardnum'] > $rulelogres['cyclenum']) {
                    return true;
                } else {
                    return false;
                }
                break;
            //n间隔时间 n次
            case 3:
                if ($ruleres['rewardnum'] > $rulelogres['cyclenum']) {
                    return true;
                } else {
                    return false;
                }

        }
    }

    /*
     * rule_log 是否周期时间之内
     */
    private function IsCycle($ruleres, $rulelogres)
    {
        $cycletype = $ruleres->cycletype;
        $cycletime = $ruleres->cycletime;
        $starttime = strtotime($rulelogres->starttime);
        $dateline = strtotime($rulelogres->dateline);
        $nowtime = time();
        switch ($cycletype) {
            case 0:
                return false;
                break;
            case 1:
                return false;
                break;
            case 2:
                // 每天
                $oldday = date('Y-m-d', $starttime);
                $nowday = date('Y-m-d', $nowtime);
                if ($nowday != $oldday) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 3:
                // 间隔时间
                $tag = ($starttime + $cycletime) < $nowtime;
                if ($tag) {
                    return true;
                } else {
                    return false;
                }
                break;
        }
    }

    /*
     *查询账户信息
     */
    private function getCount($count_table, $uid)
    {
        $db = YII::$app->db;
        $ratingsql = "SELECT credits,rating FROM " . $count_table . " WHERE user_id='$uid'";
        $res = $db->createCommand($ratingsql)->queryOne();
        return $res;
    }

    /*
     * 返回信息
     */
    private function Error($id, $messqge)
    {
        $data = array(
            'error' => $id,
            'message' => $messqge
        );
        return $data;
    }

    /**
     *
     * 用户添加积分记录
     *
     * @access public
     * @param unknown $createarray
     *            rid规则ID uid用户id operation操作项 relatedid关系ID
     * @return boolean
     * @version 1.0.0 (2014-4-28)
     * @author zhaowang
     */
    private function addCredit($createarray)
    {
        $uid = $createarray['user_id'];
        $group_id = $createarray['group_id'];
        //$uid='467974464261324800';
        //$group_id='1';
        $rid = $createarray['rid'];
        $relatedid = $createarray['relatedid'];
        $fid = $createarray['plate'];
        $dateline = date('Y-m-d H:i:s');

        // 查询规则
        $ruleres = $this->FindRule($rid);
        if (!$ruleres) {
            return $this->Error('5501', '没有找到规则ID');
        }

        $operation = $ruleres->action;
        // 如果规则加币了
        if ($ruleres->coin != '0') {
            //将写入coin的历史记录
            $order_id = F::generateOrderSn('');
            $remark = $ruleres->rulename . "赠送哇哇豆.($ruleres->coin)";
            $coin_log = new CoinLog();
            $coin_log->user_id = $uid;
            $coin_log->order_id = $order_id;
            $coin_log->order_type = 8;
            $coin_log->nums = $ruleres->coin;
            $coin_log->type = 1;
            $coin_log->remark = $remark;
            $coin_log->createtime = $dateline;
            $coin_log->save();
        }

        // 如果哇哇豆存在 则用应用的哇哇豆
        if (isset($createarray['wawadou'])) {
            $credits = round($createarray['wawadou'] * $ruleres->rates);
        } else {
            $credits = $ruleres->credits;
        }

        $datainfo['coin'] = $coin = $ruleres->coin;
        $rulelogres = $this->FindRuleLog($uid, $rid);

        if (!empty($ruleres) && !empty($rulelogres)) {
            // 查询是否在周期内
            $iscycle = $this->IsCycle($ruleres, $rulelogres);

            // 在周期之内，验证数量 ，否则不验证
            if (!$iscycle) {
                // 数量验证
                $isnums = $this->IsNums($ruleres, $rulelogres);
                if (!$isnums) {
                    return $this->Error('5502', '超出了规则的数量');
                }
            }
        }

        // 写入LOG
        $credit_log = new CreditLog();
        $credit_log->uid = $uid;
        $credit_log->operation = $operation;
        $credit_log->relatedid = $relatedid;
        $credit_log->dateline = $dateline;
        $credit_log->credits = $credits;
        $credit_log->coin = $coin;
        $credit_log->save();

        if($group_id == 2){
            $tch_count = TchCount::find()->select('rating', 'credits','coin')->where(['user_id' => $uid])->one();
            if (null !== $tch_count) {
                $count_credits = $tch_count->credits + $credits;

                $rating = $this->rating($count_credits);

                //更新老师统计信息
                $tch_count->credits = $tch_count->credits + $credits;
                $tch_count->coin = $tch_count->coin + $coin;
                $tch_count->rating = $rating;
                $tch_count->update();
            }
        }else{
            $stu_count = StuCount::find()->select('rating', 'credits','coin')->where(['user_id' => $uid])->one();
            if (null !== $stu_count) {
                $count_credits = $stu_count->credits + $credits;

                $rating = $this->rating($count_credits);

                //更新老师统计信息
                $stu_count->credits = $stu_count->credits + $credits;
                $stu_count->coin = $stu_count->coin + $coin;
                $stu_count->rating = $rating;
                $stu_count->update();
            }
        }



        // 写入 RULE_LOG
        if (!$this->FindRuleLog($uid, $rid)) {
            $credit_rule_log = new CreditRuleLog();
            $credit_rule_log->uid = $uid;
            $credit_rule_log->rid = $rid;
            $credit_rule_log->fid = $fid;
            $credit_rule_log->total = 1;
            $credit_rule_log->cyclenum = 1;
            $credit_rule_log->uid = $uid;
            $credit_rule_log->starttime = $dateline;
            $credit_rule_log->dateline = $dateline;
            $credit_rule_log->credits = $credits;
            $credit_rule_log->coin = $coin;
            $credit_rule_log->save();

        } else {
            // 验证现在的时间 是否在周期内
            $credit_rule_log = CreditRuleLog::findOne(['uid' => $uid, 'rid' => $rid]);
            if (!$iscycle) {
                // 周期之内
                $credit_rule_log->total = $credit_rule_log->total + 1;
                $credit_rule_log->cyclenum = $credit_rule_log->cyclenum + 1;
                $credit_rule_log->coin = $coin;
                $credit_rule_log->dateline = $dateline;
                $credit_rule_log->update();

            } else {
                // 周期之外
                $credit_rule_log->total = $credit_rule_log->total + 1;
                $credit_rule_log->cyclenum = 1;
                $credit_rule_log->starttime = $dateline;
                $credit_rule_log->dateline = $dateline;
                $credit_rule_log->update();
            }
        }
    }

    /**
     *
     * 用户积分查看
     *
     * @access public
     * @return return_type
     * @version 1.0.0 (2014-7-7)
     * @author zhaowang
     */
    public function getCredit($uid, $group_id)
    {
        $uid = '478819691414945792';
        $group_id = '1';

        // 对应身份的表
        if ($group_id == '1') {
            $count_table = "edu_passport_student_count";
        } elseif ($group_id == '2') {
            $count_table = "edu_passport_teacher_count";
        }
        $sql = "SELECT credits FROM " . $count_table . " WHERE user_id='$uid'";
        $db = YII::$app->db;
        $credits = $db->createCommand($sql)->queryone();
        return $credits['credits'];
    }
}
