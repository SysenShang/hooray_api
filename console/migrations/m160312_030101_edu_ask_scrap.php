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

class m160312_030101_edu_ask_scrap extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%ask_scrap}}', [
            'scrap_id' => $this->primaryKey(),
            'order_id' => $this->integer(11)->notNull()->defaultValue(0),
            'first' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'add_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'question_id' => $this->integer(11)->notNull()->defaultValue(0),
            'answer_uid' => $this->string(18)->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%ask_scrap}}');
    }
}
