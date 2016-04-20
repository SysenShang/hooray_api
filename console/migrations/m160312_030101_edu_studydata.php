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

class m160312_030101_edu_studydata extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%studydata}}', [
            'document_id' => $this->primaryKey(),
            'document_name' => $this->string(255)->defaultValue(''),
            'document_size' => $this->integer(11)->notNull()->defaultValue(0),
            'add_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'document_location' => $this->string(255)->notNull()->defaultValue(''),
            'update_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'thumbnail' => $this->string(255)->defaultValue(''),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%studydata}}');
    }
}
