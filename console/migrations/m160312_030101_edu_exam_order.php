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

class m160312_030101_edu_exam_order extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%exam_order}}', [
            'id' => $this->bigPrimaryKey(),
            'order_id' => $this->string(60),
            'exam_id' => $this->integer(9)->notNull()->defaultValue(0),
            'exam_name' => $this->string(60)->notNull()->defaultValue('0'),
            'record_id' => $this->string(18)->defaultValue('0'),
            'exam_time' => $this->timestamp()->defaultValue('0000-00-00 00:00:00'),
            'teacher_id' => $this->string(18)->notNull()->defaultValue(''),
            'teacher_name' => $this->string(30)->notNull()->defaultValue(''),
            'subject_gname' => $this->string(10)->notNull()->defaultValue(''),
            'buyer_id' => $this->string(18)->notNull()->defaultValue('0'),
            'buyer_name' => $this->string(30)->defaultValue(''),
            'price' => $this->integer(11)->notNull(),
            'total_fen' => $this->string(10)->notNull()->defaultValue(''),
            'top' => $this->integer(8)->notNull()->defaultValue(0),
            'xstatus' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'createtime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'updatetime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'exam_start_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'exam_end_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%exam_order}}');
    }
}
