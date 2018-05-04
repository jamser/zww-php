<?php
$this->title = '注册人数趋势图';
use miloschuman\highcharts\Highcharts;
use dosamigos\datepicker\DatePicker;
?>
<div class="form mb20">
    <form action="<?= yii\helpers\Url::to(['/channels/chart'])?>" method="get" class="form-inline">
        <div class="mb10">
            <div class="form-group">
                <label class="control-label" for="day">不同周期</label>
                <select class="form-control" id="day" name="day">
                    <option value="周期">周期</option>
                    <option value="3" <?='3'==Yii::$app->getRequest()->get('day')?'selected':''?>>3</option>
                    <option value="7" <?='7'==Yii::$app->getRequest()->get('day')?'selected':''?>>7</option>
                    <option value="30" <?='30'==Yii::$app->getRequest()->get('day')?'selected':''?>>30</option>
                </select>
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
                'title' => ['text' => '近期注册人数趋势'],
                'credits' => [
                    'text' => 'p.365zhuawawa.com',
                    'href' => 'http://p.365zhuawawa.com',
                ],
                'xAxis' => [
                    'categories' => $days,
                ],
                'yAxis' => [
                    'title' => ['text' => '注册人数'],
                    'tickInterval' => 100,
                ],
                'series' => [
                    ['name' => '注册人数', 'data' => $counts_d],
                ]
            ]
        ]);
        ?>
        <!--单柱形图widget start-->
<!--        --><?php
//        echo Highcharts::widget([
//            'scripts' => [
//                'highcharts-more',
//                'modules/exporting',
//                'themes/grid-light',
//            ],
//            'options' => [
//                'title' => ['text' => '近期注册人数'],
//                'credits' => [
//                    'text' => 'p.365zhuawawa.com',
//                    'href' => 'http://p.365zhuawawa.com',
//                ],
//                'xAxis' => [
//                    'categories' => $days,
//                ],
//                'yAxis' => [
//                    'title' => ['text' => '注册人数']
//                ],
//                'series' => [
//                    [
//                        'type' => 'column',
//                        'name' => '注册人数',
//                        'data' => $counts_d
//                    ],
//                ]
//            ]
//        ]);
//        ?>
        <!--widget end-->
    </div>
</div>
