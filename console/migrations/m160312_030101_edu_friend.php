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

class m160312_030101_edu_friend extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%friend}}', [
            'id' => $this->primaryKey(),
            'fromId' => $this->string(18)->notNull()->defaultValue(''),
            'toId' => $this->string(18)->notNull()->defaultValue(''),
            'createdTime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'title' => $this->string(200),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%friend}}');
    }
}
