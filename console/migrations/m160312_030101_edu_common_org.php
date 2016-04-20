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

class m160312_030101_edu_common_org extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%common_org}}', [
            'id' => $this->primaryKey(),
            'org_name' => $this->string(200)->notNull()->defaultValue(''),
            'province_id' => $this->integer(11)->notNull()->defaultValue(0),
            'city_id' => $this->integer(11)->notNull()->defaultValue(0),
            'dist_id' => $this->integer(11)->notNull()->defaultValue(0),
            'type' => $this->smallInteger(1)->defaultValue(0),
            'xstatus' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'createtime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'updatetime' => $this->timestamp()->defaultValue('0000-00-00 00:00:00'),
            'SchoolId' => $this->string(200)->defaultValue('0'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%common_org}}');
    }
}
