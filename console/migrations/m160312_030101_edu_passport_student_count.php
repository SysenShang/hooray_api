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

class m160312_030101_edu_passport_student_count extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%passport_student_count}}', [
            'user_id' => $this->string(18)->notNull(),
            'coin' => $this->integer(10)->notNull()->defaultValue(0),
            'lock_coin' => $this->integer(10)->defaultValue(0),
            'credits' => $this->integer(10)->notNull()->defaultValue(0),
            'question_num' => $this->integer(8)->notNull()->defaultValue(0),
            'comment_num' => $this->integer(8)->notNull()->defaultValue(0),
            'coureses_num' => $this->integer(8)->notNull()->defaultValue(0),
            'online_coureses_num' => $this->integer(8)->notNull()->defaultValue(0),
            'follower' => $this->integer(8)->notNull()->defaultValue(0),
            'following' => $this->integer(8)->notNull()->defaultValue(0),
            'rating' => $this->smallInteger(2)->notNull()->defaultValue(1),
            'favorites' => $this->smallInteger(8)->notNull()->defaultValue(0),
            'PRIMARY KEY ([[user_id]])',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%passport_student_count}}');
    }
}
