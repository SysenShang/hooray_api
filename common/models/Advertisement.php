<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_advertisement".
 *
 * @property integer $id
 * @property string $title
 * @property integer $org_id
 * @property integer $style
 * @property string $url
 * @property integer $xstatus
 * @property integer $sort
 * @property string $target
 * @property string $md5
 */
class Advertisement extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_advertisement';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'style', 'url'], 'required'],
            [['org_id', 'style', 'xstatus', 'sort'], 'integer'],
            [['title'], 'string', 'max' => 100],
            [['url', 'md5'], 'string', 'max' => 255],
            [['target'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'org_id' => 'Org ID',
            'style' => 'Style',
            'url' => 'Url',
            'xstatus' => 'Xstatus',
            'sort' => 'Sort',
            'target' => 'Target',
            'md5' => 'Md5',
        ];
    }
}
