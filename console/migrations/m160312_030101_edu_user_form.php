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

class m160312_030101_edu_user_form extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%user_form}}', [
            'id' => $this->primaryKey(),
            'FormUserId' => $this->string(40),
            'Name' => $this->string(40),
            'SchoolName' => $this->string(40),
            'GradeName' => $this->string(40),
            'UserType' => $this->integer(11),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%user_form}}');
    }
}
