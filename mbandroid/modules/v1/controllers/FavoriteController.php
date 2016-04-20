<?php

namespace mbandroid\modules\v1\controllers;

use common\components\RedisStorage;
use common\models\AskOrder;
use common\models\Favorites;
use common\models\MicroCourse;
use common\models\Question;
use common\models\StudentInfo;
use yii;
use yii\data\Pagination;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;


class FavoriteController extends ActiveController
{
    public $modelClass = 'common\models\Favorites';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors                  = parent::behaviors();
        $behaviors['authenticator'] = [
            //            'except' => ['index'],  // set actions for disable access!
            //            'class' => QueryParamAuth::className(),

            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        $behaviors['access']        = [
            'class' => AccessControl::className(),
            'only' => ['index'],
            'rules' => [
                // allow authenticated users
                //                [
                //                    'allow' => true,
                //                    'actions' => ['index'],
                //                    'roles' => ['?'],
                //                ],
                [
                    'allow' => true,
                    'actions' => ['index'],
                    'roles' => ['@'],
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

    public function getUserName($user_id)
    {
        $redis_storage = new RedisStorage();
        $user_info = $redis_storage->user($user_id);
        return $user_info['username'];
    }

    /**
     * 为了兼容客户端旧代码
     * @return mixed
     * @author grg
     */
    public function actionIndex()
    {
        return $this->actionLists();
    }

    public function actionLists()
    {
        $postData = Yii::$app->request->post();
        $getData = Yii::$app->request->get();
        //为了兼容客户端旧代码
        $postData = array_merge($postData, $getData);
        if (!isset($postData['resource_type'])) {
            Yii::$app->response->statusCode = 400;
            return Yii::$app->response->isNotFound;
        }

        $uid   = Yii::$app->user->getId();
        $query = new Query;
        $query->from(Favorites::tableName() . ' f');
        if ($postData['resource_type'] == 'ask') {
            $query->select(['f.id favorite_id', 'f.createtime favorite_time', 'q.*', 's.avatar', 'a.replies','a.answer_add_time', '(CASE a.replies WHEN 0 THEN "未解答" WHEN 1 THEN "已解答" WHEN 2 THEN "未解答" END) reply']);
            $query->leftJoin(Question::tableName() . ' q', 'q.question_id = f.resource_id');
            $query->leftJoin(AskOrder::tableName() . ' a', 'a.question_id = f.resource_id');
            $query->leftJoin(StudentInfo::tableName() . ' s', 's.user_id = q.published_uid');
        } elseif ($postData['resource_type'] == 'weike') {
            $query->select(['f.id favorite_id', 'f.createtime favorite_time', 'm.*']);
            $query->leftJoin(MicroCourse::tableName() . ' m', 'm.id = f.resource_id');
        }
        $query->where(['f.user_id' => $uid, 'f.resource_type' => $postData['resource_type'], 'f.status' => 0]);
        if ($postData['resource_type'] == 'ask') {
            $query->andWhere(['q.status' => 1]);
        }  elseif ($postData['resource_type'] == 'weike') {
            $query->andWhere(['m.publish' => 1, 'm.xstatus' => 1, 'm.isauth' => 1, 'm.isfop' => 0]);
        }
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        $list = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['createtime' => SORT_DESC])
            ->all();
        if ($pages->pageCount) {
            if ($postData['resource_type'] == 'ask') {
                foreach ($list as &$item) {
                    $attach_info = '';
                    if (!empty($item['attach_info'])) {
                        $attach_info = json_decode($item['attach_info'], true);
                    }
                    if(!empty($attach_info)){
                        if (!isset($attach_info[0])) {
                            $attach_info = [$attach_info];
                        }
                        $item['attach_info'] = $attach_info;
                    }else{
                        $item['attach_info'] = [["imgUrl"=>"","voice_url"=>"","voice_length"=>""]];
                    }
                    $item['question_detail'] = empty($item['question_detail']) ? '' : $item['question_detail'];
                    $item['avatar'] = empty($item['avatar']) ? '' : $item['avatar'];
                }
                unset($item);
            } else {
                foreach ($list as &$item) {
                    $item['avatar'] = empty($item['avatar']) ? '' : $item['avatar'];
                    $item['username'] = $this->getUserName($item['user_id']);
                    $item['realname'] = $item['username'];
                }
                unset($item);
            }
            $data['total_page'] = (string)$pages->pageCount;
            $data['list'] = $list;
        } else {
            $data['list'] = [];
        }
        return $data;
    }

    public function actionCreate()
    {
        $postData = Yii::$app->request->post();
        if (!isset($postData['resource_type']) || !isset($postData['resource_id'])) {
            Yii::$app->response->statusCode = 400;
            return Yii::$app->response->isNotFound;
        }
        $uid    = Yii::$app->user->getId();
        $exists = Favorites::findOne([
            'user_id' => $uid,
            'resource_type' => $postData['resource_type'],
            'resource_id' => $postData['resource_id']
        ]);
        if ($exists) {
            if ($exists['status'] == 1) {
                $exists['status']     = 0;
                $exists['createtime'] = date('Y-m-d H:i:s');
                $exists->save();
                if ($postData['resource_type'] == 'weike') {
                    $micro = MicroCourse::findOne(['id' => $postData['resource_id']]);
                    $micro['favnums'] += 1;
                    $micro->save();
                }
            }
            return ['status' => '200', 'favorite_id' => $exists['id']];
        } else {
            $favorite                  = new Favorites();
            $favorite['user_id']       = $uid;
            $favorite['resource_id']   = $postData['resource_id'];
            $favorite['resource_type'] = $postData['resource_type'];
            $favorite['createtime']    = date('Y-m-d H:i:s');
            $favorite->save();
            if ($postData['resource_type'] == 'weike') {
                $micro = MicroCourse::findOne(['id' => $postData['resource_id']]);
                $micro['favnums'] += 1;
                $micro->save();
            }
            return ['status' => '200', 'favorite_id' => $favorite['id']];
        }
    }

    public function actionCancel()
    {
        $getData   = Yii::$app->request->get();
        $postData   = Yii::$app->request->post();
        $getData = array_merge($getData, $postData);
        $condition = [];
        if (isset($getData['favorite_id'])) {
            $condition['id'] = $getData['favorite_id'];
        } elseif (isset($getData['resource_id']) && isset($getData['resource_type'])) {
            $condition['resource_id']   = $getData['resource_id'];
            $condition['resource_type'] = $getData['resource_type'];
        }
        $condition['user_id'] = Yii::$app->user->getId();
        $favorite = Favorites::findOne($condition);
        if ($favorite) {
            $favorite['status'] = 1;
            $favorite->save();
            if ($getData['resource_type'] == 'weike') {
                $micro = MicroCourse::findOne(['id' => $getData['resource_id']]);
                $micro['favnums'] -= 1;
                $micro->save();
            }
            return ['status' => '200'];
        } else {
            Yii::$app->response->statusCode = 400;
            return ['status' => 7101];
        }
    }

}
