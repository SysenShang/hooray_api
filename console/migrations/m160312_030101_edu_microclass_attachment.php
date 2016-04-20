<?php
/**
 * Created by Aptana studio.
 * Author: Kevin Henry Gates III at Hihooray,Inc
 * Date: 2016/03/12
 * Time: 11:01
 * Email: zhouwensheng@hihooray.com
 * migration
 */
use yii\db\Schema;

class m160312_030101_edu_microclass_attachment extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%microclass_attachment}}', [
            'micro_id' => $this->primaryKey(),
            'microclass_name' => $this->string(100)->notNull()->defaultValue(''),
            'user_id' => $this->string(18)->notNull()->defaultValue(''),
            'realname' => $this->string(20),
            'stage_id' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'stagename' => $this->string(20)->notNull()->defaultValue(''),
            'grade_id' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'gradename' => $this->string(20)->notNull()->defaultValue(''),
            'course_id' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'coursename' => $this->string(20)->notNull()->defaultValue(''),
            'price' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'description' => $this->string(255)->notNull()->defaultValue(''),
            'create_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'update_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'vedio_url' => $this->string(255)->notNull()->defaultValue(''),
            'file_name' => $this->string(255)->notNull()->defaultValue(''),
            'filesize' => $this->string(255)->notNull()->defaultValue(''),
            'readperm' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'vedio_small_image' => $this->string(255)->notNull()->defaultValue(''),
            'vedio_middle_image' => $this->string(255)->notNull()->defaultValue(''),
            'vedio_big_image' => $this->string(255)->notNull()->defaultValue(''),
            'free' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'publish' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'xstatus' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'isauth' => $this->smallInteger(1)->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%microclass_attachment}}');
    }
}
