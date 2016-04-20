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

class m160312_030101_edu_passport_student_field extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%passport_student_field}}', [
            'user_id' => $this->integer(8)->notNull(),
            'publishfeed' => $this->smallInteger(3)->notNull()->defaultValue(0),
            'customshow' => $this->smallInteger(1)->notNull()->defaultValue(26),
            'customstatus' => $this->string(30)->notNull()->defaultValue(''),
            'medals' => $this->text()->notNull(),
            'sightml' => $this->text()->notNull(),
            'groupterms' => $this->text()->notNull(),
            'authstr' => $this->string(20)->notNull()->defaultValue(''),
            'groups' => $this->text()->notNull(),
            'attentiongroup' => $this->string(255)->notNull()->defaultValue(''),
            'PRIMARY KEY ([[user_id]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%passport_student_field}}');
    }
}
