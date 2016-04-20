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

class m160312_030101_edu_time_sms extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%time_sms}}', [
            'id' => $this->primaryKey(),
            'mobile' => $this->string(30)->notNull()->defaultValue(''),
            'msg' => $this->string(200)->notNull()->defaultValue(''),
            'at_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%time_sms}}');
    }
}
