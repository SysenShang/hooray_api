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

class m160312_030101_edu_task extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%task}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->string(18)->notNull()->defaultValue('0'),
            'total_scores' => $this->integer(11)->notNull()->defaultValue(0),
            'today_scores' => $this->integer(11)->notNull()->defaultValue(0),
            'once_register' => $this->smallInteger(4)->notNull()->defaultValue(0),
            'once_complete' => $this->smallInteger(4)->notNull()->defaultValue(0),
            'everyday_checkin' => $this->smallInteger(4)->notNull()->defaultValue(0),
            'lastdate_checkin' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'everyday_weike' => $this->smallInteger(4)->notNull()->defaultValue(0),
            'lastdate_weike' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'everyday_share' => $this->smallInteger(4)->notNull()->defaultValue(0),
            'lastdate_share' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'serial_checkin' => $this->smallInteger(4)->notNull()->defaultValue(0),
            'everyday_ask' => $this->smallInteger(4)->notNull()->defaultValue(0),
            'lastdate_ask' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'serial_ask' => $this->smallInteger(4)->notNull()->defaultValue(0),
            'updated_at' => $this->timestamp()->notNull(),
            'everyday_judge' => $this->smallInteger(4)->notNull()->defaultValue(0),
            'lastdate_judge' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'everyday_buyweike' => $this->smallInteger(4)->notNull()->defaultValue(0),
            'lastdate_buyweike' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'everyday_judgeweike' => $this->smallInteger(4)->notNull()->defaultValue(0),
            'lastdate_judgeweike' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%task}}');
    }
}
