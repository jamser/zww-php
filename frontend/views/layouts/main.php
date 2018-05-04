<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use common\widgets\Alert;
use frontend\assets\MobileAsset;
MobileAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="mobile-html">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
    <?= Html::csrfMetaTags() ?>
    <link rel=”icon” href="<?=Yii::getAlias('@staticAssetUrl')?>/imgs/favicon.ico" mce_href="<?=Yii::getAlias('@staticAssetUrl')?>/imgs/favicon.ico" type="image/x-icon">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?=$this->render('@frontend/views/base/bdTongji')?>
</head>
<body class="mobile-body <?=isset($this->params['bodyCssClass']) ? $this->params['bodyCssClass'] : ''?>">
<?php $this->beginBody() ?>
    <div class="wrap" id="container_wrap">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
    <?php 
    if(!empty($this->blocks['footer'])):
        echo $this->blocks['footer'];
    endif;?>
    <div id="loading_toast" class="weui_loading_toast" style="display: none;">
        <div class="weui_mask_transparent"></div>
        <div class="weui_toast">
            <div class="weui_loading">
                <div class="weui_loading_leaf weui_loading_leaf_0"></div>
                <div class="weui_loading_leaf weui_loading_leaf_1"></div>
                <div class="weui_loading_leaf weui_loading_leaf_2"></div>
                <div class="weui_loading_leaf weui_loading_leaf_3"></div>
                <div class="weui_loading_leaf weui_loading_leaf_4"></div>
                <div class="weui_loading_leaf weui_loading_leaf_5"></div>
                <div class="weui_loading_leaf weui_loading_leaf_6"></div>
                <div class="weui_loading_leaf weui_loading_leaf_7"></div>
                <div class="weui_loading_leaf weui_loading_leaf_8"></div>
                <div class="weui_loading_leaf weui_loading_leaf_9"></div>
                <div class="weui_loading_leaf weui_loading_leaf_10"></div>
                <div class="weui_loading_leaf weui_loading_leaf_11"></div>
            </div>
            <p class="weui_toast_content">数据加载中</p>
        </div>
    </div>
    <div class="weui_dialog_confirm hidden" id="login_alert">
        <div class="weui_mask"></div>
        <div class="weui_dialog">
            <div class="weui_dialog_hd"><strong class="weui_dialog_title">提示</strong></div>
            <div class="weui_dialog_bd">该操作需要登录</div>
            <div class="weui_dialog_ft">
                <a href="javascript:;" class="weui_btn_dialog default" onclick="$('#login_alert').addClass('hidden')">取消</a>
                <a href="/user/auth/login?returnUrl=<?=  urlencode(\yii\helpers\Url::current())?>" class="weui_btn_dialog primary">前往登录</a>
            </div>
        </div>
    </div>
<?php
if(Yii::$app->hasModule('debug')) {
    Yii::$app->getModule('debug')->allowedIPs = [];
}
$this->endBody();?>
</body>
</html>
<?php $this->endPage() ?>
