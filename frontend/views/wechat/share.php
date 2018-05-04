<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>微信分享</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <script src="js/jquery-3.1.1.min.js" type="text/javascript"></script>
</head>
<body>
<a href="showsharetowechatalet://webview"><img src="http://zww-image-prod.oss-cn-shanghai.aliyuncs.com/%E5%88%86%E4%BA%AB%E6%8C%89%E9%92%AE@2x.png" width="150px" height="50px"></a>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js" type="application/javascript"></script>
<script>
        function callFriends() {
        location.href = "shareToWXFriend://webview"
        }
        function callCircle() {
        location.href = "shareToWXCircle://webview"
        }

        var imgUrl = "http://zww-image-dev.oss-cn-shanghai.aliyuncs.com/238640877592622509.jpg";  //图片LOGO注意必须是绝对路径
        var lineLink = "http://p.365zhuawawa.com/?r=share/invite&memberId="//.<?=$invite_code?>;   //网站网址，必须是绝对路径
        var descContent = '天上不掉馅饼，但是掉娃娃吖 足不出户，躺在床上玩手机抓娃娃，下载就送60币，抓到包邮送到家，快来体验吧~'; //分享给朋友或朋友圈时的文字简介
        var shareTitle = '365抓娃娃';  //分享title
        var appid = ''; //apiID，可留空

        function shareFriend() {
        WeixinJSBridge.invoke('sendAppMessage',{
        "appid": appid,
        "img_url": imgUrl,
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
        "img_url": imgUrl,
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
</body>
</html>