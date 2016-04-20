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

class m160312_030101_edu_online_courses_audition extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%online_courses_audition}}', [
            'audition_id' => $this->primaryKey(),
            'course_id' => $this->integer(6)->notNull()->defaultValue(0),
            'user_id' => $this->string(18)->defaultValue('0'),
            'create_time' => $this->integer(11),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%online_courses_audition}}');
    }
}
