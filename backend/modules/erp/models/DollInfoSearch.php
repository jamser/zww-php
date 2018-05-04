<?php

namespace backend\modules\erp\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\erp\models\DollInfo;

/**
 * DollInfoSearch represents the model behind the search form about `backend\modules\erp\models\DollInfo`.
 */
class DollInfoSearch extends DollInfo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'dollTotal'], 'integer'],
            [['dollName', 'img_url', 'addTime', 'dollCode','agency','size','type','note','dollCoins','deliverCoins'], 'safe'],
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
        $query = DollInfo::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'dollTotal' => $this->dollTotal,
            'addTime' => $this->addTime,
        ]);

        $query->andFilterWhere(['like', 'dollName', $this->dollName])
            ->andFilterWhere(['like', 'img_url', $this->img_url])
            ->andFilterWhere(['like', 'agency', $this->agency])
            ->andFilterWhere(['like', 'size', $this->size])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'dollCoins', $this->dollCoins])
            ->andFilterWhere(['like', 'deliverCoins', $this->deliverCoins])
            ->andFilterWhere(['like', 'dollCode', $this->dollCode]);

        return $dataProvider;
    }
}
