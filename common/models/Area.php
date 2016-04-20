<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_area".
 *
 * @property integer $id
 * @property string $area_name
 * @property integer $parent_id
 * @property string $level
 * @property string $is_delete
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_area';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent_id'], 'integer'],
            [['area_name'], 'string', 'max' => 60],
            [['level', 'is_delete'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'area_name' => 'Area Name',
            'parent_id' => 'Parent ID',
            'level' => 'Level',
            'is_delete' => 'Is Delete',
        ];
    }
}
