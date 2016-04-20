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

class m160312_030101_users extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'password_reset_token' => $this->string(255),
            'status' => $this->smallInteger(6)->notNull()->defaultValue(10),
            'created_at' => $this->datetime()->notNull()->defaultValue('0000-00-00'),
            'updated_at' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'access_token' => $this->string(45),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('users');
    }
}
