<?php

use yii\db\Schema;
use yii\db\Migration;

class m160115_011635_edu_micro_course_view_count extends Migration
{
    public function up()
    {
        $this->createTable('{{edu_micro_course_view_count}}', [
            'id'         => Schema::TYPE_PK ." NOT NULL COMMENT 'ID'",
            'micro_id'    => Schema::TYPE_INTEGER ." NOT NULL DEFAULT '0' COMMENT '微课ID'",
            'user_id'  => Schema::TYPE_STRING . "(20) DEFAULT NULL COMMENT '用户ID'",
            'view_counts'  => Schema::TYPE_INTEGER . " DEFAULT '0' COMMENT '每天浏览量'",
            'ip'  => Schema::TYPE_STRING . "(20) DEFAULT NULL COMMENT 'IP'",
            'created_time'  => Schema::TYPE_DATETIME . " DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'",
            'updated_time'  => Schema::TYPE_DATETIME . " COMMENT '更新时间'",
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%edu_micro_course_view_count}}');
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
