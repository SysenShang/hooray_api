<?php
/** 
 * Created by Aptana studio. 
 * User: Kevin Henry Gates III at Hihooray,Inc 
 * Date: 2015/11/24  
 * Time: 03:14 AM 
 */
namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_teacher_files".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $persistentId
 * @property string $inputBucket
 * @property string $inputKey
 * @property string $cmd
 * @property string $hash
 * @property string $itemsKey
 * @property string $pipeline
 * @property string $reqid
 * @property integer $page_num
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 */
class TeacherFiles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'edu_teacher_files';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'persistentId'], 'required'],
            [['page_num'], 'integer'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'string', 'max' => 18],
            [['persistentId', 'inputBucket', 'reqid'], 'string', 'max' => 35],
            [['inputKey', 'hash', 'pipeline'], 'string', 'max' => 60],
            [['cmd'], 'string', 'max' => 100],
            [['itemsKey'], 'string', 'max' => 150]
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
            'persistentId' => 'Persistent ID',
            'inputBucket' => 'Input Bucket',
            'inputKey' => 'Input Key',
            'cmd' => 'Cmd',
            'hash' => 'Hash',
            'itemsKey' => 'Items Key',
            'pipeline' => 'Pipeline',
            'reqid' => 'Reqid',
            'page_num' => 'Page Num',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
