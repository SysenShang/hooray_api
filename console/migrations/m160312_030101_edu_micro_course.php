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

class m160312_030101_edu_micro_course extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%micro_course}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull()->defaultValue(''),
            'user_id' => $this->string(18)->notNull()->defaultValue(''),
            'realname' => $this->string(20),
            'video_id' => $this->integer(11),
            'stage_id' => $this->integer(8)->notNull()->defaultValue(0),
            'stagename' => $this->string(20)->notNull()->defaultValue(''),
            'grade_id' => $this->integer(8)->notNull()->defaultValue(0),
            'gradename' => $this->string(20)->notNull()->defaultValue(''),
            'course_id' => $this->integer(8)->notNull()->defaultValue(0),
            'coursename' => $this->string(20)->notNull()->defaultValue(''),
            'price' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'description' => $this->string(255)->notNull()->defaultValue(''),
            'create_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'update_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'video_url' => $this->string(255)->notNull()->defaultValue(''),
            'video_small_image' => $this->string(255)->notNull()->defaultValue(''),
            'video_middle_image' => $this->string(255)->notNull()->defaultValue(''),
            'video_big_image' => $this->string(255)->notNull()->defaultValue(''),
            'video_duration' => $this->string(20)->defaultValue('00:00'),
            'content' => $this->text(),
            'free' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'publish' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'xstatus' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'isauth' => $this->smallInteger(1)->defaultValue(0),
            'persistentId' => $this->string(120),
            'isfop' => $this->smallInteger(1)->defaultValue(0),
            'authtime' => $this->timestamp()->defaultValue('0000-00-00 00:00:00'),
            'school_id' => $this->integer(11)->defaultValue(0),
            'access' => $this->smallInteger(1)->defaultValue(0),
            'viewnums' => $this->bigInteger(20)->defaultValue(1),
            'favnums' => $this->bigInteger(20)->defaultValue(0),
            'buynums' => $this->bigInteger(20)->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%micro_course}}');
    }
}
