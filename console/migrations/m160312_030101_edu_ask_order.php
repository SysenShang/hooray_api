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

class m160312_030101_edu_ask_order extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%ask_order}}', [
            'order_id' => $this->primaryKey(),
            'order_sn' => $this->string(60)->defaultValue(''),
            'question_id' => $this->integer(11)->notNull()->defaultValue(0),
            'answer_uid' => $this->string(18)->notNull()->defaultValue(''),
            'answer_nickname' => $this->string(50)->notNull()->defaultValue(''),
            'answer_username' => $this->string(50)->defaultValue(''),
            'order_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'confrim' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'answer_add_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'replies' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'order_status' => $this->smallInteger(1)->notNull()->defaultValue(1),
            't_is_comment' => $this->smallInteger(1)->notNull()->defaultValue(0),
            's_is_comment' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'acquire_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            't_status' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'dissent_id' => $this->integer(11)->notNull()->defaultValue(0),
            'solve_id' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'refund_status' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'sh_id' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'first' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'is_video' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'is_append' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'appoint_type_id' => $this->smallInteger(1)->notNull()->defaultValue(0),
            's_is_del' => $this->smallInteger(1)->defaultValue(0),
            't_is_del' => $this->smallInteger(1)->defaultValue(0),
            'classroom_id' => $this->string(30),
            'answer_begin_time' => $this->datetime()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%ask_order}}');
    }
}
