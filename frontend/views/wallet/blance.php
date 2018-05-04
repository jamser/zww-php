<?php


/* @var $this \yii\web\View */
/* @var $wallet \common\models\user\Wallet */

use yii\helpers\Url;

$this->params['bodyCssClass'] = 'gray-bg';
$this->title = '钱包余额';
?>
<div class="wallet-blance">
    <h2 class="margin20 header">
        <span class="icon icon-cash"></span>
    </h2>
    
    <div class="margin20">
        <h2 class="walletblance text-center">余额 <?=sprintf('%0.2f',$wallet->blance/100)?> 元</h2>
        <p class="text-center wallet-desc">收入<?=sprintf('%0.2f',$wallet->blance/100)?> 元
            可提现<?=sprintf('%0.2f',$wallet->can_withdraw/100)?>元
            已提现<?=sprintf('%0.2f',$wallet->withdraw/100)?> 元
        </p>
        
        
        <div class="clearfix mt20">
            <a href="<?=$wallet->can_withdraw>0 ? Url::to(['withdraw']) : 'javascript:;'?> " class="weui-btn weui-btn_primary <?=$wallet->can_withdraw>0?'':'weui-btn_disabled'?>">提现</a>
        </div>
    </div>

    
</div>


<div class="weui-footer">
    <p class="weui-footer__links">
        <a href="<?= Url::to(['blance-log'])?>" class="weui-footer__link">钱包记录</a>
    </p>
</div>