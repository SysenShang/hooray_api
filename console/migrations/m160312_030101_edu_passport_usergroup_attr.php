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

class m160312_030101_edu_passport_usergroup_attr extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%passport_usergroup_attr}}', [
            'attr_id' => $this->primaryKey(),
            'group_id' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'attr_name' => $this->string(60)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%passport_usergroup_attr}}');
    }
}
