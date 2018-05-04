<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\erp\models\DollInfo */

$this->title = '上新';
$this->params['breadcrumbs'][] = ['label' => '娃娃列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="doll-info-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'img_url' => $img_url,
    ]) ?>

</div>
