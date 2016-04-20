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

class m160312_030101_edu_advertisements extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%advertisements}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(100),
            'block' => $this->string(50),
            'image_size' => $this->string(10)->notNull(),
            'image_url' => $this->string(100)->notNull(),
            'is_delete' => $this->smallInteger(1),
            'is_click' => $this->smallInteger(1),
            'position' => $this->smallInteger(5),
            'clicks' => $this->integer(11),
            'target_url' => $this->string(100),
            'start' => $this->datetime(),
            'expire' => $this->datetime(),
            'device' => $this->string(20),
            'is_new_window' => $this->smallInteger(1),
            'height' => $this->integer(11),
            'width' => $this->integer(11),
            'created_at' => $this->datetime(),
            'updated_at' => $this->datetime(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%advertisements}}');
    }
}
