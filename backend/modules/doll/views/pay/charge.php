<?php
$this->title = '礼包购买统计';
backend\assets\DatePickerAsset::register($this);
use dosamigos\datepicker\DatePicker;
?>
<div class="container-fluid">
    <div id="pad-wrapper" class="statistic-list">
        <div class="form mb20">
            <form action="<?= yii\helpers\Url::to(['/doll/pay/charge'])?>" method="get" class="form-inline">
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
                    <th class="span3 sortable">
                        <span class="line"></span>日期
                    </th>
                    <th class="span3 sortable">
                        <span class="line"></span>充值金额
                    </th>
                    <th class="span3 sortable">
                        <span class="line"></span>礼包
                    </th>
                    <th class="span2 sortable">
                        <span class="line"></span>数量
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>人数
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>人均购买
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
                            <?php echo $v['price'] ?>
                        </td>
                        <td>
                            <?php echo $v['charge_name'] ?>
                        </td>
                        <td>
                            <?php echo $v['charge_num'] ?>
                        </td>
                        <td>
                            <?php echo $v['buy_num'] ?>
                        </td>
                        <td>
                            <?php echo $v['buy_one'] ?>
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
            format:'yyyy-mm-dd hh:ii:00'
        });

        <?php
        $this->endBlock();
        $this->registerJs($this->blocks['pageJs']);
        ?>

    </script>
</div>