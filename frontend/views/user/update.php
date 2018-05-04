<?php


/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $form frontend\models\UserUpdateForm */

use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Setting;

$this->params['bodyCssClass'] = 'gray-bg';
$this->title = '编辑资料';

?>
<div class="user-update">
    <form action="<?=Url::to(['/user/update'])?>" method="POST">
        <input type="hidden" name="_csrf" value="<?=Yii::$app->getRequest()->csrfToken;?>"/>
        <div class="form-group">
            <div class="weui-cell" id="uploader">
                <div class="weui-cell__hd"><label class="weui-label">头像</label></div>
                <div class="weui-cell__bd">
                    <div class="avatar" id="avatar">
                        <img src="<?=$user->getAvatar(80);?>" width="80" height="80"/>
                    </div>
                    <input id="uploaderInput" class="weui-uploader__input" type="file" accept="image/*" capture="camera" multiple="" />
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="weui-cell<?=$form->hasErrors('username')?' weui-cell_warn':''?>">
                <div class="weui-cell__hd"><label class="weui-label">昵称</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" placeholder="请输入昵称" name="data[username]" value="<?=Html::encode($form->username)?>">
                </div>
            </div>
            <div class="weui-cells__tips red"><?=$form->getFirstError('username')?></div>
            <div class="weui-cell weui-cell_select weui-cell_select-after<?=$form->hasErrors('sex')?' weui-cell_warn':''?>">
                <div class="weui-cell__hd">
                    <label for="data[sex]" class="weui-label">性别</label>
                </div>
                <div class="weui-cell__bd">
                    <select class="weui-select"  name="data[sex]">
                        <option value="0">请选择</option>
                        <option value="1" <?=$form->sex==1?'selected':''?>>男</option>
                        <option value="2" <?=$form->sex==2?'selected':''?>>女</option>
                    </select>
                </div>
            </div>
            <div class="weui-cells__tips red"><?=$form->getFirstError('sex')?></div>
            <div class="weui-cell<?=$form->hasErrors('birthday')?' weui-cell_warn':''?>">
                <div class="weui-cell__hd"><label for="" class="weui-label">生日</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="date" value="<?=$form->birthday?>" name="data[birthday]">
                </div>
            </div>
            <div class="weui-cells__tips red"><?=$form->getFirstError('birthday')?></div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label for="" class="weui-label">位置</label></div>
                <div class="weui-cell__bd">
                    上海 杨浦区
                </div>
            </div>
            <div class="weui-cell<?=$form->hasErrors('about')?' weui-cell_warn':''?>">
                <div class="weui-cell__bd">
                    <label for="" class="weui-label">个人签名</label>
                    <textarea class="weui-textarea" placeholder="" rows="2" value="<?=$form->about?>" name="data[about]"><?=$form->about?></textarea>
                    <div class="weui-textarea-counter"><span>0</span>/200</div>
                </div>
            </div>
            <div class="weui-cells__tips red"><?=$form->getFirstError('about')?></div>
        </div>

        <div class="weui-btn-area">
            <button type="submit" class="weui-btn weui-btn_primary" >确定</button>
        </div>
    </form>
</div>

<script>
<?php $this->beginBlock('pageSript'); ?>
/* 图片自动上传 */
var uploadCount = 0, uploadList = [];
weui.uploader('#uploader', {
    url: '//<?=Yii::getAlias('@frontendHost')?>/api/file/upload-avatar',
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
        if (files.length > 5) { // 防止一下子选中过多文件
            weui.alert('最多只能上传5张图片，请重新选择');
            return false;
        }
        if (uploadCount + 1 > 5) {
            weui.alert('最多只能上传5张图片');
            return false;
        }

        ++uploadCount;
    },
    onQueued: function(){
        uploadList.push(this);
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
        } else {
            alert(ret.msg);
        }
    },
    onError: function(err){
        console.log(this, err);
    }
});

<?php
if(Yii::$app->session->hasFlash('pageMsg')):
    $data = Yii::$app->session->getFlash('pageMsg');
?>
    FlashMsg.<?=$data['type']?>('<?=$data['content'];?>');
<?php   
endif;
$this->endBlock();
$this->registerJs($this->blocks['pageSript']);
?>
</script>
