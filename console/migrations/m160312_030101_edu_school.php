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

class m160312_030101_edu_school extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%school}}', [
            'id' => $this->bigPrimaryKey(),
            'area_id' => $this->integer(10)->notNull()->defaultValue(0),
            'level' => $this->smallInteger(4)->notNull()->defaultValue(1),
            'name' => $this->string(60)->notNull()->defaultValue(''),
            'created_at' => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%school}}');
    }
}
