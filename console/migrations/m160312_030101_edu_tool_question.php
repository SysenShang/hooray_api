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

class m160312_030101_edu_tool_question extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%tool_question}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull()->defaultValue(''),
            'content' => $this->text(),
            'grade_id' => $this->integer(11)->notNull()->defaultValue(0),
            'grade_name' => $this->string(40)->notNull()->defaultValue(''),
            'subject_id' => $this->integer(11)->notNull()->defaultValue(0),
            'subject_name' => $this->string(40)->notNull()->defaultValue(''),
            'status' => $this->integer(11)->defaultValue(0),
            'attachments' => $this->string(200)->defaultValue(''),
            'create_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'update_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'question_type_id' => $this->integer(11),
            'question_type_name' => $this->string(40),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%tool_question}}');
    }
}
