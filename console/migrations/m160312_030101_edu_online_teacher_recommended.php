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

class m160312_030101_edu_online_teacher_recommended extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%online_teacher_recommended}}', [
            'teacher_id' => $this->string(18)->notNull(),
            'teacher_name' => $this->string(40)->notNull(),
            'education_id' => $this->string(255)->notNull(),
            'subject_id' => $this->string(255),
            'rating' => $this->smallInteger(2),
            'update_time' => $this->timestamp()->notNull(),
            'create_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'PRIMARY KEY ([[teacher_id]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%online_teacher_recommended}}');
    }
}
