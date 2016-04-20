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

class m160312_030101_edu_admin_user extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%admin_user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(128)->notNull(),
            'password' => $this->string(128)->notNull(),
            'email' => $this->string(128)->notNull(),
            'profile' => $this->text(),
            'status' => $this->smallInteger(3)->notNull(),
            'password_hash' => $this->string(200)->notNull(),
            'password_reset_token' => $this->string(50)->notNull(),
            'auth_key' => $this->string(50)->notNull(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%admin_user}}');
    }
}
