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

class m160312_030101_edu_teacher_examyuan_verify extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%teacher_examyuan_verify}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->string(18)->notNull()->defaultValue(''),
            'username' => $this->string(45)->notNull()->defaultValue(''),
            'examyuan_name' => $this->string(45)->notNull()->defaultValue(''),
            'createtime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'flag' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'exam_num' => $this->integer(6)->notNull()->defaultValue(0),
            'hits' => $this->integer(6)->notNull()->defaultValue(0),
            'examer_num' => $this->integer(6)->notNull()->defaultValue(0),
            'images' => $this->string(255)->notNull()->defaultValue(''),
            'display' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'order' => $this->smallInteger(6)->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%teacher_examyuan_verify}}');
    }
}
