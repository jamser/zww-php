<?php

/* @var $this \yii\web\View */
/* @var $logs \common\models\user\BlanceChangeLog[] */
use yii\helpers\Url;
use yii\helpers\Html;
$this->params['bodyCssClass'] = 'gray-bg';

$this->title = '余额记录';
?>
<div class="wallet-blance-log">
    <h2 class="margin20"><?=$this->title?></h2>
    <?php foreach($logs as $log) { ?>
    <div class="weui-form-preview mt20">
        <div class="weui-form-preview__hd">
            <div class="weui-form-preview__item">
                <label class="weui-form-preview__label">变动金额</label>
                <em class="weui-form-preview__value"><?=$log->change_value>=0?'+':'-'?> ¥ <?=sprintf('%0.2f',$log->change_value/100)?></em>
            </div>
        </div>
        <div class="weui-form-preview__bd">
            <div class="weui-form-preview__item">
                <label class="weui-form-preview__label">备注</label>
                <span class="weui-form-preview__value"><?=Html::encode($log->remark)?></span>
            </div>
            <div class="weui-form-preview__item">
                <label class="weui-form-preview__label">时间</label>
                <span class="weui-form-preview__value"><?=date('Y-m-d H:i:s',$log->created_at)?></span>
            </div>
        </div>
    </div>
    <?php } ?>
    
    <?php if(!$logs) {?>
        <div class="weui-loadmore weui-loadmore_line">
            <span class="weui-loadmore__tips">暂无数据</span>
        </div>
    <?php } else if(count($log)==20) {?>
        
    <?php }?>
</div>
