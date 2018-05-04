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
            <?=$this->title = '充值记录';?>
        </header>
        <div class="panel-body">
            <div class="adv-table editable-table ">
                <?php
                echo "<div class='col-lg-12'>
                    <table class='table table-bordered table-striped table-hover'>
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>用户id</th>
                            <th>用户名</th>
                            <th>充值规则</th>
                            <th>充值金额（￥）</th>
                            <th>充值时间</th>
                        </tr>
                        </thead>
                        <tbody>";?>
                <?php
                $id = $Info['id'];
                $user_id = $Info['member_id'];
                $member_name = $Info['member_name'];
                $charge_name = $Info['charge_name'];
                $price = $Info['price'];
                $create_date= $Info['create_date'];

                echo "<tr>";
                echo "<td> $id </td>";
                echo "<td> $user_id </td>";
                echo "<td> $member_name </td>";
                echo "<td> $charge_name </td>";
                echo "<td> $price </td>";
                echo "<td> $create_date </td>";
                echo "</tr>";
                echo "</tbody>
                        </table>
                </div>
            </div>";
                ?>

    </section>
</section>
