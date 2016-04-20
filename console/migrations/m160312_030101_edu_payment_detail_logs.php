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

class m160312_030101_edu_payment_detail_logs extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%payment_detail_logs}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->string(64),
            'trade_no' => $this->string(64),
            'gateway' => $this->string(30),
            'content' => $this->text()->notNull(),
            'created_at' => $this->timestamp(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%payment_detail_logs}}');
    }
}
