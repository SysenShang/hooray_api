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

class m160312_030101_oauth_users extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('oauth_users', [
            'username' => $this->string(255)->notNull(),
            'password' => $this->string(2000),
            'first_name' => $this->string(255),
            'last_name' => $this->string(255),
            'PRIMARY KEY ([[username]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('oauth_users');
    }
}
