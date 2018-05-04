<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use frontend\assets\CallAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\PcallCaller */

CallAsset::register($this);
$this->title = $model->user_id==Yii::$app->user->id ? '个人详情' : $model->callUser->nickname;
?>

<?=$this->render('/base/header');?>

<div class="pcall-main">
    
    <div class="pcall-caller-profile">
        <div class="pcall-caller-detail">
            <div class="form-group">
                <label>昵称</label>
                <?=Html::encode($model->callUser->nickname);?>
            </div>
            <div class="form-group">
                <label>性别</label>
                <?php
                if($model->callUser->gender==1) {
                    echo '男';
                } else if($model->callUser->gender==2) {
                    echo '女';
                }
                ?>
                
            </div>
            
            <!--
            <div class="form-group">
                <label>年龄</label>
                <?=$model->callUser->getAge()?>岁
            </div>
            
            <div class="form-group">
                <label>坐标</label>
                <?=Html::encode($model->callUser->getLocation())?>
            </div>
-->
            <div class="form-group">
                <label>一句话介绍</label>
                <?=$model->description;?>
            </div>
        </div>
    </div>
    
    <div class="pcall-caller-imgs margin10">
        <?php
        $covers = json_decode($model->covers,true);
        foreach($covers as $cover) { ?>
        <div class="mt10 mb10">
            <img src="<?=$cover['url']?>" width="100%"/>
        </div>
        <?php
        }
        ?>
    </div>
    

</div>

<footer class="pcall-footer">
    <div class="margin10">
        <?php if($model->user_id==Yii::$app->user->id):?>
            <a href="<?=Url::to(['/call/caller/update','id'=>$model->user_id])?>" class="weui-btn weui-btn_primary">更新</a>
        <?php else:?>
            <a href="javascript:alert('即将开放');" class="weui-btn weui-btn_primary">立即预约</a>
        <?php endif;?>
    </div>
</footer>
