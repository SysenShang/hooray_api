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

class m160312_030101_edu_passport_users_qq extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%passport_users_qq}}', [
            'id' => $this->bigPrimaryKey(),
            'uid' => $this->string(18)->notNull(),
            'nickname' => $this->string(64),
            'openid' => $this->string(128)->defaultValue(''),
            'gender' => $this->string(8),
            'add_time' => $this->timestamp(),
            'access_token' => $this->string(64),
            'refresh_token' => $this->string(64),
            'expires_time' => $this->integer(10),
            'figureurl' => $this->string(255),
            'group_id' => $this->smallInteger(1)->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%passport_users_qq}}');
    }
}
