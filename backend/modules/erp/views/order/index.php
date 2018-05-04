<?php
$this->title = 'ERP订单列表';
/* @var $this \yii\web\View */
backend\assets\DatePickerAsset::register($this);
use yii\helpers\Html;
?>
<div class="erp-order-list">
    <div class="form mb20">
        <form action="<?= yii\helpers\Url::to(['/erp/order/index'])?>" method="get" class="form-inline">
            <div class="mb10">
                <div class="form-group">
                    <label class="sr-only" for="startTime">开始时间</label>
                    <input type="text" class="form-control" id="startTime" name="startTime" placeholder="订单开始时间"
                           data-date-format="yyyy-mm-dd hh:ii:00"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('startTime'))?>">
                  </div>
                  <div class="form-group">
                    <label class="sr-only" for="endTime">结束时间</label>
                    <input type="text" class="form-control" id="endTime" name="endTime" placeholder="订单结束时间"
                           data-date-format="yyyy-mm-dd hh:ii:00"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('endTime'))?>">
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
                <input type="hidden" id="agency_value" name="agency" />
                <input type="hidden" id="allmerge_value" name="allmerge" />
                <input type="hidden" id="detain_value" name="detain" />
                <input type="hidden" id="last_value" name="last" />
            </div>
            
            <div class="mb10">
                <div class="form-group">
                    <label class="control-label" for="status">订单状态</label>
                    <select class="form-control" id="status" name="status">
<!--                        <option value="全部">全部</option>-->
                        <option value="申请发货" <?='申请发货'==Yii::$app->getRequest()->get('status')?'selected':'selected'?>>申请发货</option>
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
                  <button type="button" class="btn btn-primary btn-primary" id="checks" onclick="chks()">合并勾选数据</button>
<!--                  <button type="button" class="btn btn-primary btn-primary" id="last" onclick="dayNum()">发货vip</button>-->
                  <button type="button" class="btn btn-default btn-default" id="checkall" onclick="chkall()">合并时间段数据</button>
                  <button type="button" class="btn btn-default btn-default" id="split" onclick="splitSubmit()">分割订单</button>
                  <button type="button" class="btn btn-default btn-default" id="agency" onclick="agencySubmit()">相同经销商合并</button>
                  <button type="button" class="btn btn-primary btn-warning" id="detain" onclick="detainOrders()">一键扣留</button>
                  <?= Html::a('批量导入快递单号', [yii\helpers\Url::to(['/erp/order/add'])], ['class' => 'btn btn-primary btn-warning']) ?>
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
                        <span class="line"></span>充值金额
                    </th>
                    <th class="span3 sortable" style="width: 50px;border: 1px solid black">
                        <span class="line"></span>用户抓取个数
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
                        <span class="line"></span>寄存箱
                    </th>
                    <th class="span3 sortable align-right" style="border: 1px solid black">
                        <span class="line"></span>日期
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
                    <th class="span3 sortable" style="width: 50px;border: 1px solid black">
                        <span class="line"></span>扣留操作
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
                            <?php
                            $id = $value['memberID'];
                            if(isset($catchs[$value['memberID']]) && isset($s_catchs[$value['memberID']])){
                                $rate = round(($catchs[$value['memberID']]/$s_catchs[$value['memberID']])*100,2);
                                if($rate>=50){
                                    echo "<span style='color: forestgreen'>$id</span>";
                                }else{
                                    echo $id;
                                }
                            }else{
                                echo $id;
                            }
                            ?>
                        </td>
                        <td class="align-right" style="border: 1px solid black">
                            <?php echo isset($charges[$value['memberID']]) ? $charges[$value['memberID']] : ''  ?>
                        </td>
                        <td class="align-right" style="border: 1px solid black">
                            <?php echo isset($catchs[$value['memberID']]) ? $catchs[$value['memberID']] : ''  ?>
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
                            <?php
                            $phone = $value['receiver_phone'];
                            if(isset($unusualData[$value['receiver_phone']])){
                                echo "<span style='color:red '>$phone</span>";
                            }else{
                                echo $phone;
                            }
                            ?>
                        </td>
                        <td class="align-right" style="border: 1px solid black">
                            <?=$value['province'].$value['city'].$value['county'].$value['street']?>
                        </td>
                        <td class="align-right" style="border: 1px solid black">
                            <?php
                            if(isset($j_data[$value['memberID']])){
                                $num = $j_data[$value['memberID']];
                                if($num > 5){
                                    echo "<span style='color:#ffd100 '>$num</span>";
                                }else{
                                    echo $num;
                                }
                            }else{
                                echo 0;
                            }
                            ?>
                        </td>
                        <td class="align-right" style="width: 20px;border: 1px solid black">
                            <?php echo $value['order_date'] ?>
                        </td>
                        <td class="align-right" style="width: 20px;border: 1px solid black">
                            <?php echo $value['deliver_number'] ?>
                        </td>
                        <td class="align-right" style="width: 20px;border: 1px solid black">
                            <?php echo $value['status'] ?>
                        </td>
                        <td class="align-right" style="width: 20px;border: 1px solid black">
                            <?= Html::a('修改信息', ['order/update','id' => $value['id']],['class' => 'btn btn-primary btn-info']) ?>
                        </td>
                        <td class="align-right" style="width: 20px;border: 1px solid black">
                            <?= Html::a('扣留订单', ['order/detain','id' => $value['id']],['class' => 'btn btn-primary btn-warning']) ?>
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

    function detainOrders(){
        $('#detain_value').val('on');
        $('#submit').click();
    }

    function dayNum(){
        $('#last_value').val('on');
        $('#submit').click();
    }
</script>