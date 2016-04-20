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

class m160312_030101_edu_pan_files_trash extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%pan_files_trash}}', [
            'auto_id' => $this->primaryKey(),
            'id' => $this->string(250)->notNull()->defaultValue(''),
            'user' => $this->string(64)->notNull()->defaultValue(''),
            'timestamp' => $this->string(12)->notNull()->defaultValue(''),
            'location' => $this->string(512)->notNull()->defaultValue(''),
            'type' => $this->string(4),
            'mime' => $this->string(255),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%pan_files_trash}}');
    }
}
