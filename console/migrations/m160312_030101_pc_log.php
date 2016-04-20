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

class m160312_030101_pc_log extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('pc_log', [
            'id' => $this->integer(11)->notNull(),
            'os_version' => $this->string(255),
            'soft_version' => $this->string(255),
            'brand' => $this->string(255),
            'stack_trace' => $this->text(),
            'excetion_message' => $this->text()->notNull(),
            'excetion_source' => $this->text()->notNull(),
            'exception_target_site' => $this->text()->notNull(),
            'messages' => $this->text()->notNull(),
            'crash_datetime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'app_start_datetime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'username' => $this->string(100)->notNull(),
            'PRIMARY KEY ([[id]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('pc_log');
    }
}
