<?php
$this->title = '充值用户展示';
/* @var $this \yii\web\View */
backend\assets\DatePickerAsset::register($this);
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>

    <br/>

    <!-- Users table -->
    <div class="col-lg-12">
        <table class="table table-bordered table-striped table-hover" style="border: 1px solid black">
            <thead>
            <tr>
                <th class="span3 sortable" style="border: 1px solid black">
                    <span class="line"></span>充值金额
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>用户id
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>用户名
                </th>
            </tr>
            </thead>
            <tbody id="content" style="border: 1px solid black">
            <?php foreach ($rows as $value) { ?>
                <tr class="first" style="border: 1px solid black">
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['price'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['member_id'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['member_name'] ?>
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
        var url = "/channel/charge-num?export=on";
        window.location.href = url;
    }

    var refresh = document.getElementById('Refresh');
    refresh.onclick = function () {
        window.location.reload();
    }
</script>