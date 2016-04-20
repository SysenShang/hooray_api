<?php

use yii\db\Migration;

class m160407_080603_extend_edu_passport_teacher_info_for_tags_and_teaching_age extends Migration
{
    public function up()
    {
        $this->addColumn('{{%edu_passport_teacher_info}}', 'tags', "varchar(255) NOT NULL DEFAULT '' COMMENT '老师标签信息'");
        $this->addColumn('{{%edu_passport_teacher_info}}', 'teaching_age', "smallint(2) NOT NULL DEFAULT 0 COMMENT '老师教龄'");
    }

    public function down()
    {
        $this->dropColumn('{{%edu_passport_teacher_info}}', 'tags');
        $this->dropColumn('{{%edu_passport_teacher_info}}', 'teaching_age');
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
