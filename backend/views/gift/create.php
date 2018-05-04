<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Gift */

$this->title = '添加礼物';
$this->params['breadcrumbs'][] = ['label' => 'Gifts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gift-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
