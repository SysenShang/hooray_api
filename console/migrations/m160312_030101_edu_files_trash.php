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

class m160312_030101_edu_files_trash extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%files_trash}}', [
            'id' => $this->primaryKey(),
            'filename' => $this->string(250)->notNull()->defaultValue(''),
            'userid' => $this->string(18)->notNull(),
            'timestamp' => $this->string(12)->notNull()->defaultValue(''),
            'location' => $this->string(512)->notNull()->defaultValue(''),
            'type' => $this->string(4)->notNull()->defaultValue(''),
            'mime' => $this->string(30)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%files_trash}}');
    }
}
