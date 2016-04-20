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

class m160312_030101_edu_tool_question_config extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%tool_question_config}}', [
            'id' => $this->primaryKey(),
            'type' => $this->smallInteger(4),
            'title' => $this->string(50),
            'week' => $this->integer(1),
            'time' => $this->integer(4),
            'config' => $this->text(),
            'status' => $this->smallInteger(4),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%tool_question_config}}');
    }
}
