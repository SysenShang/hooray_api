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

class m160312_030101_edu_credit_log extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%credit_log}}', [
            'logid' => $this->primaryKey(),
            'uid' => $this->string(18)->notNull()->defaultValue('0'),
            'operation' => $this->string(35)->notNull()->defaultValue(''),
            'relatedid' => $this->integer(10)->notNull(),
            'dateline' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'credits' => $this->integer(10)->notNull()->defaultValue(0),
            'coin' => $this->integer(10)->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%credit_log}}');
    }
}
