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

class m160312_030101_edu_online_courses extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%online_courses}}', [
            'course_id' => $this->primaryKey(),
            'title' => $this->string(40)->notNull()->defaultValue(''),
            'discount' => $this->integer(8)->notNull()->defaultValue(8),
            'course_price' => $this->integer(8)->notNull()->defaultValue(0),
            'price' => $this->integer(8)->notNull()->defaultValue(0),
            'rating' => $this->string(10)->notNull()->defaultValue('0'),
            'ratingNum' => $this->integer(8)->notNull()->defaultValue(0),
            'tags' => $this->string(200)->notNull()->defaultValue(''),
            'courses_audition_time' => $this->integer(8)->notNull()->defaultValue(0),
            'smallPicture' => $this->string(200)->notNull()->defaultValue(''),
            'middlePicture' => $this->string(200)->notNull()->defaultValue(''),
            'largePicture' => $this->string(200)->notNull()->defaultValue(''),
            'teacher_id' => $this->string(18),
            'teacherName' => $this->string(40)->notNull()->defaultValue(''),
            'teacher_rating' => $this->integer(10)->notNull()->defaultValue(0),
            'recommended' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'recommendedTime' => $this->timestamp()->notNull(),
            'hitNum' => $this->integer(8)->notNull()->defaultValue(0),
            'free' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'education_id' => $this->integer(8)->notNull()->defaultValue(0),
            'educationName' => $this->string(40)->defaultValue(''),
            'subject_id' => $this->integer(8)->notNull()->defaultValue(0),
            'subjectName' => $this->string(40)->notNull()->defaultValue(''),
            'grade_id' => $this->integer(8)->notNull()->defaultValue(0),
            'gradeName' => $this->string(40)->defaultValue(''),
            'course_introduce' => $this->text(),
            'startTime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'endTime' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'update_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'create_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'room' => $this->string(40),
            'orderNum' => $this->integer(8)->defaultValue(0),
            'course_logo' => $this->string(255)->notNull()->defaultValue('1'),
            'status' => $this->integer(8)->notNull()->defaultValue(0),
            'sectionNumber' => $this->integer(10)->notNull()->defaultValue(0),
            'Positive' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'section_long' => $this->integer(10)->notNull()->defaultValue(0),
            'Moderate' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'Negative' => $this->smallInteger(6)->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%online_courses}}');
    }
}
