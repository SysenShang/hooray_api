<?php
/**
 * Created by PhpStorm.
 * User: lvdongxiao
 * Date: 16/2/25
 * Time: 上午11:33
 */

namespace console\Controllers;

use common\models\AskOrder;
use common\models\Comment;
use common\models\MicroCourse;
use common\models\Question;
use common\models\TchCount;
use common\models\TeacherInfo;
use common\models\User;
use yii;
use yii\console\Controller;
use yii\db\Query;

/**
 * Class TeacherRatingController
 * @package console\Controllers
 */
class TeacherRatingController extends Controller
{
    public function actionReset()
    {
        $result = TchCount::updateAll(['credits' => 0, 'rating' => 1]);
        return $result;
    }

    public function actionRecalculate()
    {
        $teacherList = TchCount::find()
            ->select(['t.user_id', 'u.username', 'i.nickname'])
            ->distinct()
            ->from(TchCount::tableName() . ' t')
            ->leftJoin(User::tableName() . ' u', 'u.user_id = t.user_id')
            ->leftJoin(TeacherInfo::tableName() . ' i', 'i.user_id = t.user_id')
            ->where("u.username is not null and u.username != ''")
            ->asArray()
            ->all();
        foreach ($teacherList as $teacher) {
            $credits = 0;
            $user_id = $teacher['user_id'];
            $count = MicroCourse::find()
                ->where(['user_id' => $user_id, 'publish' => 1, 'xstatus' => 1, 'isauth' => 1, 'isfop' => 0])
                ->andWhere(['>', 'create_time', '2015-10-01'])
                ->count();
            if ($count > 0) {
                $row = MicroCourse::find()
                    ->select(['SUM(if(buynums > 100, 100, buynums)) buynums'])
                    ->where(['user_id' => $user_id, 'publish' => 1, 'xstatus' => 1, 'isauth' => 1, 'isfop' => 0])
                    ->andWhere(['>', 'create_time', '2015-10-01'])
                    ->asArray()
                    ->one();
                $buynums = $row['buynums'];
                $credits += Yii::$app->params['teacherLevelMicroCardinality'] * $count + $buynums;
            }

            $askCount = AskOrder::find()
                ->where(['answer_uid' => $user_id])
                ->andWhere([
                    'confrim' => 2,
                    'replies' => 1,
                    'order_status' => 3,
                    's_is_comment' => 1,
                    'refund_status' => 4
                ])
                ->andWhere(['>', 'order_time', '2015-10-01'])
                ->count();
            if ($askCount > 0) {
                $askRowQuery = AskOrder::find()
                    ->select(['a.question_id', 'a.answer_uid', 'c.comment_rating'])
                    ->distinct()
                    ->from(AskOrder::tableName() . ' a')
                    ->leftJoin(Comment::tableName() . ' c', 'c.target=a.question_id and c.teacher_id=a.answer_uid')
                    ->leftJoin(Question::tableName() . ' q', 'q.question_id=a.question_id')
                    ->where(['answer_uid' => $user_id])
                    ->andWhere([
                        'a.confrim' => 2,
                        'a.replies' => 1,
                        'a.order_status' => 3,
                        'a.s_is_comment' => 1,
                        'a.refund_status' => 4,
                        'q.status' => 1,
                    ])
                    ->andWhere(['>', 'order_time', '2015-10-01']);

                $sum = 'CASE t.comment_rating ';
                for ($i = 2; $i < 6; ++$i) {
                    $askCredit = Yii::$app->params['teacherLevelCoefficient'][$i] * Yii::$app->params['teacherLevelCardinality'];
                    $sum .= "WHEN $i THEN $askCredit ";
                }
                $askCredit = Yii::$app->params['teacherLevelCoefficient'][1] * Yii::$app->params['teacherLevelCardinality'];
                $sum .= "ELSE $askCredit END";
                $askRow = (new Query())
                    ->select("SUM($sum) credits")
                    ->from(['t' => $askRowQuery])
                    ->one();
                $credits += $askRow['credits'];
            }
            $rating = TchCount::getRatingByCredits($credits);
            $result = TchCount::updateAll(['credits' => $credits, 'rating' => $rating], ['user_id' => $user_id]);
            print_r("积分 = $credits; 等级 = $rating; result = $result; (${teacher['username']}, ${teacher['nickname']}, $user_id); \n");
        }
    }
}
