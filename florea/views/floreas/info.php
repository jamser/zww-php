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
            <?=$this->title = '抓取记录';?>
        </header>
        <div class="panel-body">
            <div class="adv-table editable-table ">
                <?php
                echo "<div class='col-lg-12'>
                    <table class='table table-bordered table-striped table-hover'>
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>房间id</th>
                            <th>抓取时间</th>
                            <th>抓取状态</th>
                        </tr>
                        </thead>
                        <tbody>";?>
                <?php
                $id = $Info['id'];
                $doll_id = $Info['doll_id'];
                $catch_date = $Info['catch_date'];
                $catch_status = $Info['catch_status'];
                echo "<tr>";
                echo "<td> $id </td>";
                echo "<td> $doll_id </td>";
                echo "<td> $catch_date </td>";
                echo "<td> $catch_status </td>";
                echo "</tr>";
                echo "</tbody>
                        </table>
                </div>
            </div>";
                ?>

    </section>
</section>
