<?php
namespace common\models;

use Yii;


use yii\db\ActiveRecord;


/**
 * Checkin model
 *
 * @property integer $id
 * @property string $user_id
 * @property string $signed_date_time
 * @property string $signed_days
 */
//class Checkin extends ActiveRecord
class Checkin extends ActiveRecord
{	
    /**
     * @table name
     */
    public static function tableName()
    {
        return 'edu_passport_user_checkins';
    }
	
    /**
     * 数据过滤
     */
    public function fields()
    {
        $fields = parent::fields();

        return $fields;
    }

    /**
     * @rules
     */
    public function rules()
    {
        return [
        	[['signed_days', 'signed_date_time'], 'integer'],
            [['user_id'], 'string', 'max' => 18],
            [['user_id'], 'unique']
        ];
    }
	
    /**
     * @find Identity
     */
    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id]);
    }
}
