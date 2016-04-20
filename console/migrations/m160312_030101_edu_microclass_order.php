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

class m160312_030101_edu_microclass_order extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%microclass_order}}', [
            'micro_order_id' => $this->primaryKey(),
            'micro_order' => $this->string(60)->notNull(),
            'micro_id' => $this->integer(11)->notNull()->defaultValue(0),
            'user_id' => $this->string(18)->notNull()->defaultValue(''),
            'username' => $this->string(15)->notNull()->defaultValue(''),
            'price' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'microclass_name' => $this->string(100)->notNull()->defaultValue(''),
            'vedio_small_image' => $this->string(255)->notNull()->defaultValue(''),
            'vedio_middle_image' => $this->string(255)->notNull()->defaultValue(''),
            'vedio_big_image' => $this->string(255)->notNull()->defaultValue(''),
            'free' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'createtime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'updatetime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%microclass_order}}');
    }
}
