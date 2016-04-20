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

class m160312_030101_fpQueue extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('fpQueue', [
            'queueID' => $this->bigInteger(20)->notNull(),
            'workgroupID' => $this->bigInteger(20)->notNull(),
            'name' => $this->string(50)->notNull(),
            'description' => $this->string(255),
            'priority' => $this->integer(11)->notNull(),
            'maxchats' => $this->integer(11)->notNull(),
            'minchats' => $this->integer(11)->notNull(),
            'overflow' => $this->integer(11)->notNull(),
            'backupQueue' => $this->bigInteger(20),
            'PRIMARY KEY ([[queueID]], [[workgroupID]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('fpQueue');
    }
}
