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

class m160312_030101_edu_teacher_exam_verify extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%teacher_exam_verify}}', [
            'id' => $this->primaryKey(),
            'examyuan_id' => $this->integer(11)->notNull()->defaultValue(0),
            'user_id' => $this->string(18)->notNull()->defaultValue(''),
            'stage_gid' => $this->integer(6)->notNull()->defaultValue(0),
            'subject_gid' => $this->integer(6)->notNull()->defaultValue(0),
            'stage_name' => $this->string(45)->notNull()->defaultValue(''),
            'subject_name' => $this->string(45)->notNull()->defaultValue(''),
            'datetime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'stage_fgid' => $this->integer(6)->notNull()->defaultValue(1),
            'isdefault' => $this->smallInteger(1)->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%teacher_exam_verify}}');
    }
}
