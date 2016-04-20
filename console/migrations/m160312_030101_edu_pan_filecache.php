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

class m160312_030101_edu_pan_filecache extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%pan_filecache}}', [
            'fileid' => $this->primaryKey(),
            'storage' => $this->string(18)->notNull()->defaultValue('0'),
            'path' => $this->string(400)->notNull()->defaultValue('0'),
            'path_hash' => $this->string(32)->notNull()->defaultValue('0'),
            'parent' => $this->integer(11)->notNull()->defaultValue(0),
            'name' => $this->string(250)->notNull()->defaultValue('0'),
            'mimetype' => $this->integer(11)->notNull()->defaultValue(0),
            'mimepart' => $this->integer(11)->notNull()->defaultValue(0),
            'size' => $this->bigInteger(20)->notNull()->defaultValue(0),
            'mtime' => $this->integer(11)->notNull()->defaultValue(0),
            'storage_mtime' => $this->integer(11)->notNull()->defaultValue(0),
            'encrypted' => $this->integer(11)->notNull()->defaultValue(0),
            'unencrypted_size' => $this->bigInteger(20)->notNull()->defaultValue(0),
            'etag' => $this->string(40),
            'permissions' => $this->integer(11)->defaultValue(0),
            'title' => $this->string(120)->notNull()->defaultValue(''),
            'OriginalPrice' => $this->integer(8),
            'education_id' => $this->integer(11),
            'education_name' => $this->string(45),
            'grade_id' => $this->integer(11),
            'grade_name' => $this->string(45),
            'subject_id' => $this->integer(11),
            'subject_name' => $this->string(45),
            'CreateTime' => $this->integer(11),
            'UpdateTime' => $this->integer(11),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%pan_filecache}}');
    }
}
