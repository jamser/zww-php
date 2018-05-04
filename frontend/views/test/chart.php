<?php
use miloschuman\highcharts\Highcharts;
?>


<div class='col-lg-6'>
    <div class="row">
        <?php
        echo Highcharts::widget([
            'scripts' => [
                'modules/exporting',
                'themes/grid-light',
            ],
            'options' => [
                'title' => ['text' => '折线'],
                'credits' => [
                    'text' => 'p.365zhuawawa.com',
                    'href' => 'http://p.365zhuawawa.com',
                ],
                'xAxis' => [
                    'categories' => $day,
                ],
                'yAxis' => [
                    'title' => ['text' => '充值金额']
                ],
                'series' => [
                    ['name' => '首充包', 'data' => [981,24,984]],
                    ['name' => '寒假包', 'data' => [435,765,897]],
                    ['name' => '钻石', 'data' => [35,235,565]],
                    ['name' => '超多钻石', 'data' => [35,574,987]],
                ]
            ]
        ]);
        ?>
        <!--widget end-->
    </div>
</div>