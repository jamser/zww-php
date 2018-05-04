<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;$this->title = '修改信息';
$this->params['breadcrumbs'][] = $this->title;
?>

<html>
<meta charset="utf-8">

<body>

<div  class='doll-info-form' >
    <?php $form = ActiveForm::begin(["options" => ["enctype" => "multipart/form-data"]]); ?>

    <?= $form->field($model, 'order_number')->textInput(['value'=> $orderInfo['order_number']])  ?>

    <?= $form->field($model, 'order_date')->textInput(['value'=> $orderInfo['order_date']])  ?>

    <?= $form->field($model, 'member_id')->textInput(['value'=> $orderInfo['member_id']])  ?>

    <?= $form->field($model, 'status')->textInput(['value'=> $orderInfo['status']])  ?>

    <?= $form->field($model, 'stock_valid_date')->textInput(['value'=> $orderInfo['stock_valid_date']])  ?>

    <?= $form->field($model, 'deliver_date')->textInput(['value'=> $orderInfo['deliver_date']])  ?>

    <?= $form->field($model, 'deliver_method')->textInput(['value'=> $orderInfo['deliver_method']])  ?>

    <?= $form->field($model, 'deliver_number')->textInput(['value'=> $orderInfo['deliver_number']])  ?>

    <?= $form->field($model, 'deliver_amount')->textInput(['value'=> $orderInfo['deliver_amount']])  ?>

    <?= $form->field($model, 'deliver_coins')->textInput(['value'=> $orderInfo['deliver_coins']])  ?>

    <?= $form->field($model, 'dollitemids')->textInput(['value'=> $orderInfo['dollitemids']])  ?>

    <?= $form->field($model, 'dolls_info')->textInput(['value'=> $orderInfo['dolls_info']])  ?>

    <?= $form->field($model, 'receiver_name')->textInput(['value'=> $orderInfo['receiver_name']])  ?>

    <?= $form->field($model, 'receiver_phone')->textInput(['value'=> $orderInfo['receiver_phone']])  ?>

    <?= $form->field($model, 'province')->textInput(['value'=> $orderInfo['province']])  ?>

    <?= $form->field($model, 'city')->textInput(['value'=> $orderInfo['city']])  ?>

    <?= $form->field($model, 'county')->textInput(['value'=> $orderInfo['county']])  ?>

    <?= $form->field($model, 'street')->textInput(['value'=> $orderInfo['street']])  ?>

    <?= $form->field($model, 'comment')->textInput(['value'=> $orderInfo['comment']])  ?>

    <?= $form->field($model, 'created_date')->textInput(['value'=> $orderInfo['created_date']])  ?>

    <?= $form->field($model, 'modified_date')->textInput(['value'=> $orderInfo['modified_date']])  ?>

    <?= $form->field($model, 'modified_by')->textInput(['value'=> $orderInfo['modified_by']])  ?>

    <?= Html::submitButton('修改', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
</div>
</body>
</html>