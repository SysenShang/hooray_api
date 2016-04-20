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

class m160312_030101_edu_order extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%order}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->string(60)->notNull(),
            'user_id' => $this->string(18)->notNull()->defaultValue('0'),
            'username' => $this->string(60)->notNull()->defaultValue(''),
            'type' => $this->smallInteger(2)->defaultValue(0),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'total_price' => $this->integer(11)->notNull()->defaultValue(0),
            'createtime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'updatetime' => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%order}}');
    }
}
