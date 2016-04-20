<?php
/**
 * 已废弃
 * 废弃时间: 2016-2-20 10:00
 * Created by PhpStorm.
 * User: chenwei
 * Date: 10/30/15
 * Time: 10:00 AM
 */
namespace mbandroid\modules\v1\controllers;

use common\models;
use common\models\MicroCourse;
use common\models\User;
use yii;
use yii\data\Pagination;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;

class  AdverListController extends ActiveController
{
    public $modelClass = 'common\models\Advertisement';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'except' => ['index'],  // set actions for disable access!
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        $behaviors['verbFilter'] = [
            'class' => VerbFilter::className(),
//                'actions' => $this->verbs(),
            'actions' => [
                'index' => ['get'],
            ],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['Index'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['Index'],
                    'roles' => ['@'],
                ],
                [
                    'allow' => true,
                    'actions' => ['Index'],
                    'roles' => ['?'],
                ],
                // everything else is denied
            ],
            'denyCallback' => function () {
                throw new \Exception('您无权访问该页面');
            },
        ];

        return $behaviors;
    }


    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);

        return $actions;
    }

    /**广告微课列表
     *
     * @return array
     */

    /*  查看广告微课列表 */
    public function actionIndex()
    {
        $getData = Yii::$app->request->get();
        $getData['id'] = isset($getData['id']) ? $getData['id'] : "";

        //微课制作列表
        if ($getData['id'] == "1") {
            $query = new Query();
            $query->from(MicroCourse::tableName());
            $query->select('*');
            $query->where(['xstatus' => '1', 'isauth' => '1', 'publish' => '1', 'isfop' => '0', 'grade_id' => '1001000']);
            $query->orderBy('id asc');
            $count = clone $query;
            $pages = new Pagination(['totalCount' => $count->count(), 'pageSize' => 15]);
            $list = $query->offset($pages->offset)->limit($pages->limit)->all();
            $infolist = array();
            if ($list) {
                foreach ($list as $m_list) {
                    $feed_id = isset($m_list['id']) ? $m_list['id'] : "";
                    $result['id'] = isset($m_list['id']) ? $m_list['id'] : "";
                    $result['name'] = isset($m_list['name']) ? $m_list['name'] : "";
                    $result['realname'] = isset($m_list['realname']) ? $m_list['realname'] : "";
                    $result['stagename'] = isset($m_list['stagename']) ? $m_list['stagename'] : "";
                    $result['gradename'] = isset($m_list['gradename']) ? $m_list['gradename'] : "";
                    $result['coursename'] = isset($m_list['coursename']) ? $m_list['coursename'] : "";
                    $result['video_url'] = isset($m_list['video_url']) ? $m_list['video_url'] : "";
                    $result['video_small_image'] = isset($m_list['video_small_image']) ? $m_list['video_small_image'] : "";
                    $result['video_middle_image'] = isset($m_list['video_middle_image']) ? $m_list['video_middle_image'] : "";
                    $result['video_big_image'] = isset($m_list['video_big_image']) ? $m_list['video_big_image'] : "";
                    $result['video_duration'] = isset($m_list['video_duration']) ? $m_list['video_duration'] : "";
                    $result['content'] = isset($m_list['content']) ? $m_list['content'] : "";
                    $result['viewnums'] = isset($m_list['viewnums']) ? $m_list['viewnums'] : "";
                    $result['price'] = isset($m_list['price']) ? $m_list['price'] : "";
                    $username = User::findOne($m_list['user_id'])['username'];
                    $result['username'] = isset($username) ? $username : "";
                    $infolist[] = array('feed_id' => $feed_id, 'feed' => $result);


                }
            }
            unset($list);
            $data = array(
                'total_number' => $count->count(),
                'page' => "1",
                'page_size' => $pages->pageSize,
                'data' => $infolist

            );
            return $data;


        }
    }


}
