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

class m160312_030101_fpSession extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('fpSession', [
            'sessionID' => $this->string(31)->notNull(),
            'userID' => $this->string(255)->notNull(),
            'workgroupID' => $this->bigInteger(20)->notNull(),
            'transcript' => $this->text(),
            'startTime' => $this->string(15)->notNull(),
            'endTime' => $this->string(15)->notNull(),
            'queueWaitTime' => $this->bigInteger(20),
            'state' => $this->integer(11)->notNull(),
            'caseID' => $this->string(20),
            'status' => $this->string(15),
            'notes' => $this->text(),
            'PRIMARY KEY ([[sessionID]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('fpSession');
    }
}
