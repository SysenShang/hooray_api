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

class m160312_030101_edu_direct_teach_reserve extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%direct_teach_reserve}}', [
            'id' => $this->primaryKey(),
            'schedule_id' => $this->integer(11)->notNull(),
            'student_id' => $this->string(18)->notNull()->defaultValue(''),
            'username' => $this->string(40),
            'created_at' => $this->timestamp()->notNull(),
            'status' => $this->smallInteger(4)->notNull()->defaultValue(0),
            'note' => $this->string(200),
            'score' => $this->smallInteger(4)->notNull()->defaultValue(0),
            'comment' => $this->string(200),
            'judge_at' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%direct_teach_reserve}}');
    }
}
