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

class m160312_030101_edu_feedback extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%feedback}}', [
            'id' => $this->primaryKey(),
            'content' => $this->text(),
            'user_id' => $this->string(18)->notNull()->defaultValue(''),
            'username' => $this->string(15)->notNull()->defaultValue(''),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'add_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'type_flag' => $this->string(255)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%feedback}}');
    }
}
