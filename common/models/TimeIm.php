<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_time_im".
 *
 * @property integer $id
 * @property integer $roomid
 * @property string $operator
 * @property string $valid
 * @property string $at_time
 */
class TimeIm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_time_im';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['roomid'], 'integer'],
            [['at_time'], 'safe'],
            [['operator'], 'string', 'max' => 18],
            [['valid'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'roomid' => 'Roomid',
            'operator' => 'Operator',
            'valid' => 'Valid',
            'at_time' => 'At Time',
        ];
    }
}
