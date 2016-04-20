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

class m160312_030101_edu_system_educoin_log extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%system_educoin_log}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->string(18)->notNull()->defaultValue('0'),
            'order_id' => $this->string(64)->defaultValue(''),
            'order_type' => $this->smallInteger(2)->defaultValue(0),
            'nums' => $this->integer(11)->notNull()->defaultValue(0),
            'remark' => $this->string(200)->notNull()->defaultValue(''),
            'createtime' => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%system_educoin_log}}');
    }
}
