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

class m160312_030101_migration extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('migration', [
            'version' => $this->string(180)->notNull(),
            'apply_time' => $this->integer(11),
            'PRIMARY KEY ([[version]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('migration');
    }
}
