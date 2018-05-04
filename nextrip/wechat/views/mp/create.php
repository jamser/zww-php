<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model nextrip\wechat\models\Mp */

$this->title = 'Create Mp';
$this->params['breadcrumbs'][] = ['label' => 'Mps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mp-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
