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

class m160312_030101_fpAgent extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('fpAgent', [
            'agentID' => $this->bigInteger(20)->notNull(),
            'agentJID' => $this->string(255)->notNull(),
            'name' => $this->string(255),
            'maxchats' => $this->integer(11)->notNull(),
            'minchats' => $this->integer(11)->notNull(),
            'PRIMARY KEY ([[agentJID]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('fpAgent');
    }
}
