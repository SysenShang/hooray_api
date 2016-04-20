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

class m160312_030101_edu_files_cache extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%files_cache}}', [
            'Id' => $this->primaryKey(),
            'UserId' => $this->string(18)->notNull()->defaultValue('0'),
            'Path' => $this->string(512)->notNull(),
            'PathHash' => $this->string(32)->notNull()->defaultValue(''),
            'Parent' => $this->integer(11)->notNull()->defaultValue(0),
            'FileName' => $this->string(250)->notNull(),
            'MimeType' => $this->integer(11)->notNull()->defaultValue(0),
            'MimePart' => $this->integer(11)->notNull()->defaultValue(0),
            'Size' => $this->bigInteger(20)->notNull()->defaultValue(0),
            'CreateTime' => $this->integer(11)->notNull()->defaultValue(0),
            'UpdateTime' => $this->integer(11)->notNull()->defaultValue(0),
            'Encrypted' => $this->integer(11)->notNull()->defaultValue(0),
            'Category' => $this->string(4)->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%files_cache}}');
    }
}
