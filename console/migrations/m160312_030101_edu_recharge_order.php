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

class m160312_030101_edu_recharge_order extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%recharge_order}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->string(64)->defaultValue('0'),
            'trade_no' => $this->string(64)->defaultValue(''),
            'title' => $this->string(200),
            'user_id' => $this->string(18)->defaultValue('0'),
            'buyer_id' => $this->string(30),
            'buyer_email' => $this->string(100),
            'total_price' => $this->float(8,2)->defaultValue(0),
            'coin' => $this->integer(11)->defaultValue(0),
            'status' => $this->smallInteger(2)->defaultValue(0),
            'createtime' => $this->timestamp()->defaultValue('0000-00-00 00:00:00'),
            'updatetime' => $this->timestamp(),
            'return_url' => $this->text(),
            'gateway' => $this->string(30),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%recharge_order}}');
    }
}
