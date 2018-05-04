<?php
$this->title = '数据统计 概览';
?>
<div class="container-fluid">
    <div id="pad-wrapper" class="statistic-list">
        <div class="page-header">
            <a href="<?= \yii\helpers\Url::to(['/doll/statistic/overview','refresh'=>1])?>" >刷新今天数据</a>
        </div>
        <div class="head" style="width: auto;height: 20px;background-color:#dcdcdc ;font-size: 16px;color: #696969">
            新老用户充值（人数/订单/金额）    &nbsp&nbsp&nbsp&nbsp   各种房间 （游戏次数/抓中次数/概率）
        </div>
        <br/>
        
        <!-- Users table -->
        <div class="row-fluid table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th class="span3 sortable">
                            <span class="line"></span>日期
                        </th>
                        <th class="span3 sortable">
                            <span class="line"></span>总人数
                        </th>
                        <th class="span3 sortable">
                            <span class="line"></span>注册人数
                        </th>
                        <th class="span2 sortable">
                            <span class="line"></span>安卓注册人数
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>苹果注册人数
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>订单数
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>支付金额
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>钻石房支付金额
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>老用户充值
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>新用户充值
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>钻石房
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>普通房
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>练习+占卜房
                        </th>
                        <th class="span3 sortable align-right">
                            <span class="line"></span>玩游戏总次数
                        </th>
<!--                        <th class="span3 sortable align-right">-->
<!--                            <span class="line"></span>抓取成功次数-->
<!--                        </th>-->
<!--                        <th class="span3 sortable align-right">-->
<!--                            <span class="line"></span>钻石房抓取成功次数-->
<!--                        </th>-->
<!--                        <th class="span3 sortable align-right">-->
<!--                            <span class="line"></span>抓取成功率-->
<!--                        </th>-->
                        <th class="span3 sortable align-right">
                            <span class="line"></span>操作
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($models as $model) {
     /* @var $model \common\models\doll\Statistic */
                        ?>
                        <tr class="first">
                            <td>
                                <?php echo date('Y-m-d', $model->day) ?>
                            </td>
                            <td>
                                <?php echo $model->user_num ?>
                            </td>
                            <td>
                                <?php echo $model->registration_num ?>
                            </td>
                            <td class="align-right">
                                <?php echo $model->android_registration_num ?>
                            </td>
                            <td class="align-right">
                                <?php echo $model->ios_registration_num ?>
                            </td>
                            <td class="align-right">
                                <?php echo $model->charge_num ?>
                            </td>
                            <td class="align-right">
                                <?php echo $model->charge_amount ?>
                            </td>
                            <td class="align-right">
                                <?php echo $model->z_charge_amount ?>
                            </td>
                            <td class="align-right">
                                <?php echo $model->old_user_charge_num ?>/
                                <?php echo $model->old_user_charge_order_num ?>/
                                <?php echo $model->old_user_charge_amount ?>
                            </td>
                            <td class="align-right">
                                <?php echo $model->new_user_charge_num ?>/
                                <?php echo $model->new_user_charge_order_num ?>/
                                <?php echo $model->new_user_charge_amount ?>
                            </td>
                            <td class="align-right">
                                <?php echo $model->z_playCount ?>/
                                <?php echo $model->z_grabCount ?>/
                                <?php echo $model->z_playCount>0 ? round(($model->z_grabCount/$model->z_playCount)*100,2):0 ?>%
                            </td>
                            <td class="align-right">
                                <?php echo $model->p_playCount ?>/
                                <?php echo $model->p_grabCount ?>/
                                <?php echo $model->p_playCount>0 ? round(($model->p_grabCount/$model->p_playCount)*100,2):0 ?>%
                            </td>
                            <td class="align-right">
                                <?php echo $model->l_playCount ?>/
                                <?php echo $model->l_grabCount ?>/
                                <?php echo $model->l_playCount>0 ? round(($model->l_grabCount/$model->l_playCount)*100,2):0 ?>%
                            </td>
                            <td class="align-right">
                                <?php echo $model->play_count ?>
                            </td>
<!--                            <td class="align-right">-->
<!--                                --><?php //echo $model->grab_count ?>
<!--                            </td>-->
<!--                            <td class="align-right">-->
<!--                                --><?php //echo $model->z_grabCount ?>
<!--                            </td>-->
<!--                            <td class="align-right">-->
<!--                                --><?php //echo $model->play_count>0 ? round(($model->grab_count/$model->play_count)*100,2):0 ?><!--%-->
<!--                            </td>-->
                            <td class="align-right">
                                
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?= yii\widgets\LinkPager::widget([
            'pagination' => $pages,
        ]) ?>

        <!-- end users table -->
    </div>
</div>