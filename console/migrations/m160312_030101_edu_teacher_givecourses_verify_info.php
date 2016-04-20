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

class m160312_030101_edu_teacher_givecourses_verify_info extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%teacher_givecourses_verify_info}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->string(18)->notNull()->defaultValue(''),
            'username' => $this->string(30)->defaultValue(''),
            'cat_id' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'name' => $this->string(15)->notNull()->defaultValue(''),
            'number' => $this->string(50)->defaultValue(''),
            'images' => $this->string(255)->notNull()->defaultValue(''),
            'datetime' => $this->timestamp()->defaultValue('0000-00-00 00:00:00'),
            'flag' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'verify_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'statistics_flag' => $this->smallInteger(1)->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%teacher_givecourses_verify_info}}');
    }
}
