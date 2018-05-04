<?php

use yii\helpers\Html;
use common\models\call\Caller;
use common\models\Setting;

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $caller common\models\call\Caller */

$this->title = '浏览 '.Setting::getValueByKey('callerName');
//$this->render('/base/header',['return'=>false])
$this->params['bodyCssClass'] = 'gray-bg';
?>

<div class="caller-explore page panel">
    <div class="weui-panel weui-panel_access">
        <div class="weui-panel__hd">浏览<?=Setting::getValueByKey('callerName')?></div>
        <div class="weui-panel__bd">
            <?php foreach($callers  as $caller): ?>
            <a class="weui-media-box weui-media-box_appmsg" href="<?= \yii\helpers\Url::to(['/call/caller/view','id'=>$caller->id])?>">
                <div class="weui-media-box__hd">
                    <img class="weui-media-box__thumb" src="<?=$caller->user->getAvatar()?>" alt="">
                </div>
                <div class="weui-media-box__bd">
                    <h4 class="weui-media-box__title">
                        <?= Html::encode($caller->user->username)?>
                        <span class="icon <?=$caller->user->sex==1?'icon-venus':'icon-mars'?>"></span>
                    </h4>
                    <p class="weui-media-box__desc mt5">所在地区 <?= Html::encode($caller->user->getLocation())?></p>
                    <p class="weui-media-box__desc mt5">个性签名 <?= Html::encode($caller->user->about)?></p>
                    <p class="weui-media-box__desc mt5">服务时间 <?= Html::encode($caller->service_time)?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
            <div class="weui-panel__ft">
            <div class="weui-flex weui-panel_pager">
                <?php if($page>1):?>
                <div class="weui-flex__item weui-panel_pager_btn">
                    <a href="<?= \yii\helpers\Url::to(['/call/caller/explore','page'=>$prePage])?>" class="">上一页</a>
                </div>
                <?php endif;?>
                <?php if($pageCount>$nextPage):?>
                <div class="weui-flex__item weui-panel_pager_btn">
                    <a href="<?= \yii\helpers\Url::to(['/call/caller/explore','page'=>$nextPage])?>" class="">下一页</a>
                </div>
                <?php endif;?>
            </div>
             
        </div>
    </div>
</div>
