<?php

namespace backend\models\search;

use Yii;
use common\models\User;
use common\models\call\Caller;
use yii\data\ActiveDataProvider;

/**
 * 订单搜索
 */
class OrderSearch extends Caller {
    
    
    public function search($params)
    {
        $query = Caller::find()->alias('caller');
        $query->joinWith('user user');
        $query->joinWith('phoneAccount phoneAccount');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'caller.apply_time' => SORT_DESC,
                ],
                'attributes' => [
                    'caller.id', 'caller.user_id', 'caller.apply_time',
                ],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'caller.id' => $this->id,
            'caller.user_id' => $this->user_id,
            'user.username' => $this->username,
            'phoneAccount.value' => $this->phone,
            'caller.status' => $this->status,
            'user.sex' => $this->sex,
        ]);

        return $dataProvider;
    }
    
    public function attributeLabels() {
        return parent::attributeLabels() + [
            'username'=>'用户名',
            'sex'=>'性别',
            'phone'=>'手机',
        ];
    }
}

