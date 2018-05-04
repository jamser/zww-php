<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>365抓娃娃</title>
    <link href="https://res.wx.qq.com/open/libs/weui/1.1.0/weui.min.css" rel="stylesheet">
    <link href="../../web/vendors/icomoon/style.css" rel="stylesheet">
    <link href="../../web/css/m.css?v=12" rel="stylesheet">
    <script>
    //设置根字体大小
(function(designWidth, maxWidth) {
	var doc = document,
	win = window,
	docEl = doc.documentElement,
	remStyle = document.createElement("style"),
	tid;

	function refreshRem() {
		var width = docEl.getBoundingClientRect().width;
		maxWidth = maxWidth || 540;
		width>maxWidth && (width=maxWidth);
		var rem = width * 100 / designWidth;
		remStyle.innerHTML = 'html{font-size:' + rem + 'px;}';
	}

	if (docEl.firstElementChild) {
		docEl.firstElementChild.appendChild(remStyle);
	} else {
		var wrap = doc.createElement("div");
		wrap.appendChild(remStyle);
		doc.write(wrap.innerHTML);
		wrap = null;
	}
	//要等 wiewport 设置好后才能执行 refreshRem，不然 refreshRem 会执行2次；
	refreshRem();

	win.addEventListener("resize", function() {
		clearTimeout(tid); //防止执行两次
		tid = setTimeout(refreshRem, 300);
	}, false);

	win.addEventListener("pageshow", function(e) {
		if (e.persisted) { // 浏览器后退的时候重新计算
			clearTimeout(tid);
			tid = setTimeout(refreshRem, 300);
		}
	}, false);

	if (doc.readyState === "complete") {
		doc.body.style.fontSize = "16px";
	} else {
		doc.addEventListener("DOMContentLoaded", function(e) {
			doc.body.style.fontSize = "16px";
		}, false);
	}
})(750,750);

    </script>
</head>
    <body>
        <div class="container1" style="width:100%;height:100%">
            <img src="http://zww-image-prod.oss-cn-shanghai.aliyuncs.com/%E5%88%86%E4%BA%AB.png" alt="" width='100%'>
            <?php if($inviteCode): ?>
                <p class="invite-code" id="code"><?= yii\helpers\Html::encode($inviteCode)?></p>
            <?php endif;?>
            <button data-clipboard-action="copy" data-clipboard-target="#code" id="copy">点击复制</button>
        </div>
        <div class="hideContent">
        复制成功~快去发送给好友吧~
        </div>
    </body>
</html>
<script type="text/javascript" src="//zww-file.oss-cn-shanghai.aliyuncs.com/js/zepto.min.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js" type="application/javascript"></script>
<script src="https://cdn.jsdelivr.net/clipboard.js/1.5.12/clipboard.min.js"></script>
<script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.js"></script>
<script>
// $(function(){
//     $('.btn-play').on("touchstart",function(){
//         // $('#play-box').hide();
//         $('#video').css({'position':'fixed','z-index':'3','top':'0','left':'0'});
//         $('#video').removeClass('hidden');
//         // var video = document.getElementById("video");
//         // video.play();
//         $('#video')[0].play();
//     });
//     $('#video').on('touchstart',function(){
//         $('#video').addClass('hidden');
//     })
// });
$(document).ready(function(){    
    var targetText=$("#code").text();    
    var clipboard = new Clipboard('#copy');    

    clipboard.on('success', function(e) {    
        console.info('Action:', e.action);    
        console.info('Text:', e.text);    
        console.info('Trigger:', e.trigger);    
        // alert("复制成功");    
        e.clearSelection();    
    });    
}); 
$('#copy').on('touchend',function(){
    $('.hideContent').fadeIn(1000).fadeOut();
})
var imgUrl = "http://zww-image-dev.oss-cn-shanghai.aliyuncs.com/238640877592622509.jpg";  //图片LOGO注意必须是绝对路径
   //网站网址，必须是绝对路径
var descContent = '抓神来了，非战斗人员请迅速撤离 手机也能抓娃娃啦，365抓娃娃全网第一家激光瞄准，良心APP'; //分享给朋友或朋友圈时的文字简介
var shareTitle = '365抓娃娃';  //分享title
var appid = ''; //apiID，可留空

function shareFriend() {
    WeixinJSBridge.invoke('sendAppMessage',{
        "appid": appid,
        "imgurl": imgUrl,
        "img_width": "200",
        "img_height": "200",
        "link": lineLink,
        "desc": descContent,
        "title": shareTitle
    }, function(res) {
        //_report('send_msg', res.err_msg);
    })
}
function shareTimeline() {
    WeixinJSBridge.invoke('shareTimeline',{
        "imgurl": imgUrl,
        "img_width": "200",
        "img_height": "200",
        "link": lineLink,
        "desc": descContent,
        "title": shareTitle
    }, function(res) {
        //_report('timeline', res.err_msg);
    });
}
function shareWeibo() {
    WeixinJSBridge.invoke('shareWeibo',{
        "content": descContent,
        "url": lineLink
    }, function(res) {
        //_report('weibo', res.err_msg);
    });
}
// 当微信内置浏览器完成内部初始化后会触发WeixinJSBridgeReady事件。
document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
    // 发送给好友
    WeixinJSBridge.on('menu:share:appmessage', function(argv){
        shareFriend();
    });
    // 分享到朋友圈
    WeixinJSBridge.on('menu:share:timeline', function(argv){
        shareTimeline();
    });
    // 分享到微博
    WeixinJSBridge.on('menu:share:weibo', function(argv){
        shareWeibo();
    });
}, false);
</script>