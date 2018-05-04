<?php
$this->title = '注册人数趋势图';
use miloschuman\highcharts\Highcharts;
use dosamigos\datepicker\DatePicker;
?>

<div class="form mb20">
    <form action="<?= yii\helpers\Url::to(['/channels/chart-hour'])?>" method="get" class="form-inline">
        <div class="mb10">
            <div class="form-group">
                <?= DatePicker::widget([
                    'name' => 'date',
                    'attribute' => 'date',
                    'options' => ['placeholder' => '日期'],
                    'template' => '{addon}{input}',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ]);?>
            </div>
            <button type="submit" id="submit" class="btn btn-primary btn-success">查看</button>
        </div>
    </form>
</div>
<div class='col-lg-12'>
    <div class="row">
        <?php
        echo Highcharts::widget([
            'scripts' => [
                'modules/exporting',
                'themes/grid-light',
            ],
            'options' => [
                'title' => ['text' => '一天内注册人数趋势'],
                'credits' => [
                    'text' => 'p.365zhuawawa.com',
                    'href' => 'http://p.365zhuawawa.com',
                ],
                'xAxis' => [
                    'categories' => $hours,
                ],
                'yAxis' => [
                    'title' => ['text' => '注册人数'],
                    'tickInterval' => 100,
                ],
                'series' => [
                    ['name' => '注册人数', 'data' => $counts_h],
                ]
            ]
        ]);
        ?>

    </div>
</div>