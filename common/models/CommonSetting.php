<?php
/** 
 * Created by Aptana studio. 
 * User: Kevin Henry Gates III at Hihooray,Inc 
 * Date: 2016/02/25  
 * Time: 08:37 AM 
 */
namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_common_setting".
 *
 * @property integer $id
 * @property string $scope
 * @property string $key
 * @property string $value
 */
class CommonSetting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_common_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value'], 'required'],
            [['scope'], 'string', 'max' => 50],
            [['key'], 'string', 'max' => 255],
            [['value'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'scope' => '区域',
            'key' => '属性',
            'value' => '值',
        ];
    }
}
