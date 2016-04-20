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

class m160312_030101_edu_exam_teacher_type_record extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%exam_teacher_type_record}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->string(18)->notNull(),
            'stage_id' => $this->integer(8)->defaultValue(0),
            'stage_name' => $this->string(60),
            'subject_id' => $this->integer(8)->defaultValue(0),
            'subject_name' => $this->string(45)->notNull()->defaultValue(''),
            'type_id' => $this->integer(8)->defaultValue(0),
            'type_name' => $this->string(60),
            'updatetime' => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%exam_teacher_type_record}}');
    }
}
