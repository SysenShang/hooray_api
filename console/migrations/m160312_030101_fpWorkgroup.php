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

class m160312_030101_fpWorkgroup extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('fpWorkgroup', [
            'workgroupID' => $this->bigInteger(20)->notNull(),
            'jid' => $this->string(255)->notNull(),
            'displayName' => $this->string(50),
            'description' => $this->string(255),
            'status' => $this->integer(11)->notNull(),
            'modes' => $this->integer(11)->notNull(),
            'creationDate' => $this->string(15)->notNull(),
            'modificationDate' => $this->string(15)->notNull(),
            'maxchats' => $this->integer(11)->notNull(),
            'minchats' => $this->integer(11)->notNull(),
            'requestTimeout' => $this->integer(11)->notNull(),
            'offerTimeout' => $this->integer(11)->notNull(),
            'schedule' => $this->text(),
            'PRIMARY KEY ([[workgroupID]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('fpWorkgroup');
    }
}
