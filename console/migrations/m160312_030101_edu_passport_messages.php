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

class m160312_030101_edu_passport_messages extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%passport_messages}}', [
            'id' => $this->primaryKey(),
            'sender' => $this->string(18)->defaultValue('0'),
            'type' => $this->integer(8)->defaultValue(0),
            'cat_id' => $this->integer(8),
            'user_id' => $this->string(18),
            'message' => $this->text(),
            'isdel' => $this->smallInteger(2)->defaultValue(0),
            'status' => $this->smallInteger(2)->defaultValue(0),
            'reg_date' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'upd_date' => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%passport_messages}}');
    }
}
