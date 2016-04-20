<?php

namespace mbandroid\modules\v1\controllers;

use common\models\Area;
use yii;
use yii\rest\ActiveController;

class AreaController extends ActiveController
{
    public $modelClass = 'common\models\Area';

    public function actionLists()
    {
        $sp_city = [1 => 35, 2 => 36, 9 => 107, 22 => 268];//直辖市
        $pid     = Yii::$app->request->post('parent_id', 0);
        if (in_array($pid, array_keys($sp_city))) {
            $pid = $sp_city[$pid];
        }
        return Area::find()->select('id,area_name name,level')->where([
            'parent_id' => $pid
        ])->asArray()->all();
    }

}
