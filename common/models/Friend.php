<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_friend".
 *
 * @property integer $id
 * @property string $fromId
 * @property string $toId
 * @property string $createdTime
 * @property string $title
 */
class Friend extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_friend';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['createdTime'], 'safe'],
            [['fromId', 'toId'], 'string', 'max' => 18],
            [['title'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fromId' => 'From ID',
            'toId' => 'To ID',
            'createdTime' => 'Created Time',
            'title' => 'Title',
        ];
    }
}
