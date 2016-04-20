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

class m160312_030101_edu_passport_users extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%passport_users}}', [
            'user_id' => $this->string(18)->notNull(),
            'user_number' => $this->string(15)->notNull()->defaultValue(''),
            'telephone' => $this->string(60)->notNull()->defaultValue(''),
            'username' => $this->string(40)->notNull()->defaultValue(''),
            'email' => $this->string(30)->notNull()->defaultValue(''),
            'upassword' => $this->string(64)->notNull()->defaultValue(''),
            'pwsafety' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'regdate' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'avatarstatus' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'group_id' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'area_id' => $this->smallInteger(5),
            'school_id' => $this->integer(11)->notNull()->defaultValue(0),
            'grade' => $this->smallInteger(4),
            'attr_id' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'xstatus' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'FormUserId' => $this->string(40),
            'GradeName' => $this->string(80),
            'user_source' => $this->smallInteger(2)->defaultValue(0),
            'xtype' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'status' => $this->smallInteger(1)->defaultValue(0),
            'type' => $this->smallInteger(1)->defaultValue(0),
            'remember_token' => $this->string(100),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
            'PRIMARY KEY ([[user_id]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%passport_users}}');
    }
}
