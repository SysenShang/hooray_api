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

class m160312_030101_edu_exam_buyer_shiti_knowledge extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%exam_buyer_shiti_knowledge}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->string(18)->notNull()->defaultValue(''),
            'exam_id' => $this->integer(8)->notNull()->defaultValue(0),
            'record_id' => $this->string(18)->defaultValue('0'),
            'shiti_id' => $this->bigInteger(8)->notNull()->defaultValue('0'),
            'knowledge_id' => $this->integer(11)->notNull()->defaultValue(0),
            'get_scores' => $this->smallInteger(3)->notNull()->defaultValue(0),
            'content' => $this->text(),
            'scores' => $this->smallInteger(3)->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%exam_buyer_shiti_knowledge}}');
    }
}
