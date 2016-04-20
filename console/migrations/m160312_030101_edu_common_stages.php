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

class m160312_030101_edu_common_stages extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%common_stages}}', [
            'id' => $this->primaryKey(),
            'fid' => $this->integer(6)->notNull(),
            'stages_name' => $this->string(45)->notNull()->defaultValue(''),
            'xtype' => $this->smallInteger(1)->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%common_stages}}');
    }
}
