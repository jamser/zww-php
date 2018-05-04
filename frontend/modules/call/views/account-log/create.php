<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PcallAccountLog */

$this->title = 'Create Pcall Account Log';
$this->params['breadcrumbs'][] = ['label' => 'Pcall Account Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pcall-account-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
