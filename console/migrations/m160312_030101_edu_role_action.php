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

class m160312_030101_edu_role_action extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%role_action}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->defaultValue(''),
            'module' => $this->string(50)->notNull()->defaultValue('index'),
            'action' => $this->string(255)->notNull()->defaultValue(''),
            'describe' => $this->string(255)->notNull()->defaultValue(''),
            'message' => $this->string(255)->notNull()->defaultValue(''),
            'allow_all' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'credit_require' => $this->string(255)->notNull()->defaultValue(''),
            'credit_update' => $this->string(255)->notNull()->defaultValue(''),
            'log' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'is_admin' => $this->smallInteger(1)->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%role_action}}');
    }
}
