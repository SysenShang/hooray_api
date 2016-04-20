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

class m160312_030101_edu_teacher_givecourses_verif_cate extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%teacher_givecourses_verif_cate}}', [
            'cat_id' => $this->primaryKey(),
            'fid' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'name' => $this->string(15)->notNull()->defaultValue(''),
            'sort' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'display_cate' => $this->smallInteger(1)->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%teacher_givecourses_verif_cate}}');
    }
}
