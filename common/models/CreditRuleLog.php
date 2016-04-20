<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_credit_rule_log".
 *
 * @property integer $clid
 * @property string $uid
 * @property integer $rid
 * @property integer $fid
 * @property integer $total
 * @property integer $cyclenum
 * @property integer $credits
 * @property integer $coin
 * @property string $starttime
 * @property string $dateline
 */
class CreditRuleLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_credit_rule_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rid', 'fid', 'total', 'cyclenum', 'credits', 'coin'], 'integer'],
            [['starttime', 'dateline'], 'safe'],
            [['uid'], 'string', 'max' => 18]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'clid' => 'Clid',
            'uid' => 'Uid',
            'rid' => 'Rid',
            'fid' => 'Fid',
            'total' => 'Total',
            'cyclenum' => 'Cyclenum',
            'credits' => 'Credits',
            'coin' => 'Coin',
            'starttime' => 'Starttime',
            'dateline' => 'Dateline',
        ];
    }
}
