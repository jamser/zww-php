<?php
$this->title = '用户金币 钻石 购买与消耗 数据统计';
backend\assets\DatePickerAsset::register($this);
use dosamigos\datepicker\DatePicker;
?>
<script src="js/jquery-3.1.1.min.js"></script>
<div class="container-fluid">
    <div id="pad-wrapper" class="users-list">
        <div class="form mb20">
            <form action="<?= yii\helpers\Url::to(['/doll/record/coins-data'])?>" method="get" class="form-inline">
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
                        <span class="line">金币总量</span>
                    </th>
                    <th class="span3 sortable">
                        <span class="line">钻石总量</span>
                    </th>
                    <th class="span3 sortable">
                        <span class="line">一天内金币购买量</span>
                    </th>
                    <th class="span2 sortable">
                        <span class="line">一天内钻石购买量</span>
                    </th>
                    <th class="span2 sortable">
                        <span class="line">一天内金币消耗量</span>
                    </th>
                    <th class="span2 sortable">
                        <span class="line">一天内钻石消耗量</span>
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
                            <?php echo $v['coins'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['superTickets'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['coins_charge'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['superTickets_charge'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['coins_cost'] ?>
                        </td>
                        <td class="align-right">
                            <?php echo $v['superTickets_cost'] ?>
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