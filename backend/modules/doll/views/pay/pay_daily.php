<?php
$this->title = '支付日报';
backend\assets\DatePickerAsset::register($this);
use dosamigos\datepicker\DatePicker;
?>
<div class="container-fluid">
    <div id="pad-wrapper" class="statistic-list">
        <div class="form mb20">
            <form action="<?= yii\helpers\Url::to(['/doll/pay/pay-daily'])?>" method="get" class="form-inline">
                <div class="mb10">
                    <div class="form-group">
                        <?= DatePicker::widget([
                            'name' => 'day',
                            'attribute' => 'day',
                            'options' => ['placeholder' => '日期'],
                            'template' => '{addon}{input}',
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd'
                            ]
                        ]);?>
                    </div>
                    <button type="submit" id="submit" class="btn btn-primary btn-success">搜索</button>
                </div>
            </form>
        </div>
        <div class="head" style="width: auto;height: 20px;background-color:#dcdcdc ;font-size: 16px;color: #696969">
            充值人数占比=充值人数/新注册人数    &nbsp&nbsp&nbsp&nbsp   首充占比=新用户充值额/充值金额
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
                        <span class="line"></span>新注册人数
                    </th>
                    <th class="span2 sortable">
                        <span class="line"></span>充值金额
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>充值人数
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>充值人数占比
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>首充占比
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>充值订单数
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>新用户充值人数
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>新用户充值订单
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>新用户充值额
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>新用户人均充值
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>新用户人均订单
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>老用户充值人数
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>老用户充值订单
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>老用户充值额
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>老用户人均充值
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>老用户人均订单数
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($models as $model=>$v) {
                    /* @var $model \common\models\doll\Statistic */
                    ?>
                    <tr class="first">
                        <td>
                            <?php echo $v['day'] ?>
                        </td>
                        <td>
                            <?php echo $v['registration_num'] ?>
                        </td>
                        <td>
                            <?php echo $v['charge_amount'] ?>
                        </td>
                        <td>
                            <?php echo $v['charge_user_num'] ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $rate = $v['charge_user_num']>0 ? round(($v['charge_user_num']/$v['registration_num'])*100,2):0;
                            echo $rate;
                            ?>%
                        </td>
                        <td class="align-right">
                            <?php
                            $rate = $v['new_user_charge_amount']>0 ? round(($v['new_user_charge_amount']/$v['charge_amount'])*100,2):0;
                            echo $rate;
                            ?>%
                        </td>
                        <td>
                            <?php echo $v['charge_order_num'] ?>
                        </td>

                        <td>
                            <?php echo $v['new_user_charge_num'] ?>
                        </td>
                        <td>
                            <?php echo $v['new_user_charge_order_num'] ?>
                        </td>

                        <td>
                            <?php echo $v['new_user_charge_amount'] ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $rate = $v['new_user_charge_amount']>0 ? round(($v['new_user_charge_amount']/$v['new_user_charge_num']),2):0;
                            echo $rate;
                            ?>
                        </td>

                        <td class="align-right">
                            <?php
                            $rate = $v['new_user_charge_order_num']>0 ? round(($v['new_user_charge_order_num']/$v['new_user_charge_num']),2):0;
                            echo $rate;
                            ?>
                        </td>
                        <td>
                            <?php echo $v['old_user_charge_num'] ?>
                        </td>
                        <td>
                            <?php echo $v['old_user_charge_order_num'] ?>
                        </td>
                        <td>
                            <?php echo $v['old_user_charge_amount'] ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $rate = $v['old_user_charge_amount']>0 ? round(($v['old_user_charge_amount']/$v['old_user_charge_num']),2):0;
                            echo $rate;
                            ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $rate = $v['old_user_charge_order_num']>0 ? round(($v['old_user_charge_order_num']/$v['old_user_charge_num']),2):0;
                            echo $rate;
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <?=
        \yii\widgets\LinkPager::widget([
            'pagination' => $pages,
            'nextPageLabel' => '下一页',
            'prevPageLabel' => '上一页',
            'firstPageLabel' => '首页',
            'lastPageLabel' => '尾页',
        ])
        ?>
    </div>
    <script>
        <?php
        $this->beginBlock('pageJs');
        ?>
        $('#day').datetimepicker({
            autoclose:true,
            language:'zh-CN',
            format:'yyyy-mm-dd'
        });

        <?php
        $this->endBlock();
        $this->registerJs($this->blocks['pageJs']);
        ?>

    </script>
</div>