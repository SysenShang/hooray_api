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

class m160312_030101_edu_coin_product extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%coin_product}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->string(50)->notNull()->defaultValue('0'),
            'price' => $this->integer(11)->defaultValue(0),
            'nums' => $this->integer(11)->defaultValue(0),
            'remark' => $this->string(200)->defaultValue(''),
            'status' => $this->smallInteger(2)->defaultValue(1),
            'createtime' => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%coin_product}}');
    }
}
