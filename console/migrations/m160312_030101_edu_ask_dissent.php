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

class m160312_030101_edu_ask_dissent extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%ask_dissent}}', [
            'id' => $this->primaryKey(),
            'reason' => $this->text(),
            'add_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'order_id' => $this->integer(11)->notNull()->defaultValue(0),
            'solve_id' => $this->smallInteger(1)->notNull()->defaultValue(3),
            'solve_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'question_id' => $this->integer(8)->notNull()->defaultValue(0),
            'results' => $this->text(),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'refund_status' => $this->smallInteger(1)->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%ask_dissent}}');
    }
}
