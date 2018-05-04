<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PcallCaller */
/* @var $call_user common\models\PcallUser */

$this->title = '申请成为起床达人';
$this->params['breadcrumbs'][] = ['label' => 'Pcall Caller Applies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pcall-caller-apply-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'call_user' => $call_user,
    ]) ?>

</div>
