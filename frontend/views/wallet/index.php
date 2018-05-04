<?php


/* @var $this \yii\web\View */
/* @var $wallet \common\models\user\Wallet */

use yii\helpers\Url;

$this->title = '钱包';
?>
<div>
    <div class="clearfix">
        <a href="<?=Url::to(['/wallet/blance'])?>">
            <div class="left">余额</div>
            <div class="left"><?=$wallet->blance/100?> 元</div>
        </a>
    </div>
    <div>
        <a href="<?=Url::to(['/wallet/virtual-money'])?>">
            <div class="left"><?=$virtualMoneyName?></div>
            <div class="left"><?=$wallet->virtual_money?> </div>
        </a>
    </div>
</div>
