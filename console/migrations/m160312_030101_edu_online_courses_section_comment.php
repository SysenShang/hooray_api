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

class m160312_030101_edu_online_courses_section_comment extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%online_courses_section_comment}}', [
            'comment_id' => $this->primaryKey(),
            'section_id' => $this->integer(8),
            'course_id' => $this->integer(8),
            'student_id' => $this->string(18)->notNull(),
            'student_name' => $this->string(40)->notNull()->defaultValue(''),
            'teacher_id' => $this->string(18)->notNull(),
            'teacher_name' => $this->string(40)->defaultValue(''),
            'title' => $this->string(100)->notNull()->defaultValue(''),
            'content' => $this->string(255)->notNull()->defaultValue(''),
            'rating' => $this->string(45)->notNull()->defaultValue(''),
            'update_time' => $this->timestamp()->notNull(),
            'create_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%online_courses_section_comment}}');
    }
}
