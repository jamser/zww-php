<?php
$this->title = '礼包趋势图';
use miloschuman\highcharts\Highcharts;
?>

<div class="form mb20">
    <form action="<?= yii\helpers\Url::to(['/doll/pay/chart-n'])?>" method="get" class="form-inline">
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
            <div class="form-group">
                <label class="control-label" for="name">礼包类型</label>
                <select class="form-control" id="name" name="name">
                    <?php
                    foreach($chargeNames as $k=>$v){
                        $charge_name = $v['charge_name'];
                        echo "<option value='$charge_name'>$charge_name</option>";
                    }
                    ?>
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
                    ['name' => $name, 'data' => $prices],
                ]
            ]
        ]);
        ?>
        <!--widget end-->
    </div>
</div>