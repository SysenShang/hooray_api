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

class m160312_030101_edu_credit_rule extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%credit_rule}}', [
            'rid' => $this->primaryKey(),
            'rulename' => $this->string(30)->notNull()->defaultValue(''),
            'action' => $this->string(30)->notNull()->defaultValue(''),
            'cycletype' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'cycletime' => $this->integer(10)->notNull()->defaultValue(0),
            'rewardnum' => $this->smallInteger(2)->notNull()->defaultValue(0),
            'coin' => $this->integer(10)->notNull()->defaultValue(0),
            'credits' => $this->integer(10)->notNull()->defaultValue(0),
            'group_id' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'rates' => $this->float()->notNull()->defaultValue(1),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%credit_rule}}');
    }
}
