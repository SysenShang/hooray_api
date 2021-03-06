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

class m160312_030101_edu_micro_course_order extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%micro_course_order}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->string(60)->notNull(),
            'user_id' => $this->string(18)->notNull()->defaultValue(''),
            'username' => $this->string(15)->notNull()->defaultValue(''),
            'mc_id' => $this->integer(11)->notNull()->defaultValue(0),
            'mc_name' => $this->string(100)->notNull()->defaultValue(''),
            't_user_id' => $this->string(18)->defaultValue(''),
            'stage_id' => $this->integer(11)->defaultValue(0),
            'grade_id' => $this->integer(8)->defaultValue(0),
            'grade_name' => $this->string(20)->defaultValue(''),
            'course_id' => $this->integer(8)->defaultValue(0),
            'course_name' => $this->string(20)->defaultValue(''),
            'price' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'video_small_image' => $this->string(255)->notNull()->defaultValue(''),
            'video_middle_image' => $this->string(255)->notNull()->defaultValue(''),
            'video_big_image' => $this->string(255)->notNull()->defaultValue(''),
            'view_nums' => $this->integer(11)->defaultValue(0),
            'valid_time' => $this->integer(11)->notNull()->defaultValue(0),
            'isshow' => $this->smallInteger(1)->defaultValue(1),
            'isdel' => $this->smallInteger(1)->defaultValue(0),
            'createtime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'updatetime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%micro_course_order}}');
    }
}
