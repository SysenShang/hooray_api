<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_pc_version_publish".
 *
 * @property string $version_name
 * @property integer $version_number
 * @property string $version_url
 * @property string $filemd5
 * @property integer $vid
 * @property integer $enforce
 * @property string $explain
 */
class PcVersion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_pc_version_publish';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['version_number', 'vid', 'enforce'], 'integer'],
            [['explain'], 'string'],
            [['version_name'], 'string', 'max' => 50],
            [['version_url'], 'string', 'max' => 100],
            [['filemd5'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'version_name' => 'Version Name',
            'version_number' => 'Version Number',
            'version_url' => 'Version Url',
            'filemd5' => 'Filemd5',
            'vid' => 'Vid',
            'enforce' => 'Enforce',
            'explain' => 'Explain',
        ];
    }
}
