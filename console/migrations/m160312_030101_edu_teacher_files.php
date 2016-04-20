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

class m160312_030101_edu_teacher_files extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%teacher_files}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->string(18)->notNull(),
            'persistentId' => $this->string(35)->notNull(),
            'inputBucket' => $this->string(35),
            'inputKey' => $this->string(60),
            'cmd' => $this->string(100),
            'hash' => $this->string(60),
            'itemsKey' => $this->string(150),
            'pipeline' => $this->string(60),
            'reqid' => $this->string(35),
            'page_num' => $this->smallInteger(5),
            'description' => $this->text(),
            'created_at' => $this->datetime(),
            'updated_at' => $this->datetime(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%teacher_files}}');
    }
}
