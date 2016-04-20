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

class m160312_030101_edu_passport_users_login_history extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%passport_users_login_history}}', [
            'user_id' => $this->string(20)->notNull(),
            'login_at' => $this->timestamp()->notNull(),
            'ip' => $this->string(20),
            'ua' => $this->string(200),
            'PRIMARY KEY ([[user_id]], [[login_at]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%passport_users_login_history}}');
    }
}
