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

class m160312_030101_edu_direct_teach_schedule extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%direct_teach_schedule}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(15),
            'teacher_id' => $this->string(18)->notNull()->defaultValue(''),
            'username' => $this->string(40),
            'date' => $this->date()->notNull(),
            'begintime' => $this->time()->notNull(),
            'endtime' => $this->time()->notNull(),
            'price' => $this->integer(11)->notNull()->defaultValue(1),
            'limit' => $this->integer(11)->notNull()->defaultValue(1),
            'num' => $this->integer(11)->notNull()->defaultValue(0),
            'classroom_id' => $this->integer(11),
            'classroom_record' => $this->string(200),
            'chatroom_id' => $this->integer(11),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%direct_teach_schedule}}');
    }
}
