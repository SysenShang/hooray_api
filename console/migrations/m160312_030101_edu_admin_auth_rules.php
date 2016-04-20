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

class m160312_030101_edu_admin_auth_rules extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%admin_auth_rules}}', [
            'name' => $this->string(64)->notNull(),
            'data' => $this->text(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'PRIMARY KEY ([[name]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%admin_auth_rules}}');
    }
}
