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

class m160312_030101_edu_course_order_detail extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%course_order_detail}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->string(60)->notNull(),
            'user_id' => $this->string(18)->notNull()->defaultValue('0'),
            'course_id' => $this->integer(8)->notNull()->defaultValue(0),
            'course_name' => $this->string(120)->notNull()->defaultValue(''),
            'section_id' => $this->integer(8)->notNull()->defaultValue(0),
            'section_name' => $this->string(120)->notNull()->defaultValue(''),
            'price' => $this->integer(11)->notNull()->defaultValue(0),
            'd_price' => $this->integer(11)->defaultValue(0),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'isdel' => $this->smallInteger(1)->defaultValue(0),
            'createtime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'updatetime' => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%course_order_detail}}');
    }
}
