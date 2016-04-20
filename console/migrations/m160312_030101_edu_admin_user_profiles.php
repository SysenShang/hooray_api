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

class m160312_030101_edu_admin_user_profiles extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%admin_user_profiles}}', [
            'user_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255),
            'public_email' => $this->string(255),
            'gravatar_email' => $this->string(255),
            'gravatar_id' => $this->string(32),
            'location' => $this->string(255),
            'website' => $this->string(255),
            'bio' => $this->text(),
            'PRIMARY KEY ([[user_id]])',
            //'FOREIGN KEY ([[user_id]]) REFERENCES {{%admin_users}} ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%admin_user_profiles}}');
    }
}
