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

class m160312_030101_edu_passport_users_weixin extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%passport_users_weixin}}', [
            'id' => $this->primaryKey(),
            'uid' => $this->string(18)->notNull(),
            'openid' => $this->string(255)->notNull(),
            'expires_in' => $this->integer(10),
            'access_token' => $this->string(255),
            'refresh_token' => $this->string(255),
            'scope' => $this->string(64),
            'headimgurl' => $this->string(255),
            'nickname' => $this->string(64),
            'sex' => $this->string()->defaultValue('男'),
            'province' => $this->string(32),
            'city' => $this->string(32),
            'country' => $this->string(32),
            'add_time' => $this->integer(10)->notNull(),
            'latitude' => $this->float(),
            'longitude' => $this->float(),
            'location_update' => $this->integer(10)->defaultValue(0),
            'group_id' => $this->smallInteger(1)->defaultValue(1),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%passport_users_weixin}}');
    }
}
