<?php

use yii\db\Schema;
use yii\db\Migration;

class m160318_040130_add_online_devices_status_to_edu_passport_users extends Migration
{
    public function up()
    {
        $this->addColumn('edu_passport_users', 'iphone_status', 'tinyint(1) DEFAULT 0 COMMENT \'在线状态 0不在线,1在线,2忙碌\'');
        $this->addColumn('edu_passport_users', 'ipad_status', 'tinyint(1) DEFAULT 0 COMMENT \'在线状态 0不在线,1在线,2忙碌\' ');
        $this->addColumn('edu_passport_users', 'android_phone_status', 'tinyint(1) DEFAULT 0 COMMENT \'在线状态 0不在线,1在线,2忙碌\' ');
        $this->addColumn('edu_passport_users', 'android_pad_status', 'tinyint(1) DEFAULT 0 COMMENT \'在线状态 0不在线,1在线,2忙碌\' ');
        $this->addColumn('edu_passport_users', 'pc_status', 'tinyint(1) DEFAULT 0 COMMENT \'在线状态 0不在线,1在线,2忙碌\' ');
    }

    public function down()
    {
        echo "m160318_040130_add_online_devices_status_to_edu_passport_users cannot be reverted.\n";

        $this->dropColumn('edu_passport_users', 'iphone_status');
        $this->dropColumn('edu_passport_users', 'ipad_status');
        $this->dropColumn('edu_passport_users', 'android_phone_status');
        $this->dropColumn('edu_passport_users', 'android_pad_status');
        $this->dropColumn('edu_passport_users', 'pc_status');

        return true;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
