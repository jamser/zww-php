<?php

namespace backend\modules\doll\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\doll\models\Member;

/**
 * MemberSearch represents the model behind the search form about `backend\modules\doll\models\Member`.
 */
class MemberSearch extends Member
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'coins', 'points', 'catch_number', 'modified_by', 'first_login', 'first_charge'], 'integer'],
            [['memberID', 'name', 'mobile', 'password', 'weixin_id', 'gender', 'birthday', 'register_date', 'modified_date', 'last_login_date', 'last_logoff_date', 'icon_context_path', 'icon_file_name', 'icon_real_path', 'easemob_uuid', 'register_from', 'last_login_from', 'register_channel', 'login_channel', 'phone_model'], 'safe'],
            [['online_flg', 'active_flg', 'invite_flg', 'invite_flg_web'], 'boolean'],
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
        $query = Member::find();

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
            'birthday' => $this->birthday,
            'coins' => $this->coins,
            'points' => $this->points,
            'catch_number' => $this->catch_number,
            'register_date' => $this->register_date,
            'modified_date' => $this->modified_date,
            'modified_by' => $this->modified_by,
            'last_login_date' => $this->last_login_date,
            'last_logoff_date' => $this->last_logoff_date,
            'online_flg' => $this->online_flg,
            'active_flg' => $this->active_flg,
            'invite_flg' => $this->invite_flg,
            'invite_flg_web' => $this->invite_flg_web,
            'first_login' => $this->first_login,
            'first_charge' => $this->first_charge,
        ]);

        $query->andFilterWhere(['like', 'memberID', $this->memberID])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'weixin_id', $this->weixin_id])
            ->andFilterWhere(['like', 'gender', $this->gender])
            ->andFilterWhere(['like', 'icon_context_path', $this->icon_context_path])
            ->andFilterWhere(['like', 'icon_file_name', $this->icon_file_name])
            ->andFilterWhere(['like', 'icon_real_path', $this->icon_real_path])
            ->andFilterWhere(['like', 'easemob_uuid', $this->easemob_uuid])
            ->andFilterWhere(['like', 'register_from', $this->register_from])
            ->andFilterWhere(['like', 'last_login_from', $this->last_login_from])
            ->andFilterWhere(['like', 'register_channel', $this->register_channel])
            ->andFilterWhere(['like', 'login_channel', $this->login_channel])
            ->andFilterWhere(['like', 'phone_model', $this->phone_model]);

        return $dataProvider;
    }
}
