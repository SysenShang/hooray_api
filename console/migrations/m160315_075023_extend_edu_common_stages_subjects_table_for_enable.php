<?php

use yii\db\Schema;
use yii\db\Migration;

class m160315_075023_extend_edu_common_stages_subjects_table_for_enable extends Migration
{
    public function up()
    {
        $this->addColumn('{{%edu_common_stages_subjects}}','enable', "tinyint(1) NOT NULL DEFAULT 1 COMMENT '阶段科目是否有效 1 有效, 0 无效'");
    }

    public function down()
    {
        $this->dropColumn('{{%edu_common_stages_subjects}}','enable');
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
