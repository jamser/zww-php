<?php
$this->title = 'ERP订单列表';
/* @var $this \yii\web\View */
backend\assets\DatePickerAsset::register($this);
use yii\helpers\Html;
?>
<div class="erp-order-list">
    <div class="form mb20">
        <form action="<?= yii\helpers\Url::to(['/erp/order/new-index'])?>" method="get" class="form-inline">
            <div class="mb10">
                <div class="form-group">
                    <label class="sr-only" for="startTime">开始时间</label>
                    <input type="text" class="form-control" id="startTime" name="startTime" placeholder="订单开始时间"
                           data-date-format="yyyy-mm-dd hh:ii:ss"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('startTime'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="endTime">结束时间</label>
                    <input type="text" class="form-control" id="endTime" name="endTime" placeholder="订单结束时间"
                           data-date-format="yyyy-mm-dd hh:ii:ss"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('endTime'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="phone">手机号</label>
                    <input type="text" class="form-control" id="phone" placeholder="手机号" name="phone"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('phone'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="receiveUser">收件人</label>
                    <input type="text" class="form-control" id="receiveUser" placeholder="收件人" name="receiveUser"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('receiveUser'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="orderNo">订单号</label>
                    <input type="text" class="form-control" id="receiveUser" placeholder="订单号"  name="orderNo" value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('orderNo'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="deliverNo">物流单号</label>
                    <input type="text" class="form-control" id="receiveUser" placeholder="物流单号"  name="deliverNo" value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('deliverNo'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="userId">用户ID</label>
                    <input type="text" class="form-control" id="receiveUser" placeholder="用户ID"  name="userId" value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('userId'))?>">
                </div>
                <input type="hidden" id="split_value" name="split" />
            </div>

            <div class="mb10">
                <div class="form-group">
                    <label class="control-label" for="status">订单状态</label>
                    <select class="form-control" id="status" name="status">
                        <option value="全部">全部</option>
                        <option value="申请发货" <?='申请发货'==Yii::$app->getRequest()->get('status')?'selected':''?>>申请发货</option>
                        <option value="已发货" <?='已发货'==Yii::$app->getRequest()->get('status')?'selected':''?>>已发货</option>
                        <option value="异常抓取审核拒绝" <?='异常抓取审核拒绝'==Yii::$app->getRequest()->get('status')?'selected':''?>>异常抓取审核拒绝</option>
                    </select>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="export"> 导出订单
                    </label>
                    <!--                    <label>-->
                    <!--                        <input type="checkbox" name="exportMerge"> 导出时合并三要素相同订单-->
                    <!--                    </label>-->
                </div>
                <button type="submit" id="submit" class="btn btn-primary btn-success">搜索/导出</button>
                <button type="button" class="btn btn-primary btn-success" id="check" onclick="chk()">导出勾选数据</button>
<!--                <button type="button" class="btn btn-primary btn-primary" id="checks" onclick="chks()">合并勾选数据</button>-->
                <!--                  <button type="button" class="btn btn-primary btn-primary" id="checkall" onclick="chkall()">合并全部数据</button>-->
                <?= Html::a('批量导入快递单号', [yii\helpers\Url::to(['/erp/order/add'])], ['class' => 'btn btn-primary btn-warning']) ?>
<!--                <button type="button" class="btn btn-primary btn-primary" id="split" onclick="splitSubmit()">分割订单</button>-->
            </div>
        </form>
    </div>

    <!-- Users table -->
    <div class="col-lg-12">
        <table class="table table-bordered table-striped table-hover" style="border: 1px solid black">
            <thead>
            <tr>
                <th class="span3 sortable" style="width: 50px;border: 1px solid black">
                    <span class="line"></span><input type="checkbox" value="" id="checkany" name="" size="" class="m-wrap  "  onclick="checkfk()">ID
                </th>
                <th class="span3 sortable" style="width: 50px;border: 1px solid black">
                    <span class="line"></span>用户ID
                </th>
                <th class="span3 sortable" style="width: 50px;border: 1px solid black">
                    <span class="line"></span>订单号
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>自定义区域1*（商品编码、娃娃名称、数量）
                </th>
                <th class="span3 sortable align-right" style="width:60px;border: 1px solid black">
                    <span class="line"></span>收件人姓名
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>收件人手机
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>收件人详细地址
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>自定义区域
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>日期
                </th>
                <th class="span3 sortable" style="width: 50px;border: 1px solid black">
                    <span class="line"></span>快递公司
                </th>
                <th class="span3 sortable" style="width: 50px;border: 1px solid black">
                    <span class="line"></span>物流单号
                </th>
                <th class="span3 sortable" style="width: 50px;border: 1px solid black">
                    <span class="line"></span>发货状态
                </th>
                <th class="span3 sortable" style="width: 50px;border: 1px solid black">
                    <span class="line"></span>编辑
                </th>
            </tr>
            </thead>
            <tbody id="content" style="border: 1px solid black">
            <?php foreach ($rows as $value) { ?>
                <tr class="first" style="border: 1px solid black">
                    <td style="border: 1px solid black">
                        <input type='checkbox' name="fkcheck" value="<?php echo $value['id'] ?>"><?php echo $value['id'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['memberID'] ?>
                    </td>
                    <td style="border: 1px solid black">

                        <?= Html::a(substr($value['order_number'],6), ['order/info','id' => $value['id']]) ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php foreach($value['dollItems'] as $dollItem) {
                            echo " {$dollItem['dollName']} {$dollItem['doll_code']} * {$dollItem['quantity']}<br/>";
                        } ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['receiver_name'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['receiver_phone'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?=$value['province'].$value['city'].$value['county'].$value['street']?>
                    </td>
                    <td class="align-right" style="border: 1px solid black"></td>
                    <td class="align-right" style="width: 20px;border: 1px solid black">
                        <?php echo $value['order_date'] ?>
                    </td>
                    <td class="align-right" style="width: 20px;border: 1px solid black">
                        <?php echo $value['deliver_method'] ?>
                    </td>
                    <td class="align-right" style="width: 20px;border: 1px solid black">
                        <?php echo $value['deliver_number'] ?>
                    </td>
                    <td class="align-right" style="width: 20px;border: 1px solid black">
                        <button type="submit" class="btn btn-default"><?php echo $value['status'] ?></button>
                    </td>
                    <td class="align-right" style="width: 20px;border: 1px solid black">
                        <?= Html::a('修改信息', ['order/update','id' => $value['id']],['class' => 'btn btn-primary btn-info']) ?>
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

    $("#check").click(function () {
        $(":checkbox").attr("checked",true);
    })
    <?php
   $this->endBlock();
   $this->registerJs($this->blocks['pageJs']);
   ?>

    function checkfk(){
        var allcheck=document.getElementById("checkany");  //获取 全选check 的id
        var othercheck=document.getElementsByName("fkcheck"); //获取数组名称为fkcheck的 复选框

        for(var i=0;i<othercheck.length;i++){ if(allcheck.checked){ othercheck[i].checked=true; }else{ othercheck[i].checked=false; } } }

    function chk(){
        var chk_value =[];
        $('input[name="fkcheck"]:checked').each(function(){
            chk_value.push($(this).val());
            var url = "/erp/order/index?id="+chk_value+"&export=on";
            window.location.href = url;
        });
    }
    function chks(){
        var chk_value =[];
        $('input[name="fkcheck"]:checked').each(function(){
            chk_value.push($(this).val());
            var url = "/erp/order/index?id="+chk_value+"&merge=on";
            window.location.href = url;
        });
    }

    function chkall(){
        var status = $("#status").val();
        var startTime = $("#startTime").val();
        var endTime = $("#endTime").val();
        var url = "/erp/order/index?status="+status+"&startTime="+startTime+"&endTime="+endTime+"&allmerge=on";
        window.location.href = url;
    }

    function splitSubmit(){
        $('#split_value').val('on');
        $('#submit').click();
    }
</script>