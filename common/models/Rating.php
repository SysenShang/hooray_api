<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_rating".
 *
 * @property integer $id
 * @property string $xtype
 * @property string $rating
 * @property string $rating_img
 * @property string $rating_desc
 * @property string $conditions
 * @property string $rate
 */
class Rating extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_rating';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['xtype'], 'string'],
            [['rating_img'], 'required'],
            [['rating', 'rating_desc', 'conditions'], 'string', 'max' => 100],
            [['rating_img'], 'string', 'max' => 255],
            [['rate'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'xtype' => 'Xtype',
            'rating' => 'Rating',
            'rating_img' => 'Rating Img',
            'rating_desc' => 'Rating Desc',
            'conditions' => 'Conditions',
            'rate' => 'Rate',
        ];
    }
}
