<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mps';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mp-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Mp', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'key',
            'app_id',
            'default_reply',
            'default_welcome',
            //'access_token',
            // 'js_ticket',
            // 'created_at',
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {set-menu}',
                'buttons' => [
                    'set-menu'=>function($url, $model, $index) {
                        return Html::a('设置菜单', $url, ['class'=>'btn btn-default','target'=>'_blank']);
                    }
                ]
            ],
        ],
    ]); ?>
</div>
