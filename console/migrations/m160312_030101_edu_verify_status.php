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

class m160312_030101_edu_verify_status extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%verify_status}}', [
            'id' => $this->primaryKey(),
            'device' => $this->string(20),
            'version' => $this->string(10),
            'type' => $this->string(10),
            'verify' => $this->smallInteger(4),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%verify_status}}');
    }
}
