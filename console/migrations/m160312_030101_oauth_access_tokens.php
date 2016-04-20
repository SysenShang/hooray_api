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

class m160312_030101_oauth_access_tokens extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('oauth_access_tokens', [
            'access_token' => $this->string(40)->notNull(),
            'client_id' => $this->string(32)->notNull(),
            'user_id' => $this->string(18),
            'expires' => $this->timestamp()->notNull(),
            'scope' => $this->string(2000),
            'PRIMARY KEY ([[access_token]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('oauth_access_tokens');
    }
}
