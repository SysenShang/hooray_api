<?php

namespace pay\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\RechargeOrder as RechargeOrderModel;

/**
 * RechargeOrder represents the model behind the search form about `common\models\RechargeOrder`.
 */
class RechargeOrder extends RechargeOrderModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'coin', 'status'], 'integer'],
            [['order_id', 'trade_no', 'title', 'user_id', 'buyer_id', 'buyer_email', 'createtime', 'updatetime', 'return_url', 'gateway'], 'safe'],
            [['total_price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = RechargeOrderModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'total_price' => $this->total_price,
            'coin' => $this->coin,
            'status' => $this->status,
            'createtime' => $this->createtime,
            'updatetime' => $this->updatetime,
            'user_id' => Yii::$app->user->identity->user_id,
        ]);

        $query->andFilterWhere(['like', 'order_id', $this->order_id])
            ->andFilterWhere(['like', 'trade_no', $this->trade_no])
            ->andFilterWhere(['like', 'title', $this->title])
      //      ->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'buyer_id', $this->buyer_id])
            ->andFilterWhere(['like', 'buyer_email', $this->buyer_email])
            ->andFilterWhere(['like', 'return_url', $this->return_url])
            ->andFilterWhere(['like', 'gateway', $this->gateway]);

        $query->orderBy('id desc');

        return $dataProvider;
    }
}
