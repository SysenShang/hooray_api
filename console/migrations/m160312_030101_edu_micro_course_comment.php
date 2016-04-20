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

class m160312_030101_edu_micro_course_comment extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%micro_course_comment}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(11)->notNull()->defaultValue(0),
            'micro_id' => $this->integer(11)->notNull()->defaultValue(0),
            'user_id' => $this->string(18)->notNull()->defaultValue(''),
            'title' => $this->string(100)->notNull()->defaultValue(''),
            'content' => $this->string(255)->notNull()->defaultValue(''),
            'update_time' => $this->timestamp()->notNull(),
            'create_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%micro_course_comment}}');
    }
}
