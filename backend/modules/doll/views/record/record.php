<?php
$this->title = '用户注册 充值 发货 抓取 数据统计';
backend\assets\DatePickerAsset::register($this);
use dosamigos\datepicker\DatePicker;
?>
<script src="js/jquery-3.1.1.min.js"></script>
<div class="container-fluid">
    <div id="pad-wrapper" class="users-list">
        <div class="form mb20">
            <form action="<?= yii\helpers\Url::to(['/doll/record/index'])?>" method="get" class="form-inline">
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

        <!-- Users table -->
        <div class="row-fluid table">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th class="span3 sortable align-right">
                        <span class="line">日期</span>
                    </th>
                    <th class="span3 sortable">
                        <span class="line">手机注册用户量</span>
                    </th>
                    <th class="span3 sortable">
                        <span class="line">手机注册充值用户量</span>
                    </th>
                    <th class="span3 sortable">
                        <span class="line">手机注册充值金额</span>
                    </th>
                    <th class="span2 sortable">
                        <span class="line">微信注册用户量</span>
                    </th>
                    <th class="span2 sortable">
                        <span class="line">微信注册充值用户量</span>
                    </th>
                    <th class="span2 sortable">
                        <span class="line">微信注册充值金额</span>
                    </th>
                    <th class="span2 sortable">
                        <span class="line">充值用户发货人数</span>
                    </th>
                    <th class="span2 sortable">
                        <span class="line">充值用户发货量</span>
                    </th>
                    <th class="span3 sortable">
                        <span class="line">用户抓中次数</span>
                    </th>
                    <th class="span3 sortable" >
                        <span class="line">充值用户抓中次数</span>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($models as $model=>$v) {
                    /* @var $model \common\models\doll\MachineStatistic */
                    ?>
                    <tr class="first">
                        <td>
                            <?php echo date('Y-m-d',strtotime($v['day'])) ?>
                        </td>
                        <td>
                            <?php echo $v['mobile_register'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['mobile_charge'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['mobile_price'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['wehcat_register'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['wechat_charge'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['wechat_price'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['charge_order'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['order_num'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['catch_num'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['charge_num'] ?>
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

        <!-- end users table -->
    </div>
</div>