<?php
$this->title = '用户支付数据';
backend\assets\DatePickerAsset::register($this);
use dosamigos\datepicker\DatePicker;
?>
<div class="container-fluid">
    <div id="pad-wrapper" class="statistic-list">
        <div class="form mb20">
            <form action="<?= yii\helpers\Url::to(['/doll/pay/index'])?>" method="get" class="form-inline">
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
            首充人数占比=首充人数/总充值人数    &nbsp&nbsp&nbsp&nbsp   二次充值占比=二次充值人数/总充值人数
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
                        <span class="line"></span>注册人数
                    </th>
                    <th class="span3 sortable">
                        <span class="line"></span>支付人数
                    </th>
                    <th class="span2 sortable">
                        <span class="line"></span>首次充值人数
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>首充人数占比
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>二次充值人数
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>二次充值人数占比
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>三次充值人数
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>三次充值人数占比
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>四次充值人数
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>四次充值人数占比
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>五次充值人数
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>五次充值人数占比
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>六次充值人数
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>六次充值人数占比
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>七次充值人数
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>七次充值人数占比
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>八次充值人数
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>八次充值人数占比
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>八次充值以上人数
                    </th>
                    <th class="span3 sortable align-right">
                        <span class="line"></span>八次充值以上人数占比
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
                            <?php echo $v['pay_user_num'] ?>
                        </td>
                        <td>
                            <?php echo $v['pay_1'] ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $rate = $v['pay_1']>0 ? round(($v['pay_1']/$v['pay_user_num'])*100,2):0;
                            echo $rate;
                            ?>%
                        </td>
                        <td>
                            <?php echo $v['pay_2'] ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $rate = $v['pay_2']>0 ? round(($v['pay_2']/$v['pay_user_num'])*100,2):0;
                            echo $rate;
                            ?>%
                        </td>
                        <td>
                            <?php echo $v['pay_3'] ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $rate = $v['pay_3']>0 ? round(($v['pay_3']/$v['pay_user_num'])*100,2):0;
                            echo $rate;
                            ?>%
                        </td>
                        <td>
                            <?php echo $v['pay_4'] ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $rate = $v['pay_4']>0 ? round(($v['pay_4']/$v['pay_user_num'])*100,2):0;
                            echo $rate;
                            ?>%
                        </td>
                        <td>
                            <?php echo $v['pay_5'] ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $rate = $v['pay_5']>0 ? round(($v['pay_5']/$v['pay_user_num'])*100,2):0;
                            echo $rate;
                            ?>%
                        </td>
                        <td>
                            <?php echo $v['pay_6'] ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $rate = $v['pay_6']>0 ? round(($v['pay_6']/$v['pay_user_num'])*100,2):0;
                            echo $rate;
                            ?>%
                        </td>
                        <td>
                            <?php echo $v['pay_7'] ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $rate = $v['pay_7']>0 ? round(($v['pay_7']/$v['pay_user_num'])*100,2):0;
                            echo $rate;
                            ?>%
                        </td>
                        <td>
                            <?php echo $v['pay_8'] ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $rate = $v['pay_8']>0 ? round(($v['pay_8']/$v['pay_user_num'])*100,2):0;
                            echo $rate;
                            ?>%
                        </td>
                        <td>
                            <?php echo $v['pay_gt_8'] ?>
                        </td>
                        <td class="align-right">
                            <?php
                            $rate = $v['pay_gt_8']>0 ? round(($v['pay_gt_8']/$v['pay_user_num'])*100,2):0;
                            echo $rate;
                            ?>%
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