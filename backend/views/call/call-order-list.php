<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\call\Caller;
use common\models\call\Order;
use common\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call 订单列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-order-index">
    
    <?php echo $this->render('_order-search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'label'=>'用户',
                'value'=>function($model) {
                    $avatar = '<img src="'.$model->user->avatar.'" width="50" height="50"/>';
                    return "#{$model->user_id} ".$avatar." ".Html::encode($model->user->username);
                },
                'format'=>'html'
            ],
            [
                'label'=>'Caller',
                'value'=>function($model) {
                    $avatar = '<img src="'.$model->callerUser->avatar.'" width="50" height="50"/>';
                    return "#{$model->caller_user_id} ".$avatar." ".Html::encode($model->callerUser->username);
                },
                'format'=>'html'
            ],
            
            [
                'label'=>'预约日期',
                'value'=>function($model) {
                    return date('Y-m-d', $model->booking_date);
                },
            ],
            [
                'label'=>'时段',
                'value'=>function($model) {
                    $todayTime = strtotime('today');
                    return date('H时i分', $todayTime+$model->booking_time_start).' 到 '.date('H时i分', $todayTime + $model->booking_time_end);
                },
            ],
            [
                'label'=>'状态',
                'value'=>function($model) {
                    return Order::$status_list[$model->status];
                }
            ],
            'pay_id',
            [
                'label'=>'支付时间',
                'value'=>function($model) {
                    return date('Y-m-d H:i:s', $model->pay_time);
                },
            ],
            [
                'label'=>'创建时间',
                'value'=>function($model) {
                    return date('Y-m-d', $model->created_at);
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{review}',
                'buttons'=> [
                    'review' =>function ($url, $model, $key) {
                        return Html::a('审核', $url);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
