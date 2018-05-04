<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '礼物';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gift-index">

    <p>
        <?= Html::a('添加礼物', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'virtual_price',
            'send_count',
            'created_at',
            'updated_at',
            'score',
            [
                'label'=>'链接',
                'format'=>'html',
                'value'=>function($model) {
                    return Html::a($model->url, $model->url, ['target'=>'_blank']);
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
