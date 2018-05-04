<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\PcallCaller;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\PcallCaller */
/* @var $call_user common\models\PcallUser */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pcall-caller-apply-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($call_user, 'nickname')->textInput() ?>
    
    <?= $form->field($call_user, 'gender')->dropDownList([null=>'请设置',1=>'男',2=>'女'],['disabled'=>!$call_user->canSetGender()]) ?>
    
    <?= $form->field($call_user, 'phone')->textInput() ?>
    
    <div id="sms_code_group" class="form-group <?php if($call_user->hasErrors('sms_code')){echo 'has-error';}
        if($call_user->getOldAttribute('phone')==$call_user->phone && $call_user->phone) echo 'hidden'; ?>">
        <label class="control-label">验证码</label>
        <div class="form-inline">
            <input type="text" id="pcalluser-sms_code" class="form-control" name="PcallUser[sms_code]" value="<?=$call_user->sms_code?>"/>
            <button class="btn btn-default" id="btn_send_smscode" type="button">发送验证码</button>
        </div>
        <div class="help-block">
            <?php 
            if($call_user->hasErrors('sms_code')) {
                echo $call_user->getFirstError('sms_code');
            }
            ?>
        </div>
    </div>
    <!--
    <div class="form-group field-pcallcaller-cover_files required">
        <label class="control-label" for="pcallcaller-cover_files">封面图片</label>
        <input type="hidden" name="PcallCaller[cover_files][]" value="">
        <input type="file" id="pcallcaller-cover_files" name="PcallCaller[cover_files][]" multiple="" accept="image/*">

        <div class="help-block"></div>
    </div>-->
    
    <div class="form-group">
        <label class="control-label" for="pcallcaller-cover_files">封面图片</label>
        <div class="weui-gallery" id="gallery">
            <span class="weui-gallery__img" id="galleryImg"></span>
            <div class="weui-gallery__opr">
                <a href="javascript:" class="weui-gallery__del">
                    <i class="weui-icon-delete weui-icon_gallery-delete"></i>
                </a>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <div class="weui-uploader" id="uploader">
                    <!--<div class="weui-uploader__hd">
                        <p class="weui-uploader__title">图片上传</p>
                        <div class="weui-uploader__info">0/2</div>
                    </div>-->
                    <div class="weui-uploader__bd">
                        <ul class="weui-uploader__files" id="uploaderFiles">
                            <?php
                            $covers = $model->covers ? json_decode($model->covers, true) : [];
                            foreach($covers as $cover) {
                                $coverId = isset($cover['id']) ? (int)$cover['id'] : 0;?>
                                <li class="weui-uploader__file" style="background-image:url(<?=$cover['url']?>)"></li>
                                <input id="cover_<?=$coverId?>" class="weui-uploader__file_input" type="hidden" name="PcallCaller[cover_ids][]" value="<?=$coverId?>"/>
                            <?php
                            }
                            ?>
                        </ul>
                        <div class="weui-uploader__input-box">
                            <input id="uploaderInput" class="weui-uploader__input" type="file" accept="image/*" multiple="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="hidden" id="cover_ids">
            
        </div>

        <div class="help-block">
            <<?php 
            if($call_user->hasErrors('cover_ids')) {
                echo $call_user->getFirstError('cover_ids');
            }
            ?>
        </div>
    </div>
    
    <?php //$form->field($model, 'cover_files[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>
        
    <?= $form->field($model, 'service_time')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '申请' : '更新', ['class' => 'weui-btn weui-btn_primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<div class="weui_dialog_alert" style="display: none;">
    <div class="weui_mask"></div>
    <div class="weui_dialog">
        <div class="weui_dialog_hd"> <strong class="weui_dialog_title">警告</strong>
        </div>
        <div class="weui_dialog_bd">弹窗内容，告知当前页面信息等</div>
        <div class="weui_dialog_ft">
            <a href="javascript:;" class="weui_btn_dialog primary">确定</a>
        </div>
    </div>
</div>

<script>
<?php 
$this->beginBlock('pageScript'); ?>
var old_phone = '<?=$call_user->getOldAttribute('phone')?>';
$('#pcalluser-phone').on('change', function(e){
    if($(this).val().replace(/(^\s*)|(\s*$)/g, "")!==old_phone) {
        $('#sms_code_group').removeClass('hidden');
    } else {
        $('#sms_code_group').addClass('hidden');
    }
});
$('#btn_send_smscode').on('click', function(){
    var phone = $("#pcalluser-phone").val();
    if(!phone) {
        alert('请先输入手机号码');
        return false;
    }
    if($(this).hasClass('loading')) {
        return false;
    }
    $(this).addClass('loading');
    var that = $(this);
    $.ajax({
        url:'<?=Url::to(['/call/user/send-sms-code'])?>', 
        data:{
            phone:phone
        }, 
        success:function(r){
            that.removeClass('loading');
            if(r.code==='OK') {
                alert('发送成功');
            } else {
                alert(r.msg);
            }
        }, 
        error:function() {
            that.removeClass('loading');
        },
        dataType:'json'
    });
});
var csrfToken = $('meta[name="csrf-token"]').attr("content");
var uploadCount = 0;
weui.uploader('#uploader', {
   url: '/call/user/upload-img',
   auto: true,
   type: 'file',
   fileVal: 'file',
   compress: {
       width: 1600,
       height: 1600,
       quality: .8
   },
   onBeforeQueued: function(files) {
       // `this` 是轮询到的文件, `files` 是所有文件

       if(["image/jpg", "image/jpeg", "image/png", "image/gif"].indexOf(this.type) < 0){
           weui.alert('请上传图片');
           return false; // 阻止文件添加
       }
       if(this.size > 10 * 1024 * 1024){
           weui.alert('请上传不超过10M的图片');
           return false;
       }
       if (files.length > 10) { // 防止一下子选择过多文件
           weui.alert('最多只能上传10张图片，请重新选择');
           return false;
       }
       if (uploadCount + 1 > 10) {
           weui.alert('最多只能上传10张图片');
           return false;
       }

       ++uploadCount;

       // return true; // 阻止默认行为，不插入预览图的框架
   },
   onQueued: function(){
       console.log(this);
       // console.log(this.base64); // 如果是base64上传，file.base64可以获得文件的base64

       // this.upload(); // 如果是手动上传，这里可以通过调用upload来实现

       // return true; // 阻止默认行为，不显示预览图的图像
   },
   onBeforeSend: function(data, headers){
       console.log(this, data, headers);
       $.extend(data, { _csrf:csrfToken }); // 可以扩展此对象来控制上传参数
       // $.extend(headers, { Origin: 'http://127.0.0.1' }); // 可以扩展此对象来控制上传头部

       // return false; // 阻止文件上传
   },
   onProgress: function(procent){
       console.log(this, procent);
       // return true; // 阻止默认行为，不使用默认的进度显示
   },
   onSuccess: function (ret) {
       console.log(this, ret);
//       var parseRes = $.parseJSON(ret);
       if(ret.code==='OK') {
           $('#cover_ids').append('<input id="cover_'+ret.data.id+'" class="weui-uploader__file_input" type="hidden" name="PcallCaller[cover_ids][]" value="'+ret.data.id+'"/>');
       } else {
           $.weui.toast(ret.msg);
       }
       // return true; // 阻止默认行为，不使用默认的成功态
   },
   onError: function(err){
       console.log(this, err);
       weui.alert('上传图片出错');
       // return true; // 阻止默认行为，不使用默认的失败态
   }
});
 var tmpl = '<li class="weui-uploader__file" style="background-image:url(#url#)"></li>',
    $gallery = $("#gallery"), $galleryImg = $("#galleryImg"),
    $uploaderInput = $("#uploaderInput"),
    $uploaderFiles = $("#uploaderFiles")
    ;

$uploaderInput.on("change", function(e){
    var src, url = window.URL || window.webkitURL || window.mozURL, files = e.target.files;
    for (var i = 0, len = files.length; i < len; ++i) {
        var file = files[i];

        if (url) {
            src = url.createObjectURL(file);
        } else {
            src = e.target.result;
        }

        $uploaderFiles.append($(tmpl.replace('#url#', src)));
    }
});
var curEle = null;
$uploaderFiles.on("click", "li", function(){
    curEle = $(this);
    $galleryImg.attr("style", this.getAttribute("style"));
    $gallery.fadeIn(100);
});
$gallery.on("click", function(){
    $gallery.fadeOut(100);
}).on('click', '.weui-gallery__del', function(){
    curEle.remove();
    var imgId = curEle.data('id');
    $('#cover_'+imgId).remove();
});


//# sourceURL=pen.js  
<?php $this->endBlock();
$this->registerJs($this->blocks['pageScript']);
?>
</script>
