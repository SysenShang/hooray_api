<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_micro_course_view_count".
 *
 * @property integer $id
 * @property string $user_id
 * @property integer $micro_id
 * @property integer $view_counts
 * @property string $created_time
 * @property string $updated_time
 * @property string $ip
 */
class MicroCourseViewCount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_micro_course_view_count';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'micro_id', 'view_counts'], 'integer'],
            [[ 'user_id','created_time', 'updated_time', 'ip'], 'safe'],
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
            'micro_id' => 'Micro ID',
            'view_counts' => 'View Counts',
            'created_time' => 'Created time',
            'updated_time' => 'Updated time',
            'ip' => 'IP',
        ];
    }
}
