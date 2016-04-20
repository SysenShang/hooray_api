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

class m160312_030101_edu_exam_publish_shiti_knowledge extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%exam_publish_shiti_knowledge}}', [
            'id' => $this->primaryKey(),
            'knowledge_id' => $this->integer(11)->notNull()->defaultValue(0),
            'user_id' => $this->string(18)->notNull()->defaultValue(''),
            'exam_id' => $this->integer(11)->notNull()->defaultValue(0),
            'record_id' => $this->string(18)->notNull()->defaultValue('0'),
            'shiti_id' => $this->integer(11)->notNull()->defaultValue(0),
            'content' => $this->string(255)->notNull()->defaultValue(''),
            'score' => $this->smallInteger(3)->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%exam_publish_shiti_knowledge}}');
    }
}
