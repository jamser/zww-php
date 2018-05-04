<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\WechatPublicAccountsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>



<section class="wrapper site-min-height">
    <!-- page start-->
    <section class="panel">
        <header class="panel-heading">
            <?=$this->title = '订单详情';?>
        </header>
        <div class="panel-body">
            <div class="adv-table editable-table ">
                <?php
                    echo "<div class='col-lg-12'>
                    <table class='table table-bordered table-striped table-hover'>
                        <thead>
                        <tr>
                            <th>订单号</th>
                            <th>订单生成日期</th>
                            <th>发货状态</th>
                            <th>发货日期</th>
                            <th>发货方式</th>
                            <th>快递单号</th>
                            <th>邮费</th>
                            <th>娃娃id集合</th>
                            <th>发货娃娃信息</th>
                            <th>收件人姓名</th>
                            <th>收件人手机号</th>
                            <th>收件人地址</th>
                        </tr>
                        </thead>
                        <tbody>";?>
                    <?php
                        $order_number = $orderInfo['order_number'];
                        $order_date = $orderInfo['order_date'];
                        $status = $orderInfo['status'];
                        $created_date = $orderInfo['created_date'];
                        $deliver_method = $orderInfo['deliver_method'];
                        $deliver_number = $orderInfo['deliver_number'];
                        $deliver_coins = $orderInfo['deliver_coins'];
                        $dollitemids = $orderInfo['dollitemids'];
                        $dolls_info = $orderInfo['dolls_info'];
                        $receiver_name = $orderInfo['receiver_name'];
                        $receiver_phone = $orderInfo['receiver_phone'];
                        echo "<tr>";
                        echo "<td> $order_number </td>";
                        echo "<td> $order_date </td>";
                        echo "<td> $status </td>";
                        echo "<td> $created_date </td>";
                        echo "<td> $deliver_method </td>";
                        echo "<td> $deliver_number </td>";
                        echo "<td> $deliver_coins </td>";
                        echo "<td> $dollitemids </td>";
                        echo "<td> $dolls_info </td>";
                        echo "<td> $receiver_name </td>";
                        echo "<td> $receiver_phone </td>";
                        echo "<td> $address </td>";
                        echo "</tr>";
                    echo "</tbody>
                        </table>
                </div>
            </div>";
                ?>

    </section>
</section>
