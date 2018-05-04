<?php
$this->title = '渠道管理列表';
/* @var $this \yii\web\View */
backend\assets\DatePickerAsset::register($this);
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>
<div class="erp-order-list">
    <div class="form mb20">
        <form action="<?= yii\helpers\Url::to(['/channels/index'])?>" method="get" class="form-inline">
            <div class="mb10">
                <div class="form-group">
                    <label class="sr-only" for="userId">用户ID</label>
                    <input type="text" class="form-control" id="receiveUser" placeholder="输入用户ID"  name="memberID" value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('memberID'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="userId">用户名</label>
                    <input type="text" class="form-control" id="receiveUser" placeholder="输入用户名"  name="name" value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('name'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="mobile">手机号</label>
                    <input type="text" class="form-control" id="mobile" placeholder="输入手机号"  name="mobile" value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('mobile'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="startTime">开始时间</label>
                    <input type="text" class="form-control" id="startTime" name="startTime" placeholder="输入注册开始时间"
                           data-date-format="yyyy-mm-dd hh:ii:ss"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('startTime'))?>">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="endTime">结束时间</label>
                    <input type="text" class="form-control" id="endTime" name="endTime" placeholder="输入注册结束时间"
                           data-date-format="yyyy-mm-dd hh:ii:ss"  value="<?= yii\helpers\Html::encode(Yii::$app->getRequest()->get('endTime'))?>">
                </div>
                <div class="form-group">
                    <select class="form-control" id="last_login_from" name="last_login_from">
                        <option value="设备">设备</option>
                        <option value="android" <?='android'==Yii::$app->getRequest()->get('last_login_from')?'selected':''?>>android</option>
                        <option value="ios" <?='ios'==Yii::$app->getRequest()->get('last_login_from')?'selected':''?>>ios</option>
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
    <div class="head" style="width: auto;height: 40px;background-color: #b8c7ce;text-align: center;font-size: 16px;color: #696969">
        <b style="float: left;margin-top: 10px">注册用户：<?php echo $count; ?></b>
    </div>
    <br/>
    <div class="col-lg-12">
        <table class="table table-bordered table-striped table-hover" style="border: 1px solid black">
            <thead>
            <tr>
                <th class="span3 sortable" style="border: 1px solid black">
                    <span class="line"></span>登录渠道号
                </th>
                <th class="span3 sortable" style="border: 1px solid black">
                    <span class="line"></span>注册渠道号
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>id
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>用户id
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>昵称
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>电话
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>性别
                </th>
                <th class="span3 sortable align-right" style="border: 1px solid black">
                    <span class="line"></span>机型
                </th>
                <th class="span3 sortable" style="border: 1px solid black">
                    <span class="line"></span>注册时间
                </th>
                <th class="span3 sortable" style="border: 1px solid black">
                    <span class="line"></span>最近登录时间
                </th>
                <th class="span3 sortable" style="border: 1px solid black">
                    <span class="line"></span>是否在线
                </th>
                <th class="span3 sortable" style="border: 1px solid black">
                    <span class="line"></span>设备
                </th>
                <th class="span3 sortable" style="border: 1px solid black">
                    <span class="line"></span>抓取记录
                </th>
                <th class="span3 sortable" style="border: 1px solid black">
                    <span class="line"></span>充值记录
                </th>
            </tr>
            </thead>
            <tbody id="content" style="border: 1px solid black">
            <?php foreach ($rows as $value) { ?>
                <tr class="first" style="border: 1px solid black">
                    <td class="align-right" style="border: 1px solid black">
                        <?php
                        if(empty($value['login_channel'])){
                            echo "未标识渠道";
                        }else{
                            echo $value['login_channel'];
                        }
                        ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php
                        if(empty($value['register_channel'])){
                            echo "未标识渠道";
                        }else{
                            echo $value['register_channel'];
                        }
                        ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['id'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['memberID'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['name'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['mobile'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php
                        if($value['gender'] == 'f'){
                            echo "女";
                        }elseif($value['gender'] == 'm'){
                            echo "男";
                        }else{
                            echo '不明';
                        }
                        ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['phone_model'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['register_date'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php echo $value['last_login_date'] ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <button type="submit" class="btn btn-success"><?php
                            if($value['online_flg'] == 1){
                                echo "在线";
                            }else{
                                echo "离线";
                            }
                            ?></button>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?php
                        if($value['last_login_from'] == 'android'){
                            echo "android";
                        }else{
                            echo "ios";
                        }
                        ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?= Html::a('查看', ['channels/info','id' => $value['id']],['class' => 'btn btn-primary btn-info']) ?>
                    </td>
                    <td class="align-right" style="border: 1px solid black">
                        <?= Html::a('查看', ['channels/catch','id' => $value['id']],['class' => 'btn btn-primary btn-info']) ?>
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
        var url = "/channels/index?export=on";
        window.location.href = url;
    }

    var refresh = document.getElementById('Refresh');
    refresh.onclick = function () {
        window.location.reload();
    }
</script>