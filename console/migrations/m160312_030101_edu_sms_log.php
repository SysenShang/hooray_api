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

class m160312_030101_edu_sms_log extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%sms_log}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(100)->notNull()->defaultValue(''),
            'uid' => $this->string(18)->notNull()->defaultValue(''),
            'version' => $this->smallInteger(4)->notNull()->defaultValue(0),
            'datetime' => $this->timestamp()->notNull(),
            'status_code' => $this->string(45)->defaultValue(''),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%sms_log}}');
    }
}
