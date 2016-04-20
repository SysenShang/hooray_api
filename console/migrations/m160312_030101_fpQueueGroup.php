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

class m160312_030101_fpQueueGroup extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('fpQueueGroup', [
            'queueID' => $this->bigInteger(20)->notNull(),
            'groupName' => $this->string(50)->notNull(),
            'PRIMARY KEY ([[queueID]], [[groupName]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('fpQueueGroup');
    }
}
