<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\PcallOrder;

/* @var $this yii\web\View */
/* @var $model common\models\PcallOrder */
/* @var $call_user common\models\PcallUser */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pcall-order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($call_user, 'nickname')->textInput() ?>
    
    <?= $form->field($call_user, 'gender')->dropDownList([null=>'请设置',1=>'男',2=>'女'],$call_user->canSetGender() ? [] : ['disabled'=>true]) ?>
    
    <?= $form->field($call_user, 'phone')->textInput() ?>
    
    
    <div id="sms_code_group" class="form-group <?php if($call_user->hasErrors('sms_code')){echo 'has-error';}
        if($call_user->getOldAttribute('phone')==$call_user->phone) echo 'hidden'; ?>">
        <label class="control-label">验证码</label>
        <div class="form-inline">
            <input type="text" id="pcalluser-sms_code" class="form-control" name="PcallUser[sms_code]" value="<?=$call_user->sms_code?>"/>
            <button class="btn btn-default" id="btn_send_smscode" type="button">发送验证码</button>
        </div>
        <div class="help-block">
            <?php 
            if($call_user->hasErrors('sms_code')) {
                echo $call_user->getFirstError('sms_code');
            }
            ?>
        </div>
    </div>
    
    <?= $form->field($model, 'booking_date')->dropDownList([null=>'请选择']+PcallOrder::getBookingDates()) ?>

    
    <div class="form-group <?php echo $model->hasErrors('booking_time_start') || $model->hasErrors('booking_time_end') ? 'has-error' : '' ?>">
        <label class="control-label">预约时间段</label>
        <div class="form-inline">
            <div class="form-group">
                <label class="sr-only" for="pcallorder-booking_time_start">开始</label>
                <select id="pcallorder-booking_time_start" class="form-control" name="PcallOrder[booking_time_start]">
                    <option>请选择</option>
                    <?php 
                    $options = PcallOrder::getTimeOptions();
                    foreach($options as $key=>$val): ?>
                    <option value="<?=$key?>"  <?php if($key==$model->booking_time_start) echo 'selected' ?>><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="" for="pcallorder-booking_time_end">到</label>
                <select id="pcallorder-booking_time_end" class="form-control" name="PcallOrder[booking_time_end]">
                    <option>请选择</option>
                    <?php 
                    foreach($options as $key=>$val): ?>
                    <option value="<?=$key?>" <?php if($key==$model->booking_time_end) echo 'selected' ?>><?=$val?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
            
        <div class="help-block">
            <?php 
            if($model->hasErrors('booking_time_start')) {
                echo $model->getFirstError('booking_time_start');
            } else if($model->hasErrors('booking_time_end')) {
                echo $model->getFirstError('booking_time_end');
            }
            ?>
        </div>
    </div>
    

    <?= $form->field($model, 'remark')->textArea() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '立即预约' : '更新', ['class' => 'weui-btn weui-btn_primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
<?php $this->beginBlock('pageScripts');?>
var old_phone = '<?=$call_user->getOldAttribute('phone')?>';
$('#pcalluser-phone').on('change', function(e){
    if($(this).val().replace(/(^\s*)|(\s*$)/g, "")!==old_phone) {
        $('#sms_code_group').removeClass('hidden');
    } else {
        $('#sms_code_group').addClass('hidden');
    }
});
<?php $this->endBlock();
$this->registerJs($this->blocks['pageScripts']);?>
</script>