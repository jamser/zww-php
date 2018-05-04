<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\File;
use backend\assets\OssUploadAsset;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
OssUploadAsset::register($this);
$this->title = '上传文件';
$fileTypes = File::getAllTypes();
$typeOptions = [
    null=>'类型'
] + $fileTypes;

?>
<div class="file-index">

    <div id="upload_files">
        <div id="upload_form_list">

        </div>

        <div class="form-group upload-file-actions">
            <button type="button" class="btn btn-primary btn-add" id="btn_select">添加</button>
            <button type="button" class="btn btn-success" id="btn_save">保存</button>
        </div>
    </div>
</div>
<template id="tpl_upload_file_form">
    <div class="upload-file-form" id="{{fileId}}">
        <div class="form-inline">
            <div class="form-group">
                <div  class="form-control">{{fileName}}({{fileSize}})<b></b></div>
            </div>
            <div class="form-group">
                <label for="" class="sr-only"></label>
                <input type="text" name="name" value="" placeholder="名称" class="form-control input-name">
            </div>
            <div class="form-group">
                <label for="" class="sr-only"></label>
                <select class="form-control  input-type" name="type">
                    <?php foreach($typeOptions as $key=>$name): ?>
                        <option value="<?=$key?>"><?=$name?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type='button' class='btn btn-danger btn-del'>删除</button>
        </div>
        <div class="help-block red"></div>
        <div class="mt10">
            <div class="progress">
                <div class="progress-bar" style="width: 0%"></div>
            </div>
        </div>
    </div>
</template>
<?php
$this->beginBlock('uploadJs');
?>
var fileTypes = {
    panoVideo:<?=File::TYPE_PANO_VIDEO?>,
    video:<?=File::TYPE_VIDEO?>,
    panoImg:<?=File::TYPE_PANO_IMG?>,
    img:<?=File::TYPE_IMG?>
};
$(function(){
    $('#upload_files').on('click', '#btn_save', function(e){
        var validate = true;
        $('#upload_files .upload-file-form').each(function(){
            var formEle = $(this);
            if(!formEle.find('.input-name').val()) {
                formEle.find('.help-block').html('请设置文件的名称以作识别');
                validate = false;
            } else if(!formEle.find('.input-type').val()) {
                formEle.find('.help-block').html('请选择文件的类型');
                validate = false;
            } else {
                var fileId = formEle.attr('id');
                var file = uploader.getFile(fileId);
                type = Number(formEle.find('.input-type').val());
                var suffix = get_suffix(file.name).toLowerCase();
                console.log(suffix);
                formEle.find('.help-block').html('');
                if(type===<?=File::TYPE_PANO_VIDEO?> || type===<?=File::TYPE_VIDEO?>) {
                    if(suffix!=='.mp4') {
                        formEle.find('.help-block').html('该类型仅支持MP4文件');
                        validate = false;
                    }
                } else if(type===<?=File::TYPE_PANO_IMG?>) {
                    if(suffix!=='.jpg' && suffix!=='.png') {
                        formEle.find('.help-block').html('该类型仅支持jpg,png文件');
                        validate = false;
                    }
                } else if(type===<?=File::TYPE_IMG?>) {
                    if(suffix!=='.jpg' && suffix!=='.png') {
                        formEle.find('.help-block').html('该类型仅支持jpg,png文件');
                        validate = false;
                    }
                }
            }
        });
        if(validate) {
            uploader.start();
        }
    }).on('click','.btn-del', function(e){
        if(confirm('确定要删除?')) {
            var formEle = $(this).closest('.upload-file-form');
            var fileId = formEle.attr('id');
            var file = uploader.getFile(fileId);
            uploader.removeFile(file);
            formEle.remove();
        }
    });
})

<?php
$this->endBlock();
$this->registerJs($this->blocks['uploadJs'])
?>
