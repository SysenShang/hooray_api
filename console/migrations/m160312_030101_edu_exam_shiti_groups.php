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

class m160312_030101_edu_exam_shiti_groups extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%exam_shiti_groups}}', [
            'gid' => $this->primaryKey(),
            'fid' => $this->integer(9)->notNull()->defaultValue(0),
            'exam_groups_name' => $this->string(60)->notNull()->defaultValue(''),
            'user_id' => $this->string(18)->notNull()->defaultValue(''),
            'createtime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'updatetime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'displayorder' => $this->smallInteger(3)->notNull()->defaultValue(0),
            'xstatus' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'xtype' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'exam_numbers' => $this->smallInteger(6)->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%exam_shiti_groups}}');
    }
}
