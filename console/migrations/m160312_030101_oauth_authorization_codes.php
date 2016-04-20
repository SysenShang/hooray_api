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

class m160312_030101_oauth_authorization_codes extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('oauth_authorization_codes', [
            'authorization_code' => $this->string(40)->notNull(),
            'client_id' => $this->string(32)->notNull(),
            'user_id' => $this->string(18),
            'redirect_uri' => $this->string(1000)->notNull(),
            'expires' => $this->timestamp()->notNull(),
            'scope' => $this->string(2000),
            'PRIMARY KEY ([[authorization_code]])',
            //'FOREIGN KEY ([[client_id]]) REFERENCES oauth_clients ([[client_id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('oauth_authorization_codes');
    }
}
