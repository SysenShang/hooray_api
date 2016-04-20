<?php
/**
 * Created by Aptana.
 * User: kevin gates III
 * Date: 11/24/15
 * Time: 7:19 PM
 * This is teacher who can upload ppt or word file.The teacher can see PNG format on white board on the devices.
 */
namespace mbandroid\modules\v1\controllers;

use common\models\TeacherFiles;
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
 * TeacherFilesController implements the CRUD actions for TeacherFiles model.
 */
class TeacherFilesController extends ActiveController
{
	public $modelClass = 'common\models\TeacherFiles';
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
                'view' => ['get'],
                'index' => ['get'],
                'info' => ['get'],
            ],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['View', 'Index', 'Info'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['View', 'Index', 'Info'],
                    'roles' => ['*'],
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
		
        unset($actions['index'], $actions['view']);

        return $actions;
    }

    /**
     * Lists all TeacherFiles records.
     * @return mixed
     */
    public function actionIndex()
    {
    	$get = Yii::$app->request->get();
		if(isset($get['user_id'])){
			$UserId=$get['user_id'];
		} else {
			$UserId = Yii::$app->user->getId();
		}
	    		
		if(empty($UserId)) {
			exit("Please login first.");
		}

		$TeacherFilesAll=TeacherFiles::find()->where(['user_id' => $UserId])
							      			 ->orderBy('id desc');
											 
		$countTeacherFiles = clone $TeacherFilesAll;
		$pages = new Pagination(['pageSize' => 20,'totalCount' => $countTeacherFiles->count()]);
		$TeacherFiles_page = $TeacherFilesAll->offset($pages->offset)
								  			 ->limit($pages->limit)
								  			 ->all();
		$pageCount = $pages->getPageCount();		
		
		if($TeacherFiles_page) {
			foreach ($TeacherFiles_page as $key => $TeacherFile) {
				$TeacherFileArray['id'] = strval($TeacherFile->id);
				$TeacherFileArray['user_id'] = strval($TeacherFile->user_id);
				$TeacherFileArray['persistentId'] = strval($TeacherFile->persistentId);
				$TeacherFileArray['inputBucket'] = strval($TeacherFile->inputBucket);
				$TeacherFileArray['inputKey'] = strval($TeacherFile->inputKey);
				$TeacherFileArray['itemsKey'] = strval($TeacherFile->itemsKey);
				$TeacherFileArray['page_num'] = strval($TeacherFile->page_num);
				$TeacherFileArray['description'] = strval($TeacherFile->description);
				$TeacherFileArray['created_at'] = strval($TeacherFile->created_at);
				$TeacherFileArray['updated_at'] = strval($TeacherFile->updated_at);
				
				$TeacherFiles["teacher_files"][]=$TeacherFileArray;
			}
		} else {
			$TeacherFiles["teacher_files"] = array();
		}			
		$TeacherFiles["page_count"]=strval($pageCount);
		$TeacherFiles["total_count"]=strval($TeacherFilesAll->count());	
		$TeacherFiles["CurrentPage"]=strval($pages->getPage()+1);
		$TeacherFiles["pageLinks"]=$pages->getLinks();
		
		return $TeacherFiles;				
    }
	   
    /**
     * Displays a single TeacherFiles model.
     * @param integer $id
     * @return mixed
     */
    public function actionInfo()
    {    	
		$get = Yii::$app->request->get();
		$id=$get['id'];
		if(empty($id)) {
			exit("Please input file id.");
		}
		
    	return $this->findModel($id);
    }
	   	   
    /**
     * Finds the TeacherFiles model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TeacherFiles the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TeacherFiles::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
