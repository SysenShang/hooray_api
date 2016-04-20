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

class m160312_030101_edu_ask_command_log extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%ask_command_log}}', [
            'id' => $this->primaryKey(),
            'order_sn' => $this->string(64)->notNull(),
            'question_id' => $this->integer(9)->notNull()->defaultValue(0),
            'remark' => $this->string(200)->notNull(),
            'reward' => $this->integer(9)->notNull()->defaultValue(0),
            'time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%ask_command_log}}');
    }
}
