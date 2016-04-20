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
class Advertisements extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_advertisements';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'block', 'is_click', 'image_url'], 'required'],
            [['is_delete', 'is_click', 'position', 'clicks', 'is_new_window', 'height', 'width'], 'integer'],
            [['title', 'image_url', 'target_url'], 'string', 'max' => 100],
            [['start', 'expire', 'created_at', 'updated_at'],'safe'],
            [['block'], 'string', 'max' => 50],
            [['device'], 'string', 'max' => 20],
            [['image_size'], 'string', 'max' => 10]
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
            'block' => 'Block',
            'image_size' => 'Image Size',
            'image_url' => 'Image Url',
            'is_delete'=>'Is Delete',
            'is_click'=>'Is click',
            'position'=>'Position',
            'clicks'=>'Clicks',
            'target_url' => 'Target Url',
            'start' => 'Start',
            'expire' => 'Expire',
            'device' => 'Device',
            'is_new_window' => 'Is New Window',
            'height' => 'Height',
            'width' => 'Width',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
