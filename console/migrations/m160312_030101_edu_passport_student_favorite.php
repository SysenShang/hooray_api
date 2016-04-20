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

class m160312_030101_edu_passport_student_favorite extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%passport_student_favorite}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->string(18)->notNull()->defaultValue('0'),
            'rc_id' => $this->integer(11)->notNull()->defaultValue(0),
            'type' => $this->smallInteger(1)->defaultValue(0),
            'course_logo' => $this->string(255)->notNull()->defaultValue(''),
            'title' => $this->string(100)->notNull()->defaultValue(''),
            'education_id' => $this->integer(11),
            'education_name' => $this->string(40),
            'grade_id' => $this->integer(11),
            'grade_name' => $this->string(40)->defaultValue(''),
            'subject_id' => $this->integer(11),
            'subject_name' => $this->string(40)->notNull()->defaultValue(''),
            'original_price' => $this->integer(8)->notNull()->defaultValue(0),
            'total_price' => $this->integer(8)->notNull()->defaultValue(0),
            't_user_id' => $this->string(18)->defaultValue('0'),
            'realname' => $this->string(20),
            'xstatus' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'createtime' => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%passport_student_favorite}}');
    }
}
