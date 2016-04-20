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

class m160312_030101_edu_common_stages_subjects extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%common_stages_subjects}}', [
            'sta_sub_id' => $this->primaryKey(),
            'stages_id' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'stages_name' => $this->string(45)->notNull()->defaultValue(''),
            'grades_id' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'grades_name' => $this->string(45)->notNull()->defaultValue(''),
            'subjects_id' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'subjects_name' => $this->string(45)->notNull()->defaultValue(''),
            'xtype' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'sort' => $this->smallInteger(3)->notNull()->defaultValue(0),
            'question_type_id' => $this->smallInteger(6)->defaultValue(0),
            'question_type_name' => $this->string(45)->defaultValue(''),
            'qt_sort' => $this->smallInteger(3)->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%common_stages_subjects}}');
    }
}
