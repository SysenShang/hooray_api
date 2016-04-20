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

class m160312_030101_edu_rating extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%rating}}', [
            'id' => $this->primaryKey(),
            'xtype' => $this->string()->notNull()->defaultValue('student'),
            'rating' => $this->string(100)->notNull()->defaultValue(''),
            'rating_img' => $this->string(255)->notNull(),
            'rating_desc' => $this->string(100)->notNull()->defaultValue(''),
            'conditions' => $this->string(100)->notNull()->defaultValue(''),
            'rate' => $this->string(10)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%rating}}');
    }
}
