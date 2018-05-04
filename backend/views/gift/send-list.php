<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '赠送礼物记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gift-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'to_user_id',
            'virtual_money_amount',
            'created_at',
            'updated_at',
        ],
    ]); ?>
</div>
