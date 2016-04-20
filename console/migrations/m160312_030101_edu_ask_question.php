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

class m160312_030101_edu_ask_question extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%ask_question}}', [
            'question_id' => $this->primaryKey(),
            'question_title' => $this->string(255)->notNull()->defaultValue(''),
            'question_detail' => $this->text(),
            'reward' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'add_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'expired_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'update_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'published_uid' => $this->string(18)->notNull()->defaultValue(''),
            'published_nickname' => $this->string(50)->notNull()->defaultValue(''),
            'published_username' => $this->string(50)->notNull()->defaultValue(''),
            'has_attach' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'grade_id' => $this->integer(8)->notNull()->defaultValue(0),
            'subject_id' => $this->integer(8)->notNull()->defaultValue(0),
            'grade_name' => $this->string(10)->notNull()->defaultValue(''),
            'subject_name' => $this->string(10)->notNull()->defaultValue(''),
            'anonymous' => $this->smallInteger(1)->defaultValue(0),
            'view_count' => $this->integer(11)->defaultValue(0),
            'is_recommend' => $this->smallInteger(1)->defaultValue(0),
            'attach_info' => $this->text(),
            'expired_id' => $this->smallInteger(3)->notNull()->defaultValue(0),
            'question_type_id' => $this->smallInteger(3)->notNull()->defaultValue(0),
            'question_type_name' => $this->string(50)->defaultValue(''),
            'fav_count' => $this->integer(11)->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%ask_question}}');
    }
}
