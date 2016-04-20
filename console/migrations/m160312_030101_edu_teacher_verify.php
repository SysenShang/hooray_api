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

class m160312_030101_edu_teacher_verify extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%teacher_verify}}', [
            'user_id' => $this->string(18)->notNull(),
            'apply_status' => $this->smallInteger(4)->notNull()->defaultValue(0),
            'verify1' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'verify2' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'verify3' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'verify4' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'verify5' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'verify6' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'verify7' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'verify8' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'verify9' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'verify10' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'is_check_in' => $this->smallInteger(1)->defaultValue(0),
            'PRIMARY KEY ([[user_id]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%teacher_verify}}');
    }
}
