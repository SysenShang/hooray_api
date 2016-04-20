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

class m160312_030101_edu_admin_users extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%admin_users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull(),
            'password_hash' => $this->string(60)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'confirmed_at' => $this->integer(11),
            'unconfirmed_email' => $this->string(255),
            'blocked_at' => $this->integer(11),
            'registration_ip' => $this->string(45),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
            'flags' => $this->integer(11)->notNull()->defaultValue(0),
            'status' => $this->smallInteger(4),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%admin_users}}');
    }
}
