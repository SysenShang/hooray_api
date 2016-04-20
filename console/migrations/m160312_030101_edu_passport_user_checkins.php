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

class m160312_030101_edu_passport_user_checkins extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%passport_user_checkins}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->string(18)->notNull(),
            'signed_date_time' => $this->integer(11)->notNull(),
            'signed_days' => $this->smallInteger(5)->notNull(),
            'created_at' => $this->datetime(),
            'updated_at' => $this->datetime(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%passport_user_checkins}}');
    }
}
