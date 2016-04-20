<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_invite".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $username
 * @property string $inviter_id
 * @property integer $progress
 * @property string $created_at
 */
class Invites extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_invite';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['progress'], 'integer'],
            [['created_at'], 'safe'],
            [['user_id', 'inviter_id'], 'string', 'max' => 18],
            [['username'], 'string', 'max' => 40],
            [['user_id'], 'unique'],
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
            'username' => 'Username',
            'inviter_id' => '邀请者',
            'progress' => '进度',
            'created_at' => 'Created At',
        ];
    }
}
