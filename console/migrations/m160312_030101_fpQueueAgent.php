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

class m160312_030101_fpQueueAgent extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('fpQueueAgent', [
            'queueID' => $this->bigInteger(20)->notNull(),
            'objectID' => $this->bigInteger(20)->notNull(),
            'objectType' => $this->integer(11)->notNull(),
            'administrator' => $this->integer(11),
            'PRIMARY KEY ([[queueID]], [[objectID]], [[objectType]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('fpQueueAgent');
    }
}
