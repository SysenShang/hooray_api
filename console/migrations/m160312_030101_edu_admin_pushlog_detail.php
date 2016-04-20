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

class m160312_030101_edu_admin_pushlog_detail extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%admin_pushlog_detail}}', [
            'id' => $this->primaryKey(),
            'msgid' => $this->integer(8)->notNull()->defaultValue(0),
            'user_id' => $this->string(18)->notNull()->defaultValue('0'),
            'username' => $this->string(30)->notNull()->defaultValue(''),
            'realname' => $this->string(30)->notNull()->defaultValue(''),
            'group_id' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'createtime' => $this->timestamp()->notNull()->defaultValue('0000-00-00'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%admin_pushlog_detail}}');
    }
}
