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

class m160312_030101_edu_exam_publish extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%exam_publish}}', [
            'id' => $this->primaryKey(),
            'exam_id' => $this->integer(10)->notNull()->defaultValue(0),
            'record_id' => $this->string(18)->notNull()->defaultValue('0'),
            'user_id' => $this->string(18)->notNull()->defaultValue(''),
            'exam_name' => $this->string(60)->notNull()->defaultValue(''),
            'xstatus' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'username' => $this->string(15)->notNull()->defaultValue(''),
            'stage_gid' => $this->integer(8)->notNull()->defaultValue(0),
            'stage_gname' => $this->string(45),
            'subject_gid' => $this->integer(8)->notNull()->defaultValue(0),
            'subject_gname' => $this->string(45),
            'exam_sort_id' => $this->integer(8)->notNull()->defaultValue(1),
            'difficult' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'all_nums' => $this->integer(11)->defaultValue(0),
            'unmarking_nums' => $this->integer(11)->defaultValue(0),
            'publish_nums' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'price' => $this->integer(8)->notNull()->defaultValue(0),
            'description' => $this->text()->notNull(),
            'displayorder' => $this->smallInteger(3)->notNull()->defaultValue(0),
            'createtime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'updatetime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'AverageScore' => $this->string(10)->notNull(),
            'Score' => $this->string(10)->notNull(),
            'Peoples' => $this->integer(11)->notNull()->defaultValue(0),
            'Content' => $this->text()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%exam_publish}}');
    }
}
