<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\finance\WithdrawApply;

/**
 * WithdrawSearch represents the model behind the search form about `common\models\finance\WithdrawApply`.
 */
class WithdrawSearch extends WithdrawApply
{
    /**
     * 申请开始时间
     * @var integer 
     */
    public $applyBeginTime;
    
    /**
     * 申请结束时间
     * @var integer 
     */
    public $applyEndTime;
    
    /**
     * 申请开始时间
     * @var integer 
     */
    public $payBeginTime;
    
    /**
     * 申请结束时间
     * @var integer 
     */
    public $payEndTime;
    
    /**
     * 手机号
     * @var string 
     */
    public $phone;

    /**
     * 用户名
     * @var string 
     */
    public $username;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'status'], 'integer'],
            [['applyBeginTime', 'applyEndTime', 'payBeginTime', 'payEndTime'], 'date', 'format' => 'yyyy-M-d H:m:s', 'message'=>'请按 2017-01-01 06:00:00 格式输入'],
            [['out_trade_no', 'phone', 'username'], 'safe'],
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
        $query = WithdrawApply::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return false;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            //'pay_time' => $this->pay_time ? strtotime($this->pay_time) : null,
            //'created_at' => $this->created_at ? strtotime($this->created_at) : null,
            'out_trade_no' => $this->out_trade_no
        ]);
        
        if($this->payBeginTime) {
            $query->andFilterWhere(['>=', 'pay_time', strtotime($this->payBeginTime)]);
        }
        if($this->payEndTime) {
            $query->andFilterWhere(['<=', 'pay_time', strtotime($this->payEndTime)]);
        }
        
        if($this->applyBeginTime) {
            $query->andFilterWhere(['>=', 'created_at', strtotime($this->applyBeginTime)]);
        }
        if($this->applyEndTime) {
            $query->andFilterWhere(['<=', 'created_at', strtotime($this->applyEndTime)]);
        }

        //$query->andFilterWhere(['like', 'out_trade_no', $this->out_trade_no]);

        return $dataProvider;
    }
    
    public function getSearchConditions($params, &$sqlParams) {

        $this->load($params);

        $conditions = [];
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $conditions;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            //'pay_time' => $this->pay_time ? strtotime($this->pay_time) : null,
            //'created_at' => $this->created_at ? strtotime($this->created_at) : null,
            'out_trade_no' => $this->out_trade_no
        ]);
        
        
        if($this->payBeginTime) {
            $conditions[] = 'wa';
            $query->andFilterWhere(['>=', 'pay_time', strtotime($this->payBeginTime)]);
        }
        if($this->payEndTime) {
            $query->andFilterWhere(['<=', 'pay_time', strtotime($this->payEndTime)]);
        }
        
        if($this->applyBeginTime) {
            $query->andFilterWhere(['>=', 'created_at', strtotime($this->applyBeginTime)]);
        }
        if($this->applyEndTime) {
            $query->andFilterWhere(['<=', 'created_at', strtotime($this->applyEndTime)]);
        }

        //$query->andFilterWhere(['like', 'out_trade_no', $this->out_trade_no]);

        return [
            
        ];
    }
    
    public function attributeLabels() {
        return parent::attributeLabels()+[
            'payBeginTime'=>'支付起止时间',
            'payEndTime'=>'支付结束时间',
            'applyBeginTime'=>'申请起止时间',
            'applyEndTime'=>'申请结束时间',
            'phone'=>'手机号',
            'username'=>'用户名'
        ];
    }
}
