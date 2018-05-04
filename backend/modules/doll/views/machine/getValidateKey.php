<?php 
$this->title = '获取机器密钥';
?>

<div class="form" style="margin:20px;">
    <div class="row" style="margin:10px;">
        <label class="control-label" >机器名称</label>
        <input type="" class="form-control" id="device_name"/>
    </div>
    
    <div class="row" id="result"  style="margin:10px;">
        
    </div>
    <button class="btn btn-default" id="btn-query">查询</button>
</div>


<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js" type="text/javascript"></script>
<script>
function getDeviceName() {
	return $('#device_name').val();
	
}

$(function(){
        
    $('#btn-query').click(function(){
            $.get('<?=\yii\helpers\Url::to('/doll/machine/get-validate-key')?>?device='+getDeviceName(),
            {},
            function(r) {
                $('#result').html("填写数据为："+r);
            });
    });
	
})
</script>

