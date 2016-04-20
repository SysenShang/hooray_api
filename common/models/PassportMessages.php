<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_passport_messages".
 *
 * @property integer $id
 * @property string $sender
 * @property integer $type
 * @property integer $cat_id
 * @property string $user_id
 * @property string $message
 * @property integer $isdel
 * @property integer $status
 * @property string $reg_date
 * @property string $upd_date
 */
class PassportMessages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_passport_messages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'cat_id', 'isdel', 'status'], 'integer'],
            [['message'], 'string'],
            [['reg_date', 'upd_date'], 'safe'],
            [['sender', 'user_id'], 'string', 'max' => 18]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'sender' => '发送者id:0为系统消息',
            'type' => '消息类型:0消息,1提醒',
            'cat_id' => '类型id类型值待定',
            'user_id' => '发信息用户id',
            'message' => '消息内容',
            'isdel' => '删除状态:0未删除，1已删除',
            'status' => '状态:0未读，1已读',
            'reg_date' => '任务添加时间',
            'upd_date' => '更新时间',
        ];
    }
}
