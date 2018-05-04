<?php

use yii\helpers\Html;
use common\models\Setting;
use common\models\user\ChangeLog;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $caller common\models\call\Caller */
/* @var $user common\models\User */
/* @var $form frontend\models\CallerApplyForm */

$this->title = '更新资料';
?>

<div class="caller-update">
    <?php ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="form-group">
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">头像</label></div>
            <div class="weui-cell__bd" id="uploader">
                <div class="avatar" id="avatar">
                    <img src="<?=$form->avatar?$form->avatar:$user->getAvatar(80);?>" width="80" height="80"/>
                    <input type="hidden" id="avatar_id" name="data[avatar_id]" value="<?=$form->avatar_id?>"/>
                </div>
                <input id="uploaderInput" class="weui-uploader__input" type="file" accept="image/*" capture="camera" multiple="" />
            </div>
        </div>
        <div class="weui-cells__tips red"><?=$form->getFirstError('avatar_id')?></div>
        
        <div class="weui-cell" id="cover_uploader">
            <div class="weui-cell__bd">
                <div class="weui-uploader">
                    <div class="weui-uploader__hd">
                        <p class="weui-uploader__title">封面相册</p>
                        <div class="weui-uploader__info"><span id="uploadCount"><?=count($form->covers)?></span>/8</div>
                    </div>
                    <div class="weui-uploader__bd">
                        <ul class="weui-uploader__files" id="uploaderFiles">
                            <?php 
                            $num = 1;
                            foreach($form->covers as $cover):?>
                                <li class="weui-uploader__file" style="background-image:url(<?=$cover['url']?>)" 
                                    data-fileid="<?=$num;?>"
                                    id="cover_<?=$cover['id']?>">
                                    <input type="hidden" name="data[covers][]" value="<?=$cover['id']?>"/>
                                </li>
                            <?php 
                            $num++;
                            endforeach;?>
                        </ul>
                        <div class="weui-uploader__input-box" id="btn_add_cover">
                            <input id="uploaderInput" class="weui-uploader__input" type="file" accept="image/*" multiple="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="weui-cells__tips red"><?=$form->getFirstError('covers')?></div>
        <div class="weui-cells__tips">上传多张真实照片 能提升你的吸引力哦</div>
    </div>
    
    <div class="form-group">
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">昵称</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="text" placeholder="请输入昵称" value="<?=$form->username?>" name="data[username]">
            </div>
        </div>
        <div class="weui-cells__tips red"><?=$form->getFirstError('username')?></div>
        
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">真实姓名</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="text" placeholder="请输入 真实姓名" value="<?=$form->true_name?>" name="data[true_name]">
            </div>
        </div>
        <div class="weui-cells__tips red"><?=$form->getFirstError('true_name')?></div>
        <div class="weui-cells__tips">用于提现</div>
        
        <div class="weui-cell weui-cell_select weui-cell_select-after<?=($user->sex && ChangeLog::getChangeCount($user->id, 'sex'))?' hidden':''?>">
            <div class="weui-cell__hd">
                <label for="" class="weui-label">性别</label>
            </div>
            <div class="weui-cell__bd">
                <select class="weui-select" name="data[sex]">
                    <option value="0">请选择</option>
                    <option value="1" <?=$form->sex==1?'selected':''?>>男</option>
                    <option value="2" <?=$form->sex==2?'selected':''?>>女</option>
                </select>
            </div>
        </div>
        <div class="weui-cells__tips red"><?=$form->getFirstError('sex')?></div>
        
        <div class="weui-cell<?=$user->birthday?' hidden':''?>">
            <div class="weui-cell__hd"><label for="" class="weui-label">生日</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="date" value="data[birthday]" value="<?=$form->birthday?>">
            </div>
        </div>
        <div class="weui-cells__tips red"><?=$form->getFirstError('birthday')?></div>
        
        <div class="weui-cell">
            <div class="weui-cell__hd"><label for="" class="weui-label">位置</label></div>
            <div class="weui-cell__bd">
                上海 杨浦区
            </div>
        </div>
        
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <label for="" class="weui-label">个人介绍</label>
                <textarea class="weui-textarea" placeholder="" rows="2" name="data[about]"><?=$form->about?></textarea>
            </div>
        </div>
        
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <label for="" class="weui-label">可预约时间</label>
                <textarea class="weui-textarea" placeholder="" rows="2" name="data[service_time]"><?=$form->service_time?></textarea>
            </div>
        </div>
        <div class="weui-cells__tips red"><?=$form->getFirstError('service_time')?></div>
        <div class="weui-cells__tips">
            例: 周一到周五 7:00-8:00 周六周日 8:30-9:00
        </div>
        
    </div>
    
    <div class="weui-btn-area">
        <button class="weui-btn weui-btn_primary" id="btn_save_profile" type="submit">确定</a>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<script>
<?php $this->beginBlock('pageSript'); ?>
/* 图片自动上传 */
var uploadCount = <?=count($form->covers)?>, uploadList = [];
var uploadCountDom = document.getElementById("uploadCount");
weui.uploader('#uploader', {
    url: '//<?=Yii::getAlias('@frontendHost')?>/api/file/upload?type=avatar',
    auto: true,
    type: 'file',
    fileVal: 'fileVal',
    compress: {
        width: 1600,
        height: 1600,
        quality: .8
    },
    onBeforeQueued: function(files) {
        if(["image/jpg", "image/jpeg", "image/png", "image/gif"].indexOf(this.type) < 0){
            weui.alert('请上传图片');
            return false;
        }
        if(this.size > 10 * 1024 * 1024){
            weui.alert('请上传不超过10M的图片');
            return false;
        }
        if (files.length > 1) { // 防止一下子选中过多文件
            weui.alert('最多只能选择1张图片，请重新选择');
            return false;
        }
    },
    onQueued: function(){
        console.log(this);
    },
    onBeforeSend: function(data, headers){
        console.log('on before send',this, data, headers);
        // $.extend(data, { test: 1 }); // 可以扩展此对象来控制上传参数
        // $.extend(headers, { Origin: 'http://127.0.0.1' }); // 可以扩展此对象来控制上传头部

        // return false; // 阻止文件上传
    },
    onProgress: function(procent){
        console.log(this, procent);
    },
    onSuccess: function (ret) {
        console.log(this, ret);
        if(ret.code===0) {
            $('#avatar img').attr('src', ret.result.url);
            $('#avatar_id').val(ret.result.id);
        } else {
            weui.alert(ret.msg);
        }
    },
    onError: function(err){
        console.log(this, err);
    }
});

