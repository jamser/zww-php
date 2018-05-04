<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use common\models\finance\WithdrawApply;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\WithdrawSearch */
/* @var $pages yii\web\Pagination */

$this->title = '提现申请列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withdraw-apply-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <div id="w1" class="grid-view">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th><input type="checkbox" /> 全选</th>
                    <th>ID</th>
                    <th>用户</th>
                    <th>真实姓名</th>
                    <th>手机</th>
                    <th>微信ID</th>
                    <th>金额</th>
                    <th>状态</th>
                    <th>申请时间</th>
                    <th>支付时间</th>
                    <th>交易流水号</th>
                    <th class="action-column">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach($rows as $key=>$row):
                ?>
                <tr data-key="<?=$key?>">
                    <td><input type="checkbox" /></td>
                    <td><?=$row['id']?></td>
                    <td>
                        #<?=$row['user_id']?>
                        <?=$row['username']?>
                    </td>
                    <td><?=$row['true_name']?></td>
                    <td><?=$row['phone']?></td>
                    <td><?=$row['open_id']?></td>
                    <td><?=sprintf('%0.2f',$row['amount']/100)?>元</td>
                    <td><?=WithdrawApply::STATUS_LIST[$row['status']]?></td>
                    <td><?=date('Y-m-d H:i:s', $row['created_at'])?></td>
                    <td><?=$row['pay_time']?date('Y-m-d H:i:s', $row['pay_time']):'无'?></td>
                    <td><?=$row['out_trade_no']?></td>
                    <td>
                        <a href="/finance/user-withdraw-view?id=<?=$row['id']?>">查看</a> 

                        <?php if($row['status']==WithdrawApply::STATUS_DEFAULT):?>
                        <a href="/finance/review?id=<?=$row['id']?>">审核</a> 
                        <?php endif;?>

                        <?php if($row['status']==WithdrawApply::STATUS_REVIEW_PASS):?>
                        <a href="/finance/pay?id=<?=$row['id']?>">打款</a> 
                        <?php endif;?>
                    </td>
                </tr>
                <?php
                endforeach;
                ?>
            </tbody>
        </table>
        
    </div>
    <!--
    <div>
        <?= Html::a('批量审核', ['batch-review'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('批量打款', ['batch-pay'], ['class' => 'btn btn-default']) ?>
    </div>-->
    <?= LinkPager::widget([
        'pagination' => $pages,
    ])?>
</div>
