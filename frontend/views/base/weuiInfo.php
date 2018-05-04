<div class="page msg_success js_show">
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title"><?=$title?></h2>
            <p class="weui-msg__desc"><?=$msg?></p>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                <?php 
                if(!empty($buttons)) {
                    $isMain = true;
                    foreach($buttons as $button):?>
                        <a href="<?=$buttonMain['url']?>" class="weui-btn <?=$isMain?'weui-btn_primary':'weui-btn_default'?>"><?=$buttonMain['text']?></a>
                    <?php 
                    $isMain = false;
                    endforeach;
                }
                ?>
            </p>
        </div>
    </div>
</div>