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

class m160312_030101_edu_teacher_givecourses_verify_teaching extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%teacher_givecourses_verify_teaching}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->string(18)->notNull(),
            'stages_id' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'stages_name' => $this->string(45)->notNull()->defaultValue(''),
            'subjects_id' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'subjects_name' => $this->string(45)->notNull()->defaultValue(''),
            'createtime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'is_recommend' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'user_name' => $this->string(50)->notNull()->defaultValue(''),
            'flag' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'verify_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'rathing' => $this->smallInteger(4)->notNull()->defaultValue(1),
            'is_rathing' => $this->smallInteger(1)->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%teacher_givecourses_verify_teaching}}');
    }
}
