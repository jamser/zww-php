<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $title string */
/* @var $message string */
/* @var $buttons array */

$this->title = $title;
?>
<div class="weui-msg">
    <div class="weui-msg__icon-area"><i class="weui-icon-warn weui-icon_msg"></i></div>
    <div class="weui-msg__text-area">
        <h2 class="weui-msg__title"><?=Html::encode($title)?></h2>
        <p class="weui-msg__desc"><?=Html::encode($message);?></p>
    </div>
    <div class="weui-msg__opr-area">
        <p class="weui-btn-area">
            <?php if($buttons):?>
                <?php foreach($buttons as $key=>$button):?>
                <a href="<?=$button['url']?>" class="weui-btn <?php if($key===0) {echo 'weui-btn_primary';} 
                    else {echo 'weui-btn_default';} ?>">
                    <?=$button['text']?>
                </a>
                <?php endforeach;?>
            <?php endif;?>
        </p>
    </div>
</div>
