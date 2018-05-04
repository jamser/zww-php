<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
?>
<header class="pcall-header">
    <?php if(!empty($return)):?>
    <a href="#" class="nav-left"><span class="icon icon-angle-left"></span> 返回</a>
    <?php endif;?>
    <span class="title">
        <?=Html::encode($this->title);?>
    </span>
</header>
