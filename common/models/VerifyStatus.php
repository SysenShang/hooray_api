<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_verify_status".
 *
 * @property integer $id
 * @property string $device
 * @property string $version
 * @property string $type
 * @property integer $verify
 */
class VerifyStatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_verify_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['verify'], 'integer'],
            [['device'], 'string', 'max' => 20],
            [['version', 'type'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'device' => 'Device',
            'version' => 'Version',
            'type' => 'Type',
            'verify' => 'Verify',
        ];
    }
}
