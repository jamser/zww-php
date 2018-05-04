<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\File */

$this->title = $model->name;

$this->title = '查看文件';
$this->params['menus'] = [
    'm1Category'=>'video',
    'm2Title'=>'视频管理',
    'm2'=>[
        [
            'label'=>'视频列表',
            'url'=>'/video/index',
        ],
        [
            'label'=>'上传',
            'url'=>'/video/create',
        ],
        [
            'label'=>'文件管理',
            'url'=>'/file/index',
        ],
    ],
];

$this->params['breadcrumbs'][] = ['label' => 'Files', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-view">

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'type',
            'name',
            [
                'label'=>'URL',
                'format'=>'raw',
                'value'=>'<a href="'.\common\helpers\Cdn::getAuthUrl($model->url).'">'.$model->url.'</a>',
            ],
            'data',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
