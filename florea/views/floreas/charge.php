<?php
$this->title = '分销渠道管理列表';
/* @var $this \yii\web\View */
backend\assets\DatePickerAsset::register($this);
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>
<div class="erp-order-list">
    <div class="form mb20">
        <form action="<?= yii\helpers\Url::to(['/floreas/charge'])?>" method="get" class="form-inline">
            <div class="mb10">
                <div class="form-group">
                    <label class="sr-only" for="member_id">用户ID</label>
                    <input type="text" class="form-control" id="receiveUser" placeholder="输入用户ID"  name="member_id" value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('member_id'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="member_id">邀请人id</label>
                    <input type="text" class="form-control" id="inviter" placeholder="输入邀请人id"  name="inviter" value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('inviter'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="member_name">用户名</label>
                    <input type="text" class="form-control" id="member_name" placeholder="输入用户名"  name="member_name" value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('member_name'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="startTime">开始时间</label>
                    <input type="text" class="form-control" id="startTime" name="startTime" placeholder="输入开始时间"
                           data-date-format="yyyy-mm-dd hh:ii:ss"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('startTime'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="endTime">结束时间</label>
                    <input type="text" class="form-control" id="endTime" name="endTime" placeholder="输入结束时间"
                           data-date-format="yyyy-mm-dd hh:ii:ss"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('endTime'))?>">
                </div>
                <div class="form-group">
                    <select class="form-control" id="charge_state" name="charge_state">
                        <option value="订单状态">订单状态</option>
                        <option value="1" <?='已完成'==Yii::$app->getRequest()->get('charge_state')?'selected':''?>>已完成</option>
                        <option value="0" <?='未完成'==Yii::$app->getRequest()->get('charge_state')?'selected':''?>>未完成</option>
                    </select>
                </div>
                <div class="form-group">
                    <select class="form-control" id="charge_name" name="charge_name">
                        <option value='充值规则'>全部</option>
                        <?php
                        foreach($chargeNames as $k=>$v){
                            $charge_name = $v['charge_name'];
                            echo "<option value='$charge_name'>$charge_name</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-success">搜索</button>
                <button type="button" class="btn btn-primary btn-success" id="check" onclick="chk()">导出数据</button>
                <button id="Refresh" class="btn btn-primary btn-success">刷新</button>
            </div>
        </form>
    </div>
    <br/>

    <!-- Users table -->
    <div class="head" style="width: auto;height: 40px;background-color: #b8c7ce;font-size: 16px;color: #696969">
        <b style="margin-left: 20px;margin-top: 10px">充值人数：<?php echo $catch_count; ?></b>
        <b style="margin-left: 400px;margin-top: 10px">充值总额（元）：<?php echo $sum;?></b>
    </div>
    <br/>
    <div class="col-lg-12">
        <table class="table table-bordered table-striped table-hover" style="border: 1px solid black">
            <thead>
            <tr>
                <th class="span3 sortable" style="border: 1px solid black">
                    <span class="line"></span>订单编号
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>充值规则
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>充值金额
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>用户id
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>用户分级
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>邀请人id
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>用户名
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>订单状态
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>充值前娃娃币
                </th>
                <th class="span3 sortable" style="border: 1px solid black">
                    <span class="line"></span>充值后娃娃币
                </th>
                <th class="span3 sortable" style="border: 1px solid black">
                    <span class="line"></span>充值娃娃币
                </th>
                <th class="span3 sortable" style="border: 1px solid black">
                    <span class="line"></span>赠送娃娃币
                </th>
                <th class="span3 sortable" style="border: 1px solid black">
                    <span class="line"></span>充值时间
                </th>
            </tr>
            </thead>
            <tbody id="content" style="border: 1px solid black">
            <?php foreach ($rows as $value) { ?>
                <tr class="first" style="border: 1px solid black">
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['order_no'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['charge_name'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['price'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['memberID'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['rank'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['inviter'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['member_name'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php
                        if($value['charge_state'] == 1){
                            echo "已完成";
                        }else{
                            echo "未完成";
                        }
                        ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['coins_before'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['coins_after'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['coins_charge'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['coins_offer'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['create_date'] ?>
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
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>
    <?php
    $this->beginBlock('pageJs');
    ?>
    $('#startTime').datetimepicker({
        autoclose:true,
        language:'zh-CN',
        format:'yyyy-mm-dd hh:ii:ss'
    });
    $('#endTime').datetimepicker({
        autoclose:true,
        language:'zh-CN',
        format:'yyyy-mm-dd hh:ii:ss'
    });
    <?php
    $this->endBlock();
    $this->registerJs($this->blocks['pageJs']);
    ?>

    function chk(){
        var url = "/channels/charge?export=on";
        window.location.href = url;
    }

    var refresh = document.getElementById('Refresh');
    refresh.onclick = function () {
        window.location.reload();
    }
</script>