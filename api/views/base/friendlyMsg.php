<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $title string */
/* @var $message string */
/* @var $buttons array */

$this->title = $title;
?>

<div class="friendly-msg-page">
    <div class="error-header clearfix">
        <div class="error-smiley">
            <!--<img src="" width="40" height="40" />-->
        </div>
        <div class="error-title">
            <?=Html::encode($title)?>
        </div>
    </div>
    <div class="message">
        <?=Html::encode($message);?>
    </div>
    <?php if($buttons):?>
    <div class="buttons clearfix">
        <?php foreach($buttons as $button):?>
        <a href="<?=$button['url']?>" class="btn"><?=$button['text']?></a>
        <?php endforeach;?>
    </div>
    <?php endif;?>
</div>
