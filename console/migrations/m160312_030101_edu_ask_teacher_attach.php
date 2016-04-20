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

class m160312_030101_edu_ask_teacher_attach extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%ask_teacher_attach}}', [
            'id' => $this->primaryKey(),
            'file_name' => $this->string(255)->defaultValue(''),
            'add_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'file_location' => $this->string(255)->defaultValue(''),
            'question_id' => $this->integer(11)->defaultValue(0),
            'order_id' => $this->string(60)->defaultValue(''),
            'published_uid' => $this->string(18)->defaultValue(''),
            'thumbnail' => $this->string(255)->defaultValue(''),
            'file_size' => $this->string(40)->notNull()->defaultValue('0'),
            'first' => $this->smallInteger(1)->defaultValue(0),
            'file_type' => $this->smallInteger(1)->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%ask_teacher_attach}}');
    }
}
