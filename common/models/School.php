<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_school".
 *
 * @property integer $id
 * @property integer $area_id
 * @property integer $level
 * @property string $name
 * @property string $created_at
 */
class School extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_school';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['area_id', 'level'], 'integer'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 60]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'area_id' => 'Area ID',
            'level' => 'Level',
            'name' => 'Name',
            'created_at' => 'Created At',
        ];
    }
}
