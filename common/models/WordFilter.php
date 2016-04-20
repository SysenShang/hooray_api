<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_word_filter".
 *
 * @property integer $id
 * @property string $words
 * @property string $datetime
 */
class WordFilter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_word_filter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['datetime'], 'safe'],
            [['words'], 'string', 'max' => 100],
            [['words'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'words' => 'Words',
            'datetime' => 'Datetime',
        ];
    }
}
