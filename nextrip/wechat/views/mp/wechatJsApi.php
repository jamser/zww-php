<?php
use WechatSdk\mp\Js;

/* @var $this \yii\web\View */

$mpKey = !empty($mpKey) ? $mpKey : 'nt1';
$debug = !empty($debug) ? true : false;
$url = !empty($url) ? $url : null;
$apis = !empty($apis) ? $apis : [
		'chooseImage',
		'previewImage',
		'uploadImage',
		'downloadImage',
		'getLocation',
		'getNetworkType',
		'onMenuShareTimeline',
		'onMenuShareAppMessage',
            
                'startRecord',
                'stopRecord',
                'onVoiceRecordEnd',
                'playVoice',
                'pauseVoice',
                'stopVoice',
                'onVoicePlayEnd',
                'uploadVoice',
                'downloadVoice',
            
		'hideMenuItems',
		'showMenuItems',
		'hideAllNonBaseMenuItem',
		'showAllNonBaseMenuItem',
	];

$wechatMpConfig = Yii::$app->params[$mpKey];
$js = new Js($wechatMpConfig['appId'], $wechatMpConfig['appSecret']);
$wxConfig = array_merge(array('debug' => $debug), $js->getSignaturePackage($url), array('jsApiList' => $apis, 'ticket'=>$js->getTicket()));
$this->beginBlock('wechatJsScript');
?>
wx.config({
    debug: <?php echo !$wxConfig['debug'] ? 'false' : 'true' ?>,
    appId: '<?php echo $wxConfig['appId']; ?>',
    timestamp: <?php echo $wxConfig['timestamp']; ?>,
    nonceStr: '<?php echo $wxConfig['nonceStr']; ?>',
    signature: '<?php echo $wxConfig['signature']; ?>',
    jsApiList: [
        "<?php echo implode('","', $wxConfig['jsApiList']) ?>"
    ]
});
wx.ready(function(){
    window.wxStatus = {
        appId:<?php echo $wxConfig['appId']; ?>,
        ready:true
    };
    <?php if(!empty($shareTimelineInfo)):?>
    wx.onMenuShareTimeline({
        title: '<?=addslashes(cutstr(str_replace(array("\n","\r\n","\t", "\r"), ' ', $shareTimelineInfo['title']),69))?>', // 分享标题
        link: <?=empty($shareTimelineInfo['url'])?'window.location.href':'"'.$shareTimelineInfo['url'].'"'?>, // 分享链接
        imgUrl: '<?=!empty($shareTimelineInfo['image'])?$shareTimelineInfo['image']:''?>', // 分享图标
        success: <?=!empty($shareTimelineInfo['success'])?$shareTimelineInfo['success']:'function() {}'?>,
        cancel: function() {}
    });
    <?php endif;?>
    <?php if(!empty($shareAppInfo)):?>
    wx.onMenuShareAppMessage({
        title: '<?=addslashes(cutstr(str_replace(array("\n","\r\n","\t", "\r"), ' ', $shareAppInfo['title']),69))?>', // 分享标题
        desc: '<?=!empty($shareAppInfo['description'])?addslashes(cutstr(str_replace(array("\n","\r\n","\t", "\r"), ' ', $shareAppInfo['description']),69)):''?>', // 分享描述
        link: <?=empty($shareAppInfo['url'])?'window.location.href':'"'.$shareAppInfo['url'].'"'?>, // 分享链接
        imgUrl: '<?=!empty($shareAppInfo['image'])?$shareAppInfo['image']:''?>', // 分享图标
        type: '', // 分享类型,music、video或link，不填默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
        success: <?=!empty($shareTimelineInfo['success'])?$shareTimelineInfo['success']:'function() {}'?>,
        cancel: function() {}
    });
    <?php endif;?>
});
wx.error(function(res){
    window.wxStatus = {
        appId:<?php echo $wxConfig['appId']; ?>,
        ready:false,
        error:true
    };
});
<?php
$this->endBlock();
$this->registerJsFile('//res.wx.qq.com/open/js/jweixin-1.0.0.js');
$this->registerJs($this->blocks['wechatJsScript']);
?>