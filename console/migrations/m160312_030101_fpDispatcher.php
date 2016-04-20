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

class m160312_030101_fpDispatcher extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('fpDispatcher', [
            'queueID' => $this->bigInteger(20)->notNull(),
            'name' => $this->string(50),
            'description' => $this->string(255),
            'offerTimeout' => $this->integer(11)->notNull(),
            'requestTimeout' => $this->integer(11)->notNull(),
            'PRIMARY KEY ([[queueID]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('fpDispatcher');
    }
}
