<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 8/10/15
 * Time: 2:25 PM
 */
namespace common\models;

use yii;
use yii\base\Model;

use yii\data\ActiveDataProvider;


class StageSearch extends Stgsubjects
{
    public function rules()
    {
        // only fields in rules() are searchable
        return [
            [['sta_sub_id'], 'integer'],
            [['grades_id', 'grades_name','subjects_id','subjects_name','stages_id','stages_name'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Stgsubjects::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // load the seach form data and validate
        if (!($this->load($params) && $this->validate())) {
    $a =             $dataProvider->getModels();
            print_r($a);
            return $dataProvider;
        }else{
            echo 2222;
        }
        // adjust the query by adding the filters
        $query->andFilterWhere(['grades_name' => $this->grades_name]);
//        $query->andFilterWhere(['like', 'title', $this->name])
//            ->andFilterWhere(['like', 'creation_date', $this->creation_date]);
        return $dataProvider;
    }
}
