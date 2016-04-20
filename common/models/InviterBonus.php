<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_inviter_bonus".
 *
 * @property integer $id
 * @property string $inviter_id
 * @property string $inviter_name
 * @property integer $num
 * @property string $bonus
 * @property string $updated_at
 */
class InviterBonus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_inviter_bonus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['num'], 'integer'],
            [['updated_at'], 'safe'],
            [['inviter_id'], 'string', 'max' => 18],
            [['inviter_name'], 'string', 'max' => 50],
            [['bonus'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inviter_id' => '邀请者',
            'inviter_name' => 'Inviter Name',
            'num' => '人数',
            'bonus' => 'JSON,奖励',
            'updated_at' => 'Updated At',
        ];
    }
}
