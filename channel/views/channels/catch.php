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
        <div class="row-fluid table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th class="span3 sortable">
                        <span class="line"></span>用户id
                    </th>
                    <th class="span3 sortable">
                        <span class="line"></span>用户名
                    </th>
                    <th class="span3 sortable">
                        <span class="line"></span>充值规则
                    </th>
                    <th class="span2 sortable">
                        <span class="line"></span>充值金额（￥）
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>充值时间
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($Info as $k=>$v) {
                    /* @var $model \common\models\doll\Statistic */
                    ?>
                    <tr class="first">
                        <td>
                            <?php echo $v['member_id'] ?>
                        </td>
                        <td>
                            <?php echo $v['member_name'] ?>
                        </td>
                        <td>
                            <?php echo $v['charge_name'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['price'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['create_date'] ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </section>
</section>
