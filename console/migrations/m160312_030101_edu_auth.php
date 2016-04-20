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

class m160312_030101_edu_auth extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%auth}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'source' => $this->string(255)->notNull(),
            'source_id' => $this->string(255)->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%auth}}');
    }
}
