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

class m160312_030101_edu_medal extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%medal}}', [
            'medalid' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->defaultValue(''),
            'available' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'image' => $this->string(255)->notNull()->defaultValue(''),
            'big_image' => $this->string(255)->notNull()->defaultValue(''),
            'type' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'displayorder' => $this->smallInteger(3)->notNull()->defaultValue(0),
            'description' => $this->string(255)->notNull()->defaultValue(''),
            'expiration' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'permission' => $this->text()->notNull(),
            'credit' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'price' => $this->integer(8)->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%medal}}');
    }
}
