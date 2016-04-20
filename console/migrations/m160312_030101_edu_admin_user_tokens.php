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

class m160312_030101_edu_admin_user_tokens extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%admin_user_tokens}}', [
            'user_id' => $this->integer(11)->notNull(),
            'code' => $this->string(32)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'type' => $this->smallInteger(6)->notNull(),
            'PRIMARY KEY ([[user_id]], [[code]], [[type]])',
            //'FOREIGN KEY ([[user_id]]) REFERENCES {{%admin_users}} ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%admin_user_tokens}}');
    }
}
