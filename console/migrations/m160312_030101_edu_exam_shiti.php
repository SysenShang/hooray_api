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

class m160312_030101_edu_exam_shiti extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%exam_shiti}}', [
            'shiti_id' => $this->primaryKey(),
            'user_id' => $this->string(18)->notNull()->defaultValue(''),
            'exam_gid' => $this->integer(9)->notNull()->defaultValue(0),
            'stage_gid' => $this->integer(8)->notNull()->defaultValue(0),
            'stage_gname' => $this->string(45)->notNull()->defaultValue(''),
            'subject_gid' => $this->integer(8)->notNull()->defaultValue(0),
            'subject_gname' => $this->string(45)->notNull()->defaultValue(''),
            'shiti_type_id' => $this->integer(8)->notNull()->defaultValue(0),
            'shiti_type_name' => $this->string(50),
            'question' => $this->text()->notNull(),
            'question_images' => $this->string(255)->notNull()->defaultValue(''),
            'answer' => $this->text(),
            'analysis' => $this->string(255)->notNull()->defaultValue(''),
            'createtime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'updatetime' => $this->timestamp()->defaultValue('0000-00-00 00:00:00'),
            'score' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'ifshare' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'xstatus' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'difficult' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'xtype' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'generation_exam_numbers' => $this->integer(8)->notNull()->defaultValue(0),
            'answer_html' => $this->text(),
            'analysis_html' => $this->text(),
            'xtype_len' => $this->smallInteger(6)->notNull()->defaultValue(1),
            'choose_config' => $this->string(255)->defaultValue(''),
            'images_h' => $this->text()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%exam_shiti}}');
    }
}
