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

class m160312_030101_edu_common_messages extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%common_messages}}', [
            'message_id' => $this->primaryKey(),
            'subject' => $this->string(80)->notNull()->defaultValue(''),
            'message' => $this->text(),
            'send_uid' => $this->string(18)->notNull()->defaultValue('0'),
            'recv_uid' => $this->string(18)->notNull()->defaultValue('0'),
            'mtype' => $this->smallInteger(1)->notNull()->defaultValue(-1),
            'isread' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'addtime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'updatetime' => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%common_messages}}');
    }
}
