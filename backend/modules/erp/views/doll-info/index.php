<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\erp\models\DollInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '娃娃信息';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="doll-info-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('上新', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            ['class' => 'yii\grid\ActionColumn'],
            'dollName',
            'dollTotal',
            [
                'attribute' => 'img_url',
                'value' => function($data){
                    return Html::img($data->img_url,['width' => 50,'height' =>50]);
                },
                'format' =>'raw',
            ],
             'addTime',
             'dollCode',
             'agency',
             'size',
             'type',
             'note',
            'dollCoins',
            'deliverCoins',
            'redeemCoins'
        ],
    ]); ?>
</div>
