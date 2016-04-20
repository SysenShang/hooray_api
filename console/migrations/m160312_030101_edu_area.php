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

class m160312_030101_edu_area extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%area}}', [
            'id' => $this->integer(11),
            'area_name' => $this->string(60),
            'parent_id' => $this->integer(11),
            'level' => $this->string(3),
            'is_delete' => $this->string(3),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%area}}');
    }
}
