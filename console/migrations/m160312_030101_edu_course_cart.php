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

class m160312_030101_edu_course_cart extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%course_cart}}', [
            'cart_id' => $this->primaryKey(),
            'user_id' => $this->string(18)->notNull()->defaultValue(''),
            'course_id' => $this->integer(8)->notNull()->defaultValue(0),
            'course_name' => $this->string(120)->notNull()->defaultValue(''),
            'section_id' => $this->integer(8)->notNull()->defaultValue(0),
            'section_name' => $this->string(120)->notNull()->defaultValue(''),
            'price' => $this->integer(11)->notNull()->defaultValue(0),
            'd_price' => $this->integer(11)->defaultValue(0),
            'course_category_id' => $this->integer(8)->notNull()->defaultValue(0),
            'smallPicture' => $this->string(255)->notNull()->defaultValue(''),
            'middlePicture' => $this->string(255)->notNull()->defaultValue(''),
            'largePicture' => $this->string(255)->notNull()->defaultValue(''),
            's_user_id' => $this->integer(8)->notNull()->defaultValue(0),
            's_user_name' => $this->string(120)->notNull()->defaultValue(''),
            'createtime' => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%course_cart}}');
    }
}
