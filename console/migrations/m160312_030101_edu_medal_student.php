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

class m160312_030101_edu_medal_student extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%medal_student}}', [
            'uid' => $this->string(18)->notNull(),
            'medalid' => $this->smallInteger(6)->notNull(),
            'type' => $this->smallInteger(3)->notNull(),
            'PRIMARY KEY ([[uid]], [[medalid]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%medal_student}}');
    }
}
