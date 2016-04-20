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

class m160312_030101_edu_cards extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%cards}}', [
            'id' => $this->primaryKey(),
            'prefix' => $this->string(3),
            'key' => $this->string(15)->notNull()->defaultValue(''),
            'crypt' => $this->string(100)->notNull()->defaultValue(''),
            'price' => $this->integer(9)->notNull()->defaultValue(0),
            'user_id' => $this->string(18)->notNull()->defaultValue('0'),
            'username' => $this->string(64),
            'order_id' => $this->string(64),
            'status' => $this->smallInteger(4)->notNull()->defaultValue(0),
            'used_at' => $this->timestamp()->defaultValue('0000-00-00 00:00:00'),
            'created_at' => $this->timestamp()->notNull(),
            'expired_at' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%cards}}');
    }
}
