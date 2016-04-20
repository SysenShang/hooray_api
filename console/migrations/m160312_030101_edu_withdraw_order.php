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

class m160312_030101_edu_withdraw_order extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%withdraw_order}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->string(64)->defaultValue('0'),
            'user_id' => $this->string(18)->defaultValue('0'),
            'username' => $this->string(50),
            'bank_name' => $this->string(50),
            'cardno' => $this->string(40),
            'total_price' => $this->float(8,2)->defaultValue(0),
            'coin' => $this->integer(11)->defaultValue(0),
            'status' => $this->smallInteger(2)->defaultValue(0),
            'createtime' => $this->timestamp()->defaultValue('0000-00-00 00:00:00'),
            'updatetime' => $this->timestamp(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%withdraw_order}}');
    }
}
