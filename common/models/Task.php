<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_task".
 *
 * @property integer $id
 * @property string $user_id
 * @property integer $total_scores
 * @property integer $today_scores
 * @property integer $once_register
 * @property integer $once_complete
 * @property integer $everyday_checkin
 * @property string $lastdate_checkin
 * @property integer $everyday_weike
 * @property string $lastdate_weike
 * @property integer $everyday_share
 * @property string $lastdate_share
 * @property integer $serial_checkin
 * @property integer $everyday_ask
 * @property string $lastdate_ask
 * @property integer $serial_ask
 * @property string $updated_at
 * @property integer $everyday_judge
 * @property string $lastdate_judge
 * @property integer $everyday_buyweike
 * @property string $lastdate_buyweike
 * @property integer $everyday_judgeweike
 * @property string $lastdate_judgeweike
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['total_scores', 'today_scores', 'once_register', 'once_complete', 'everyday_checkin', 'everyday_weike', 'everyday_share', 'serial_checkin', 'everyday_ask', 'serial_ask', 'everyday_judge', 'everyday_buyweike', 'everyday_judgeweike'], 'integer'],
            [['lastdate_checkin', 'lastdate_weike', 'lastdate_share', 'lastdate_ask', 'updated_at', 'lastdate_judge', 'lastdate_buyweike', 'lastdate_judgeweike'], 'safe'],
            [['user_id'], 'string', 'max' => 18],
            [['user_id'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'total_scores' => '总数',
            'today_scores' => '今日总数',
            'once_register' => '注册得分',
            'once_complete' => '完善资料得分',
            'everyday_checkin' => '每日签到得分',
            'lastdate_checkin' => '最后签到时间',
            'everyday_weike' => '每日点击微课得分',
            'lastdate_weike' => '最后点击微课时间',
            'everyday_share' => '每日分享得分',
            'lastdate_share' => '最后分享时间',
            'serial_checkin' => '连续签到得分',
            'everyday_ask' => 'Everyday Ask',
            'lastdate_ask' => 'Lastdate Ask',
            'serial_ask' => 'Serial Ask',
            'updated_at' => 'Updated At',
            'everyday_judge' => '每日评价',
            'lastdate_judge' => '最后评价时间',
            'everyday_buyweike' => '每日购买微课',
            'lastdate_buyweike' => '最后购买微课时间',
            'everyday_judgeweike' => '每日评价微课',
            'lastdate_judgeweike' => '最后评价微课时间',
        ];
    }
}
