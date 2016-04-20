<?php
/**
 * Created by Aptana.
 * User: Kevin gates
 * Date: 23/20/15
 * Time: 11:19 PM
 */
namespace mbandroid\modules\v1\controllers;

use common\components\RedisStorage;


use common\models\PassportMessages;
use yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;


use yii\web\NotFoundHttpException;

use yii\data\Pagination;

/**
 * PassportMessagesController implements actions for PassportMessages model.
 */
class PassportMessagesController extends ActiveController
{
	public $modelClass = 'common\models\PassportMessages';
	
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
		
		$behaviors['verbFilter'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'index' => ['get'],
                'delete' => ['post'],
                'update' => ['post'],
                'unread' => ['get'],
            ],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['Index', 'Delete', 'Update', 'Unread'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['Index', 'Delete', 'Update', 'Unread'],
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
	
    /**
     * Lists all PassportMessages models.
     * @return mixed
     */
    public function actionIndex()
    {
    	$user_id = Yii::$app->user->getId();
	    $messages = PassportMessages::find()->where(['user_id' => $user_id, 'isdel' => 0])->orderBy("id desc");
	    $countMessages = clone $messages;
	    $pages = new Pagination(['pageSize' => 20,'totalCount' => $countMessages->count()]);
	    $Messages_page = $messages->offset($pages->offset)
	        ->limit($pages->limit)
	        ->all();				
		$pageCount = $pages->getPageCount();
		$data=array();
		if($Messages_page) {
			foreach($Messages_page as $message) {			
				$message_new["id"] = strval($message->id);
				$message_new["type"] = strval($message->type);
				$message_new["cat_id"] = strval($message->cat_id);
				$message_new["isdel"] = strval($message->isdel);
				$message_new["status"] = strval($message->status);
				$redis_storage = new RedisStorage();
				$user_info = $redis_storage->userinfo($message->user_id, 2);
				$message_new["avatar"] = $user_info['avatar'] ? $user_info['avatar'].".png" : "";
								
				$message_new["sender"] = strval($message->sender);
				$message_new["message"] = strval($message->message);
				$message_new["isdel"] = strval($message->isdel);
				$message_new["reg_date"] = strval($message->reg_date);
				
				$messages_arr["user_messages"][] = $message_new;
			}
		}
		else {
			$messages_arr["user_messages"] = array();
		}
		$messages_arr["page_count"]=strval($pageCount);
		$messages_arr["total_count"]=strval($messages->count());		
		
		return $messages_arr;
    }
	
    /**
     * Deletes an existing PassportMessages model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
    	if(empty($id)) return;     	
        $result = PassportMessages::deleteAll("id in(".$id.")");
		if($result)
        	return ['status' => '200'];
    }
	
    /**
     * Updates an existing PassportMessages model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
    	$post = Yii::$app->request->post();
    	if(isset($post['status'])) {
	        $user_message = $this->findModel($id);			
			$user_message->status = $post['status'];			
	        if ($user_message->save()) {
	            $data = '';
	        }else{	        	
            	$data['msg'] = "save failed";
	        }
		}else{
            $data['msg'] = "status is emppty";
		}
		return $data;
    }

	/**
	 * Updates all PassportMessages by ids.
	 * @param string $post['ids'] message ids
	 * @param string $post['status'] 0=read and 1=unread
	 * @return mixed
	 */
	public function actionUpdateAll()
	{
		$post = Yii::$app->request->post();
		if(isset($post['status']) && isset($post['ids'])) {
			PassportMessages::updateAll(['status' => $post['status']], "id in(".$post['ids'].")");
			$data['msg'] = "save successful";
		}else{
			$data['msg'] = "status is emppty or ids is empty";
		}
		return $data;
	}

	/**
	 * Updates the user all PassportMessages.
	 * @param string $post['ids'] message ids
	 * @param string $post['status'] 0=read and 1=unread
	 * @return mixed
	 */
	public function actionUpdateUserAll()
	{
		$uid   = Yii::$app->user->getId();
		if(isset($uid)) {
			PassportMessages::updateAll(['status' => 1], ['user_id' => $uid]);
			$data['msg'] = "save successful";
		}else{
			$data['msg'] = "not login";
		};
		return $data;
	}

    /**
     * get unread total count and the latest one unread message.
     * @return row
     */
    public function actionUnread()
    {
    	$user_id = Yii::$app->user->getId();
	    $messages = PassportMessages::find()->where(['user_id' => $user_id, 'status' => 0, 'isdel' => 0])->orderBy("id desc");
		
	    $countMessages = clone $messages;
	    $pages = new Pagination(['pageSize' => 1,'totalCount' => $countMessages->count()]);
		
	    $Messages_page = $messages->offset($pages->offset)
	        ->limit($pages->limit)
	        ->all();

		$data=array();
		if($Messages_page) {
			foreach($Messages_page as $message) {			
				$message_new["id"] = strval($message->id);
				$message_new["type"] = strval($message->type);
				$message_new["cat_id"] = strval($message->cat_id);
				$message_new["isdel"] = strval($message->isdel);
				$message_new["status"] = strval($message->status);			
				$message_new["sender"] = strval($message->sender);
				$message_new["message"] = strval($message->message);
				$message_new["isdel"] = strval($message->isdel);
				$message_new["reg_date"] = strval($message->reg_date);
				
				$messages_arr["user_message"][] = $message_new;
			}
		}
		else {
			$messages_arr["user_message"] = array();
		}
		$messages_arr["total_unread_count"]=strval($messages->count());		
		
		return $messages_arr;
    }	
	
    /**
     * Finds the PassportMessages model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PassportMessages the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PassportMessages::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
