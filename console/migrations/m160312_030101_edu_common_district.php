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

class m160312_030101_edu_common_district extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%common_district}}', [
            'district_id' => $this->integer(8),
            'fid' => $this->integer(8),
            'district_name' => $this->string(765),
            'level' => $this->smallInteger(4),
            'usetype' => $this->smallInteger(1),
            'displayorder' => $this->smallInteger(6),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%common_district}}');
    }
}
