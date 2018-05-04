<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $wallet common\models\user\Wallet */
use yii\helpers\Html;
use common\models\Setting;
use yii\helpers\Url;

$this->params['bodyCssClass'] = 'gray-bg';
$this->title = '用户中心';

?>
<div class="user-index">
    
    <div class="header-group clearfix">
        <div class="avatar left">
            <img src="<?=$user->getAvatar(120);?>" width="80" height="80"/>
        </div>
        <div class="profile">
            <div class="nickname"><?=Html::encode($user->username)?></div>
            <div class="btns">
                <a href="<?=Url::to(['user/update'])?>" class="weui-btn weui-btn_mini weui-btn_primary mr10">编辑资料</a>
            </div>
        </div>
    </div>
    
    <div class="weui-panel user-index-wallet">
        <div class="weui-panel__bd">
            <div class="weui-media-box weui-media-box_small-appmsg">
                <div class="weui-cells">
                    <a class="weui-cell weui-cell_access" href="/call/order/user">
                        <div class="weui-cell__hd"><span class="icon icon-list-ul"></span></div>
                        <div class="weui-cell__bd weui-cell_primary">
                            <p>
                                订单
                            </p>
                        </div>
                        <span class="weui-cell__ft"></span>
                    </a>
                    <a class="weui-cell weui-cell_access" href="javascript:;">
                        <div class="weui-cell__hd"><span class="icon icon-money2"></span></div>
                        <div class="weui-cell__bd weui-cell_primary">
                            <p><?=Setting::getValueByKey('virtualMoneyName')?> <?=$wallet->virtual_money?$wallet->virtual_money:''?></p>
                        </div>
                        <span class="weui-cell__ft"></span>
                    </a>
                    <?php if($isCaller):?>
                    <a class="weui-cell weui-cell_access" href="javascript:;">
                        <div class="weui-cell__hd"><span class="icon icon-credit-card"></span></div>
                        <div class="weui-cell__bd weui-cell_primary">
                            <p>账户余额 <?=$wallet->blance?$wallet->blance/100:''?></p>
                        </div>
                        <span class="weui-cell__ft"></span>
                    </a>
                    <?php endif;?>
                    
                </div>
            </div>
        </div>
    </div>
    
    <div class="weui-panel user-index-wallet">
        <div class="weui-panel__bd">
            <div class="weui-media-box weui-media-box_small-appmsg">
                <div class="weui-cells">
                    <a class="weui-cell weui-cell_access" href="<?=Url::to(['user/account'])?>">
                        <div class="weui-cell__hd"><span class="icon icon-cogs"></span></div>
                        <div class="weui-cell__bd weui-cell_primary">
                            <p>账号设置</p>
                        </div>
                        <span class="weui-cell__ft">绑定手机号</span>
                    </a>
                    <a class="weui-cell weui-cell_access" href="javascript:;">
                        <div class="weui-cell__hd"><span class="icon icon-file-text-o"></span></div>
                        <div class="weui-cell__bd weui-cell_primary">
                            <p>使用帮助</p>
                        </div>
                        <span class="weui-cell__ft"></span>
                    </a>
                    <a class="weui-cell weui-cell_access" href="javascript:;">
                        <div class="weui-cell__hd"><span class="icon icon-comments-o"></span></div>
                        <div class="weui-cell__bd weui-cell_primary">
                            <p>意见反馈</p>
                        </div>
                        <span class="weui-cell__ft"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
</div>
