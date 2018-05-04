<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\doll\models\Inform */

$this->title = '添加通知人';
$this->params['breadcrumbs'][] = ['label' => 'Informs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inform-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
