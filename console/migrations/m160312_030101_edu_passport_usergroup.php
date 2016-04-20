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

class m160312_030101_edu_passport_usergroup extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%passport_usergroup}}', [
            'group_id' => $this->primaryKey(),
            'radmin_id' => $this->smallInteger(3)->notNull()->defaultValue(0),
            'xtype' => $this->string()->notNull()->defaultValue('member'),
            'group_name' => $this->string(100)->notNull()->defaultValue(''),
            'icon' => $this->string(255)->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%passport_usergroup}}');
    }
}
