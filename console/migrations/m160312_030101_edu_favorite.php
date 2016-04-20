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

class m160312_030101_edu_favorite extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%favorite}}', [
            'FavoriteId' => $this->primaryKey(),
            'CourseId' => $this->integer(6)->notNull()->defaultValue(0),
            'UserId' => $this->string(18)->notNull()->defaultValue('0'),
            'CreateTime' => $this->integer(11)->notNull(),
            'Type' => $this->integer(11)->notNull(),
            'Title' => $this->string(255)->notNull(),
            'CourseLogo' => $this->string(255)->notNull(),
            'EducationId' => $this->integer(11)->notNull(),
            'EducationName' => $this->string(45)->notNull(),
            'GradeId' => $this->integer(11)->notNull(),
            'GradeName' => $this->string(45)->notNull(),
            'SubjectId' => $this->integer(11)->notNull(),
            'SubjectName' => $this->string(45)->notNull(),
            'TeacherId' => $this->string(18)->notNull(),
            'TeacherName' => $this->string(45)->notNull(),
            'Status' => $this->integer(11)->notNull(),
            'OriginalPrice' => $this->integer(8)->notNull(),
            'DiscountPrice' => $this->integer(8)->notNull(),
            'SectionNumber' => $this->integer(11)->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%favorite}}');
    }
}
