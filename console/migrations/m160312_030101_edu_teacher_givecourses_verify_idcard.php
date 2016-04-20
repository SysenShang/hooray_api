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

class m160312_030101_edu_teacher_givecourses_verify_idcard extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%teacher_givecourses_verify_idcard}}', [
            'user_id' => $this->string(18)->notNull(),
            'realname' => $this->string(15)->notNull()->defaultValue(''),
            'id_card' => $this->string(20)->notNull()->defaultValue(''),
            'idcard_head' => $this->string(255)->notNull()->defaultValue(''),
            'idcard_front' => $this->string(255)->notNull()->defaultValue(''),
            'idcard_back' => $this->string(255)->notNull()->defaultValue(''),
            'flag' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'datetime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'verify_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'PRIMARY KEY ([[user_id]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%teacher_givecourses_verify_idcard}}');
    }
}
