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

class m160312_030101_edu_student_version extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%student_version}}', [
            'vid' => $this->primaryKey(),
            'version_name' => $this->string(50)->defaultValue(''),
            'version_number' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'version_url' => $this->string(100)->notNull()->defaultValue(''),
            'filemd5' => $this->string(32)->notNull()->defaultValue(''),
            'explain' => $this->text(),
            'create_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'update_time' => $this->timestamp()->notNull(),
            'xstatus' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'enforce' => $this->smallInteger(1)->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%student_version}}');
    }
}
