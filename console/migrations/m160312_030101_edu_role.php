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

class m160312_030101_edu_role extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%role}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->defaultValue(''),
            'creditshigher' => $this->integer(10)->notNull()->defaultValue(0),
            'creditslower' => $this->integer(10)->notNull()->defaultValue(0),
            'privilege' => $this->text()->notNull(),
            'type' => $this->string()->notNull()->defaultValue('normal'),
            'rank' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'icon' => $this->string(100)->notNull()->defaultValue(''),
            'allow_sendpm_to' => $this->text(),
            'allow_sendpm_from' => $this->text(),
            'allow_topic_forward_to' => $this->text(),
            'allow_topic_forward_from' => $this->text(),
            'allow_topic_reply_to' => $this->text(),
            'allow_topic_reply_from' => $this->text(),
            'allow_topic_at_to' => $this->text(),
            'allow_topic_at_from' => $this->text(),
            'allow_follow_to' => $this->text(),
            'allow_follow_from' => $this->text(),
            'system' => $this->smallInteger(1)->defaultValue(1),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%role}}');
    }
}
