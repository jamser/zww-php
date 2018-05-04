<?php 
/* @var $this \yii\web\View */
$this->title = '小机器控制测试';
?>
<label>机器名称</label>
<input type="text" value="devicea_" id="device_name" /><br/><br/>


<label>IP</label>
<input type="text" value="106.15.156.126" id="ip" /><br/><br/>

<button id="btn-init">初始化</button> <button id="btn-start">投币</button><br/><br/>
<button id="btn-forward">前</button>&nbsp;
<button id="btn-forward-stop">前停止</button><br/><br/>

<button id="btn-backward">后</button>&nbsp;
<button id="btn-backward-stop">后停止</button><br/><br/>

<button id="btn-left">左</button>&nbsp;
<button id="btn-left-stop">左停止</button><br/><br/>

<button id="btn-right">右</button>&nbsp;
<button id="btn-right-stop">右停止</button><br/><br/>

<button id="btn-stop">停止</button><br/><br/>

<button id="btn-strong-claw">强抓</button><br/><br/>
<button id="btn-weak-claw">弱抓</button><br/><br/>

<button id="btn-query">查询</button><br/><br/>

<!--
<button id="btn-get-validate-key">生成物联网模块信息</button><br/><br/>
-->
<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js" type="text/javascript"></script>
<script>
function getDeviceName() {
	return $('#device_name').val();
	
}
function getIp() {
	return $('#ip').val();
	
}
$(function(){
	
	$('#btn-init').click(function(){
		$.get('<?= \yii\helpers\Url::to('/doll/machine/send-control-command')?>?ip='+getIp()+'&content='+getDeviceName()+'|control|init');
	});
	$('#btn-forward').click(function(){
		$.get('<?=\yii\helpers\Url::to('/doll/machine/send-control-command')?>?ip='+getIp()+'&content='+getDeviceName()+'|control|forward');
	});
	$('#btn-forward-stop').click(function(){
		$.get('<?=\yii\helpers\Url::to('/doll/machine/send-control-command')?>?ip='+getIp()+'&content='+getDeviceName()+'|control|forwardStop');
	});
	
	$('#btn-backward').click(function(){
		$.get('<?=\yii\helpers\Url::to('/doll/machine/send-control-command')?>?ip='+getIp()+'&content='+getDeviceName()+'|control|backward');
	});
	$('#btn-backward-stop').click(function(){
		$.get('<?=\yii\helpers\Url::to('/doll/machine/send-control-command')?>?ip='+getIp()+'&content='+getDeviceName()+'|control|backwardStop');
	});
	
	$('#btn-left').click(function(){
		$.get('<?=\yii\helpers\Url::to('/doll/machine/send-control-command')?>?ip='+getIp()+'&content='+getDeviceName()+'|control|left');
	});
	$('#btn-left-stop').click(function(){
		$.get('<?=\yii\helpers\Url::to('/doll/machine/send-control-command')?>?ip='+getIp()+'&content='+getDeviceName()+'|control|leftStop');
	});
	
	$('#btn-right').click(function(){
		$.get('<?=\yii\helpers\Url::to('/doll/machine/send-control-command')?>?ip='+getIp()+'&content='+getDeviceName()+'|control|right');
	});
	$('#btn-right-stop').click(function(){
		$.get('<?=\yii\helpers\Url::to('/doll/machine/send-control-command')?>?ip='+getIp()+'&content='+getDeviceName()+'|control|rightStop');
	});
	
	$('#btn-stop').click(function(){
		$.get('<?=\yii\helpers\Url::to('/doll/machine/send-control-command')?>?ip='+getIp()+'&content='+getDeviceName()+'|control|stop');
	});
	
	
	$('#btn-strong-claw').click(function(){
		$.get('<?=\yii\helpers\Url::to('/doll/machine/send-control-command')?>?ip='+getIp()+'&content='+getDeviceName()+'|control|strongClaw');
	});
	
	$('#btn-weak-claw').click(function(){
		$.get('<?=\yii\helpers\Url::to('/doll/machine/send-control-command')?>?ip='+getIp()+'&content='+getDeviceName()+'|control|weakClaw');
	});
        
        $('#btn-query').click(function(){
		$.get('<?=\yii\helpers\Url::to('/doll/machine/send-control-command')?>?ip='+getIp()+'&content='+getDeviceName()+'|control|query');
	});
	
	$('#btn-start').click(function(){
		//$.get('<?=\yii\helpers\Url::to('/doll/machine/send-control-command')?>?ip='+getIp()+'&content='+getDeviceName()+'|control|init');
		$.get('<?=\yii\helpers\Url::to('/doll/machine/send-control-command')?>?ip='+getIp()+'&content='+getDeviceName()+'|control|coin');
	});
        $('#btn-get-validate-key').click(function(){
            $.get('<?=\yii\helpers\Url::to('/doll/machine/send-control-command')?>?ip='+getIp()+'&content='+getDeviceName()+'|control|coin');
        })
})
</script>