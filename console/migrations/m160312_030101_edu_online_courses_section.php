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

class m160312_030101_edu_online_courses_section extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%online_courses_section}}', [
            'section_id' => $this->primaryKey(),
            'course_id' => $this->integer(8),
            'title' => $this->string(100)->notNull(),
            'start_time' => $this->timestamp(),
            'end_time' => $this->timestamp()->defaultValue('0000-00-00 00:00:00'),
            'update_time' => $this->timestamp()->defaultValue('0000-00-00 00:00:00'),
            'create_time' => $this->timestamp()->defaultValue('0000-00-00 00:00:00'),
            'file' => $this->string(255)->notNull()->defaultValue(''),
            'schedule_id' => $this->string(60)->defaultValue('0'),
            'status' => $this->smallInteger(2)->defaultValue(0),
            'section_order' => $this->integer(11)->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%online_courses_section}}');
    }
}
