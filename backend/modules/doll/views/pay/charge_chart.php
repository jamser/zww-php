<?php
$this->title = '充值情况折线图';
use miloschuman\highcharts\Highcharts;
?>

<div class="form mb20">
    <form action="<?= yii\helpers\Url::to(['/doll/pay/charge-chart'])?>" method="get" class="form-inline">
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
        echo Highcharts::widget(array(
            'scripts' => array(
                'modules/exporting',
                'themes/grid-light',
            ),
            'options' => array(
                'title' => array('text' => '新/老用户充值趋势图'),
                'credits' => array(
                    'text' => 'p.365zhuawawa.com',
                    'href' => 'http://p.365zhuawawa.com',
                ),
                'xAxis' => array(
                    'categories' => $days,
                ),
                'yAxis' => array(
                    'title' => ['text' => '充值金额百分比']
                ),
                'series' => array(
                    array('name' => '新用户充值百分比', 'data' => $new_rate),
                    array('name' => '老用户充值百分比', 'data' => $old_rate),
                )
            )
        ));
        ?>
        <!--widget end-->
    </div>
</div>