<?php

/* @var $this \yii\web\View */
/* @var $logs \common\models\user\VirtualMoneyChangeLog[] */

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = Html::encode($virtualMoneyName).'记录';
?>
<div>
    <?php foreach($logs as $log) { ?>
    <div class="margin10">
        <div class="clearfix">
            <div class="left">
                <?=$log->change_value?>
            </div>
            <div class="right">
                <?=date('Y-m-d H:i:s',$log->created_at)?>
            </div>
        </div>
        <div class="mt10">
            <?=Html::encode($log->remark)?>
        </div>
        
    </div>
    <?php } ?>
    
    <?php if(!$logs) {?>
        暂无记录
    <?php } else if(coutn($log)==20) {?>
        <a href="#">加载更多</a>
    <?php }?>
</div>
