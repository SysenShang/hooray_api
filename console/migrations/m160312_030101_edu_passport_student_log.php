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

class m160312_030101_edu_passport_student_log extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%passport_student_log}}', [
            'user_id' => $this->string(18)->notNull(),
            'regip' => $this->string(15)->notNull()->defaultValue(''),
            'lastlogin' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'version' => $this->string(30)->notNull()->defaultValue(''),
            'logintime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'logouttime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'PRIMARY KEY ([[user_id]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%passport_student_log}}');
    }
}
