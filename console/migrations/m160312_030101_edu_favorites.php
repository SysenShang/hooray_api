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

class m160312_030101_edu_favorites extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%favorites}}', [
            'id' => $this->primaryKey(),
            'resource_id' => $this->integer(6)->notNull()->defaultValue(0),
            'user_id' => $this->string(18)->notNull()->defaultValue('0'),
            'type' => $this->integer(11)->notNull()->defaultValue(0),
            'status' => $this->integer(11)->notNull()->defaultValue(0),
            'createtime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'resource_type' => $this->string(20)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%favorites}}');
    }
}
