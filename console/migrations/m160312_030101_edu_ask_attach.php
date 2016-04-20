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

class m160312_030101_edu_ask_attach extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%ask_attach}}', [
            'id' => $this->primaryKey(),
            'file_name' => $this->string(255),
            'add_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'file_location' => $this->string(255),
            'question_id' => $this->integer(11)->defaultValue(0),
            'type_id' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'published_uid' => $this->string(18)->defaultValue(''),
            'file_size' => $this->string(30)->notNull()->defaultValue('0'),
            'file_type' => $this->smallInteger(1)->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%ask_attach}}');
    }
}
