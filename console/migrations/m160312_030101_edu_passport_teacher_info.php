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

class m160312_030101_edu_passport_teacher_info extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%passport_teacher_info}}', [
            'user_id' => $this->string(18)->notNull(),
            'realname' => $this->string(30)->notNull()->defaultValue(''),
            'nickname' => $this->string(40)->notNull()->defaultValue(''),
            'gender' => $this->string()->notNull()->defaultValue('男'),
            'birthmonth' => $this->smallInteger(3)->notNull()->defaultValue(0),
            'birthyear' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'birthday' => $this->smallInteger(3)->notNull()->defaultValue(0),
            'resideprovince' => $this->string(255)->notNull()->defaultValue(''),
            'residecity' => $this->string(255)->notNull()->defaultValue(''),
            'residedist' => $this->string(255)->notNull()->defaultValue(''),
            'residecommunity' => $this->string(255)->notNull()->defaultValue(''),
            'residesuite' => $this->string(255)->notNull()->defaultValue(''),
            'telephone' => $this->string(20)->notNull()->defaultValue(''),
            'education' => $this->string(255)->notNull()->defaultValue(''),
            'profile' => $this->text(),
            'avatar' => $this->string(255)->notNull()->defaultValue(''),
            'characteristics' => $this->text(),
            'SchoolName' => $this->string(60)->defaultValue('\'\''),
            'GradeName' => $this->string(60)->defaultValue('\'\''),
            'idcard' => $this->string(45)->notNull()->defaultValue(''),
            'PRIMARY KEY ([[user_id]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%passport_teacher_info}}');
    }
}
