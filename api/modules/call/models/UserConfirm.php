<?php

namespace frontend\modules\call\models;

use Yii;
use common\models\PcallOrder;

/**
 * 用户确认表单
 * 用户需要确认 : 对方有没有打电话过来 服务时间是否准确 服务评分
 */
class UserConfirm extends \yii\base\Model {

    
    /**
     * 订单
     * @var PcallOrder 
     */
    public $order;
    
    /**
     * 评论
     * @var string 
     */
    public $comment;
    
    
    /**
     * 赠送礼物
     * @var type 
     */
    public $gift;
    
    public function rules() {
        return [
            [['comment'], 'required'],
            [['comment'], 'string'],
            [['gift'], 'safe'],
        ];
    }
    
    public function attributeLabels() {
        return [
            'comment'=>'评价',
            'gift'=>'赠送礼物',
        ];
    }
}

