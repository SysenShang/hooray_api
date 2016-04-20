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

class m160312_030101_edu_common_comment extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%common_comment}}', [
            'comment_id' => $this->primaryKey(),
            'comment_style' => $this->integer(8)->notNull()->defaultValue(0),
            'target' => $this->integer(8),
            'student_id' => $this->string(18)->notNull()->defaultValue(''),
            'student_name' => $this->string(40)->notNull()->defaultValue(''),
            'teacher_id' => $this->string(18)->notNull()->defaultValue(''),
            'teacher_name' => $this->string(40)->defaultValue(''),
            'title' => $this->string(100)->notNull()->defaultValue(''),
            'content' => $this->string(255)->notNull()->defaultValue(''),
            'rating' => $this->string(45)->notNull()->defaultValue(''),
            'comment_rating' => $this->smallInteger(2)->defaultValue(5),
            'describe_teacher' => $this->string(20)->defaultValue(''),
            'update_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'create_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%common_comment}}');
    }
}
