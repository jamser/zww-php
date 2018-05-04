<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\PcallOrder;
use common\models\PcallCaller;
use frontend\models\CallerDispatchForm;
use common\models\PcallUser;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $model common\models\CallerDispatchForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '分配起床达人';
?>

<div class="pcall-order-form" style="padding-top:60px;">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'order_id')->textInput();?>
    
    <?= $form->field($model, 'caller_id')->textInput(['id'=>'caller_id']);?>
    
    <div class="form-group" >
        <label class="control-label">达人列表</label>
        <div id="callers">
            <table class="table">
                <tr>
                    <td>ID</td>
                    <td>昵称</td>
                    <td>手机</td>
                    <td>性别</td>
                    <td>预约时间</td>
                    <td>操作</td>
                </tr>
                
                <?php foreach($callers as $caller):
                    /* @var $caller PcallCaller */?>
                <tr data-id="<?=$caller->user_id?>">
                    <td><?=$caller->user_id?></td>
                    <td><?=Html::encode($caller->callUser->nickname)?></td>
                    <td><?=$caller->callUser->phone?></td>
                    <td><?=  PcallUser::getGenderStr($caller->callUser->gender==1)?></td>
                    <td><?=Html::encode($caller->service_time)?></td>
                    <td><button class="btn-select">选择</button></td>
                </tr>
                <?php endforeach;?>
            </table>
            
        </div>
        <?=
        LinkPager::widget([
              'pagination' => $pagination,
            ]);
        ?>
    </div>
    
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'weui-btn weui-btn_primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
<?php $this->beginBlock('pageScript');?>
    $('#callers').on('click','.btn-select',function(e){
        e.preventDefault();
        var userId = $(this).parents('tr').data('id');
        $('#caller_id').val(userId);
    })
<?php $this->endBlock();
$this->registerJs($this->blocks['pageScript']);
?>
</script>