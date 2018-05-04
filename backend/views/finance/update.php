<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\finance\WithdrawalsApply */

$this->title = 'Update Withdrawals Apply: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Withdrawals Applies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="withdrawals-apply-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
