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

class m160312_030101_edu_qiniu_pfop extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%qiniu_pfop}}', [
            'id' => $this->primaryKey(),
            'input_key' => $this->string(60)->notNull()->defaultValue(''),
            'bucket' => $this->string(15),
            'hash' => $this->string(60),
            'file_key' => $this->string(100),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%qiniu_pfop}}');
    }
}
