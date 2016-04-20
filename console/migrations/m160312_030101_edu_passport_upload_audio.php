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

class m160312_030101_edu_passport_upload_audio extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%passport_upload_audio}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->string(18)->notNull()->defaultValue('0'),
            'file_key' => $this->string(255)->notNull()->defaultValue(''),
            'file_hash' => $this->string(60)->notNull()->defaultValue(''),
            'file_fname' => $this->string(255)->notNull()->defaultValue(''),
            'file_ext' => $this->string(50)->notNull()->defaultValue(''),
            'file_size' => $this->integer(20)->notNull()->defaultValue(0),
            'file_from' => $this->smallInteger(2)->notNull()->defaultValue(0),
            'createtime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'updatetime' => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%passport_upload_audio}}');
    }
}
