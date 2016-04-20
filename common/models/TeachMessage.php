<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_direct_teach_message".
 *
 * @property integer $id
 * @property integer $reserve_id
 * @property string $user_id
 * @property string $username
 * @property string $content
 * @property integer $read_status
 * @property string $created_at
 */
class TeachMessage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_direct_teach_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reserve_id'], 'required'],
            [['reserve_id', 'read_status'], 'integer'],
            [['created_at'], 'safe'],
            [['user_id'], 'string', 'max' => 18],
            [['username'], 'string', 'max' => 40],
            [['content'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reserve_id' => 'Reserve ID',
            'user_id' => 'User ID',
            'username' => 'Username',
            'content' => 'Content',
            'read_status' => '0:未读 1:对方已读',
            'created_at' => 'Created At',
        ];
    }
}
