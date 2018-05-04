<?php
$this->title = '扣留订单列表';
/* @var $this \yii\web\View */
backend\assets\DatePickerAsset::register($this);
use yii\helpers\Html;
?>
<div class="erp-order-list">
    <div class="form mb20">
        <form action="<?= yii\helpers\Url::to(['/erp/order/detain-order'])?>" method="get" class="form-inline">
            <div class="mb10">
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
                    <label class="sr-only" for="userId">用户ID</label>
                    <input type="text" class="form-control" id="receiveUser" placeholder="用户ID"  name="userId" value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('userId'))?>">
                </div>
                <button type="submit" id="submit" class="btn btn-primary btn-success">搜索</button>
                <?= Html::a('导入扣留订单', [yii\helpers\Url::to(['/erp/order/add-detain'])], ['class' => 'btn btn-primary btn-warning']) ?>
            </div>
        </form>
    </div>

    <!-- Users table -->
    <div class="col-lg-12">
        <table class="table table-bordered table-striped table-hover" style="border: 1px solid black">
            <thead>
            <tr>
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
                    <span class="line"></span>订单日期
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>扣留日期
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>扣留原因
                </th>
                <th class="span3 sortable" style="width: 50px;border: 1px solid black">
                    <span class="line"></span>恢复订单
                </th>
                <th class="span3 sortable" style="width: 50px;border: 1px solid black">
                    <span class="line"></span>操作
                </th>
            </tr>
            </thead>
            <tbody id="content" style="border: 1px solid black">
            <?php foreach ($rows as $value) { ?>
                <tr class="first" style="border: 1px solid black">
                    <td class="align-right" style="border: 1px solid black">
                        <?php
                        $id = $value['memberID'];
                        echo $id;
                        ?>
                    </td>
                    <td style="border: 1px solid black">
                        <?php echo substr($value['order_number'],6) ?>
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
                        <?php
                        $phone = $value['receiver_phone'];
                        echo $phone;
                        ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?=$value['province'].$value['city'].$value['county'].$value['street']?>
                    </td>
                    <td class="align-right" style="width: 20px;border: 1px solid black">
                        <?php echo $value['order_date'] ?>
                    </td>
                    <td class="align-right" style="width: 20px;border: 1px solid black">
                        <?php echo $value['detain_date'] ?>
                    </td>
                    <td class="align-right" style="width: 20px;border: 1px solid black">
                        <?php echo $value['detain_reason'] ?>
                    </td>
                    <td class="align-right" style="width: 20px;border: 1px solid black">
                        <?= Html::a('恢复订单', ['order/order','id' => $value['id']],['class' => 'btn btn-primary btn-warning']) ?>
                    </td>
                    <td class="align-right" style="width: 20px;border: 1px solid black">
                        <?= Html::a('修改', ['order/detain-update','id' => $value['id']],['class' => 'btn btn-primary btn-info']) ?>
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
        format:'yyyy-mm-dd hh:ii:00'
    });
    $('#endTime').datetimepicker({
        autoclose:true,
        language:'zh-CN',
        format:'yyyy-mm-dd hh:ii:00'
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
        $('#allmerge_value').val('on');
        $('#submit').click();
        var status = $("#status").val();
//        var startTime = $("#startTime").val();
//        var endTime = $("#endTime").val();
//        var url = "/erp/order/index?status="+status+"&startTime="+startTime+"&endTime="+endTime+"&allmerge=on";
//        window.location.href = url;
    }

    function splitSubmit(){
        $('#split_value').val('on');
        $('#submit').click();
    }

    function agencySubmit(){
        $('#agency_value').val('on');
        $('#submit').click();
    }
</script>