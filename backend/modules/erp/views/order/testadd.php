<?php
use yii\widgets\ActiveForm;
$this->title = '上传表';
$this->params['breadcrumbs'][] = $this->title;
$form = ActiveForm::begin(["options" => ["enctype" => "multipart/form-data"]]); ?>
<?= $form->field($model, "dollCode")->fileInput()->label('请选择文件 ：') ?>
    <button>上传</button>
<?php ActiveForm::end(); ?>