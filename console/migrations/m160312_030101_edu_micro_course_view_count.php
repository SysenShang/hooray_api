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

class m160312_030101_edu_micro_course_view_count extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%micro_course_view_count}}', [
            'id' => $this->primaryKey(),
            'micro_id' => $this->integer(11)->notNull()->defaultValue(0),
            'user_id' => $this->string(20),
            'view_counts' => $this->integer(11)->defaultValue(0),
            'ip' => $this->string(20),
            'created_time' => $this->datetime()->defaultValue('0000-00-00'),
            'updated_time' => $this->datetime(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%micro_course_view_count}}');
    }
}
