<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_credit_log".
 *
 * @property integer $logid
 * @property string $uid
 * @property string $operation
 * @property integer $relatedid
 * @property string $dateline
 * @property integer $credits
 * @property integer $coin
 */
class CreditLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_credit_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['relatedid'], 'required'],
            [['relatedid', 'credits', 'coin'], 'integer'],
            [['dateline'], 'safe'],
            [['uid'], 'string', 'max' => 18],
            [['operation'], 'string', 'max' => 35]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'logid' => 'Logid',
            'uid' => 'Uid',
            'operation' => 'Operation',
            'relatedid' => 'Relatedid',
            'dateline' => 'Dateline',
            'credits' => 'Credits',
            'coin' => 'Coin',
        ];
    }
}
