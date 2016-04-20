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

class m160312_030101_edu_common_exmastore_type extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%common_exmastore_type}}', [
            'id' => $this->primaryKey(),
            'exam_store_type' => $this->string(45)->defaultValue(''),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%common_exmastore_type}}');
    }
}
