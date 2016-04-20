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

class m160312_030101_edu_recharge_validateinfo extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%recharge_validateinfo}}', [
            'id' => $this->primaryKey(),
            'hash' => $this->string(64)->defaultValue('0'),
            'code' => $this->text(),
            'delflg' => $this->smallInteger(1)->defaultValue(0),
            'transaction_id' => $this->string(64)->defaultValue('0'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%recharge_validateinfo}}');
    }
}
