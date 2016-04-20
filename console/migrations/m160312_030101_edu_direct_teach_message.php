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

class m160312_030101_edu_direct_teach_message extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%direct_teach_message}}', [
            'id' => $this->primaryKey(),
            'reserve_id' => $this->integer(11)->notNull(),
            'user_id' => $this->string(18)->notNull()->defaultValue(''),
            'username' => $this->string(40),
            'content' => $this->string(200),
            'read_status' => $this->smallInteger(4)->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%direct_teach_message}}');
    }
}
