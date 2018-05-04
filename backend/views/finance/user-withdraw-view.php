<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\user\Account;
use common\models\Setting;
use common\models\call\Caller;
use common\models\finance\WithdrawApply;

/* @var $this yii\web\View */
/* @var $model common\models\finance\WithdrawApply */
/* @var $user common\models\User */
/* @var $caller common\models\call\Caller */
/* @var $wallet common\models\user\Wallet */

$this->title = '提现申请查看';
$this->params['breadcrumbs'][] = ['label' => '提现申请列表', 'url' => ['user-withdraw-list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withdraw-apply-review">
    <table id="w0" class="table table-striped table-bordered detail-view">
        <tbody>
            <tr>
                <th>用户信息</th>
                <td> 
                    ID <?=$user->id?> 昵称 <?=Html::encode($user->username)?> <br/>
                    真实姓名 <?=Html::encode($user->true_name)?> <br/>
                    注册时间 <?=date('Y-m-d H:i:s',$user->created_at)?>
                </td>
            </tr>
            <tr>
                <th>账户信息</th>
                <td>
                    手机号 <?=Html::encode($phone)?><br/>
                    微信UnionId <?=Html::encode($unionId)?><br/>
                    微信OpenId <?=Html::encode($openId)?>
                </td>
            </tr>
            <tr>
                <th><?=Html::encode(Setting::getValueByKey('callerName'))?>信息</th>
                <td><?php if($caller):?>
                        申请时间 <?=date('Y-m-d H:i:s',$caller->created_at)?> <br/>
                        状态 <?=isset(Caller::$status_list[$caller->status])?Caller::$status_list[$caller->status]:'未知状态 '.$caller->status?><br/>
                    <?php else: ?>
                        未申请
                    <?php endif;?>
                </td>
            </tr>
            <tr>
                <th>订单信息</th>
                <td>
                    已收到 <?=$payOrderCount?> 个支付订单
                </td>
            </tr>
            <tr>
                <th>用户钱包</th>
                <td>
                    余额 <?=sprintf('%0.2f',$wallet->blance/100)?>元<br/>
                    收入 <?=sprintf('%0.2f',$wallet->income/100)?>元<br/>
                    已提现 <?=sprintf('%0.2f',$wallet->withdraw/100)?>元<br/>
                    可提现 <?=sprintf('%0.2f',$wallet->can_withdraw/100)?>元<br/>
                    已申请提现 <?=sprintf('%0.2f',$applyAmount/100)?>元
                </td>
            </tr>
            
            <tr>
                <th>状态</th>
                <td>
                    <?=isset(WithdrawApply::STATUS_LIST[$model->status]) ? WithdrawApply::STATUS_LIST[$model->status] : '未知'?>
                </td>
            </tr>
            <tr>
                <th>日志</th>
                <td>
                    <?php foreach($logs as $log):?>
                        <?=date("Y-m-d H:i:s",$log->created_at)." 管理员 {$log->admin_id} 进行审核 ; 备注: ".Html::encode($log->remark)?> <br>
                    <?php endforeach; ?>
                </td>
            </tr>
        </tbody>
    </table>
    <div>
        <?php if($model->canReview()):?>
        <?= Html::a('审核', ['user-withdraw-review','id'=>$model->id], ['class' => 'btn btn-default']) ?>
        <?php endif; ?>
        <?php if($model->canPay()):?>
        <?= Html::a('打款', ['user-withdraw-pay','id'=>$model->id], ['class' => 'btn btn-default']) ?>
        <?php endif; ?>
    </div>
</div>
