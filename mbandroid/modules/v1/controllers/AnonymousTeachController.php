<?php

namespace mbandroid\modules\v1\controllers;

use common\models\TeachSchedule;
use yii;
use yii\rest\ActiveController;

class AnonymousTeachController extends ActiveController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->modelClass = '';
        parent::init();
    }

    public function actions()
    {
        return [];
    }

    /**
     * 开课信息
     * @return array|yii\db\ActiveRecord[]
     * @author grg
     */
    public function actionSchedule()
    {
        $data = Yii::$app->request->post();

        $schedule = TeachSchedule::find()->where([
            'code' => $data['code']
        ])->andWhere('date >=' . date('Y-m-d'))->asArray()->one();
        if (count($schedule) > 0) {
            $now = time();

            $beginTime = strtotime($schedule['date'] . ' ' . $schedule['begintime']);
            $coming    = $now > $beginTime - 900;
            $endTime   = strtotime($schedule['date'] . ' ' . $schedule['endtime']);
            $over      = $now > $endTime + 900;

            $schedule['expire']    = (int)($now >= $beginTime);
            $schedule['is_up']     = (int)($now >= $beginTime);
            $schedule['is_down']   = (int)($now > $endTime);
            $schedule['is_coming'] = (int)$coming;
            $schedule['is_over']   = (int)$over;
            $schedule['can_join']  = (int)($coming && !$over);

            $schedule['now']      = date('Y-m-d H:i:s');
            $schedule['time']     = $now;
            $schedule['duration'] = ($endTime - $beginTime) / 60;
        }
        return [$schedule];
    }
}
