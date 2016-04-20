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

class m160312_030101_edu_exam_publish_item extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%exam_publish_item}}', [
            'id' => $this->primaryKey(),
            'exam_id' => $this->integer(20)->notNull()->defaultValue(0),
            'item_title' => $this->string(255)->notNull()->defaultValue(''),
            'item_num' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'item_score' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'shiti_id' => $this->string(1000)->notNull()->defaultValue(''),
            'displayorder' => $this->smallInteger(6)->defaultValue(0),
            'record_id' => $this->string(18),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%exam_publish_item}}');
    }
}
