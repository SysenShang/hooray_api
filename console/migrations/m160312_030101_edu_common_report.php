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

class m160312_030101_edu_common_report extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%common_report}}', [
            'id' => $this->primaryKey(),
            'report_id' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'message' => $this->text()->notNull(),
            'uid' => $this->string(18)->notNull()->defaultValue('0'),
            'username' => $this->string(15)->notNull()->defaultValue(''),
            'dateline' => $this->timestamp(),
            'num' => $this->smallInteger(6)->notNull()->defaultValue(1),
            'opuid' => $this->string(18)->notNull()->defaultValue('0'),
            'opname' => $this->string(15)->notNull()->defaultValue(''),
            'optime' => $this->timestamp(),
            'opresult' => $this->string(255)->notNull()->defaultValue(''),
            'xtype' => $this->smallInteger(1)->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%common_report}}');
    }
}
