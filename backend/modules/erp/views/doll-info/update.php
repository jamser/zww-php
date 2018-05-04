<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\erp\models\DollInfo */

$this->title = '更新信息: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '娃娃列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="doll-info-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'img_url' => $img_url,
    ]) ?>

</div>