weui.uploader('#cover_uploader', {
    url: '//<?=Yii::getAlias('@frontendHost')?>/api/file/upload?type=cover',
    auto: true,
    type: 'file',
    fileVal: 'fileVal',
    compress: {
        width: 1600,
        height: 1600,
        quality: .8
    },
    onBeforeQueued: function(files) {
        if(["image/jpg", "image/jpeg", "image/png", "image/gif", "video/mp4", "audio/mp4", "application/mp4"].indexOf(this.type) < 0){
            weui.alert('请上传图片或MP4视频');
            return false;
        }
        if(this.size > 20 * 1024 * 1024){
            weui.alert('请上传不超过20M的文件');
            return false;
        }
        if (files.length > 1) { // 防止一下子选中过多文件
            weui.alert('每次最多只能选择1张照片，请重新选择');
            return false;
        }
        if (uploadCount + files.length > 8) {
            weui.alert('最多只能上传8张图片');
            return false;
        }

        ++uploadCount;
        if(uploadCount>=8) {
            $('#btn_add_cover').hide();
        } else {
            $('#btn_add_cover').show();
        }
        uploadCountDom.innerHTML = uploadCount;
        
    },
    onQueued: function(){
        uploadList.push(this);
        console.log("onQueued",this);
    },
    onBeforeSend: function(data, headers){
        console.log('on before send',this, data, headers);
        // $.extend(data, { test: 1 }); // 可以扩展此对象来控制上传参数
        // $.extend(headers, { Origin: 'http://127.0.0.1' }); // 可以扩展此对象来控制上传头部

        // return false; // 阻止文件上传
    },
    onProgress: function(procent){
        console.log(this, procent);
    },
    onSuccess: function (ret) {
        console.log(this, ret);
        if(ret.code===0) {
            //$('#avatar img').attr('src', ret.result.url);
            //$('#avatar_id').val(ret.result.id);
            var id = this.id;
            $('#uploaderFiles li').each(function(){
                if($(this).data('id')==id) {
                    $(this).append('<input type="hidden" name="data[covers][]" value="'+ret.result.id+'"/>');
                }
            });
            //return true;
        } else {
            weui.alert(ret.msg);
            return false;
        }
    },
    onError: function(err){
        console.log(this, err);
    }
});

// 缩略图预览
document.querySelector('#uploaderFiles').addEventListener('click', function (e) {
    var target = e.target;

    while (!target.classList.contains('weui-uploader__file') && target) {
        target = target.parentNode;
    }
    if (!target) return;

    var url = target.getAttribute('style') || '';
    var id = target.getAttribute('data-id');

    if (url) {
        url = url.match(/url\((.*?)\)/)[1].replace(/"/g, '');
    }
    var gallery = weui.gallery(url, {
        className: 'custom-name',
        onDelete: function onDelete() {
            weui.confirm('确定删除该图片？', function () {
                --uploadCount;
                if(uploadCount>=8) {
                    $('#btn_add_cover').hide();
                } else {
                    $('#btn_add_cover').show();
                }
                uploadCountDom.innerHTML = uploadCount;

                for (var i = 0, len = uploadList.length; i < len; ++i) {
                    var file = uploadList[i];
                    if (file.id == id) {
                        file.stop();
                        break;
                    }
                }
                target.remove();
                gallery.hide();
            });
        }
    });
});

var sendSmsInterval;

$('#btn_get_code').on('click', function() {
    if($(this).attr('disabled')) {
        return false;
    }
    var btn = $(this);
    var phone = $('#phone').val();
    if(!phone || !checkMobile(phone)) {
        weui.alert('请输入11位手机号');
        return false;
    }
    sendSmsCode(btn);
});

function sendSmsCode(btn) {
    var phone = $('#phone').val();
    btn.html('发送中..').attr('disabled', true);
    
    $.ajax({
        type:"POST",
        url:"<?= \yii\helpers\Url::to(['/api/common/send-sms-code'])?>",
        data:{
            phoneNum:phone,
            type:"applyCaller"
        },
        dataType:'json',
        success:function(r) {
            if(r.code===0) {
                smsCounter(btn, 60);
            } else {
                weui.alert(r.msg);
                btn.html('获取验证码').removeAttr('disabled');
            }
        },
        error:function() {
            btn.html('获取验证码').removeAttr('disabled');
        }
    });
}

function smsCounter(btn, second) {
    btn.html(second+'秒后重试');
    if(second>0) {
        setTimeout(function(){
            smsCounter(btn, --second);
        },1000);
    } else {
        btn.removeAttr('disabled');
    }
}


<?php
if(Yii::$app->session->hasFlash('pageMsg')):
    $data = Yii::$app->session->getFlash('pageMsg');
?>
    weui.topTips('<?=$data['content'];?>', {
        className:"toptips-<?=$data['type']?>"
    });
<?php   
endif;
$this->endBlock();
$this->registerJs($this->blocks['pageSript']);
?>
</script>
