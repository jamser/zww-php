<?php


/* @var $this \yii\web\View */
/* @var $wallet \common\models\user\Wallet */

use yii\helpers\Url;

$this->title = $virtualMoneyName;
?>
<div>
    <div class="clearfix">
        <div class="left"><?=$virtualMoneyName?></div>
        <div class="left"><?=$wallet->virtual_money?></div>
    </div>
    <div class="clearfix mt20">
        <a href="#" class="btn">
            100<?=$virtualMoneyName?> ￥10
        </a>
        <a href="#" class="btn">
            200<?=$virtualMoneyName?> ￥20
        </a>
        <a href="#" class="btn">
            500<?=$virtualMoneyName?> ￥50
        </a>
        <a href="#" class="btn">
            1000<?=$virtualMoneyName?> ￥100
        </a>
        <a href="#" class="btn">
            5000<?=$virtualMoneyName?> ￥500
        </a>
        <a href="#" class="btn">
            10000<?=$virtualMoneyName?> ￥1000
        </a>
    </div>
</div>
