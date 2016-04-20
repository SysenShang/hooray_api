<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_favorites".
 *
 * @property integer $id
 * @property integer $resource_id
 * @property string $user_id
 * @property integer $type
 * @property integer $status
 * @property string $createtime
 * @property string $resource_type
 */
class Favorites extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_favorites';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['resource_id', 'type', 'status'], 'integer'],
            [['createtime'], 'safe'],
            [['user_id'], 'string', 'max' => 18],
            [['resource_type'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '收藏ID',
            'resource_id' => '资源ID',
            'user_id' => '用户ID',
            'type' => '类型',
            'status' => '状态:0有效',
            'createtime' => '创建时间',
            'resource_type' => 'Resource Type',
        ];
    }
}
