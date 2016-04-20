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

class m160312_030101_oauth_public_keys extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('oauth_public_keys', [
            'client_id' => $this->string(255)->notNull(),
            'public_key' => $this->string(2000),
            'private_key' => $this->string(2000),
            'encryption_algorithm' => $this->string(100)->defaultValue('RS256'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('oauth_public_keys');
    }
}
