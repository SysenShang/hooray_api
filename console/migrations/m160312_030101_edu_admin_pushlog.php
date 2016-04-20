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

class m160312_030101_edu_admin_pushlog extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%admin_pushlog}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->defaultValue(''),
            'msg' => $this->text()->notNull(),
            'admin_username' => $this->string(150)->notNull()->defaultValue('0'),
            'status' => $this->smallInteger(1)->defaultValue(0),
            'deflg' => $this->smallInteger(1)->defaultValue(0),
            'createtime' => $this->timestamp()->notNull()->defaultValue('0000-00-00'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%admin_pushlog}}');
    }
}
