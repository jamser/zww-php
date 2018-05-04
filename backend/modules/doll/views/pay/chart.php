<?php
$this->title = '礼包趋势图';
use miloschuman\highcharts\Highcharts;
?>

<div class="form mb20">
    <form action="<?= yii\helpers\Url::to(['/doll/pay/chart'])?>" method="get" class="form-inline">
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
                'title' => ['text' => '礼包购买趋势图'],
                'credits' => [
                    'text' => 'p.365zhuawawa.com',
                    'href' => 'http://p.365zhuawawa.com',
                ],
                'xAxis' => [
                    'categories' => $day,
                ],
                'yAxis' => [
                    'title' => ['text' => '充值金额'],
                    'tickInterval' => 100,
                ],
                'series' => [
                    ['name' => '首充包', 'data' => $first_price],
                    ['name' => '寒假包', 'data' => $h_price],
                    ['name' => '钻石礼包', 'data' => $z_price],
                    ['name' => '超多钻石', 'data' => $m_price],
                    ['name' => '新年礼包', 'data' => $n_price],
                    ['name' => '半糖礼包', 'data' => $t_price],
                    ['name' => '百合包', 'data' => $b_price],
                    ['name' => '爱久久', 'data' => $a_price],
                    ['name' => '土豪包', 'data' => $v_price],
                    ['name' => '周卡', 'data' => $w_price],
                    ['name' => '月卡', 'data' => $y_price],
                    ['name' => '新年钻石包', 'data' => $nz_price],
                    ['name' => '豪华钻石包', 'data' => $hz_price],
                    ['name' => '招财大礼包', 'data' => $zc_price],
                ]
            ]
        ]);
        ?>
        <!--widget end-->
    </div>
</div>