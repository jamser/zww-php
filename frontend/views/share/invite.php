<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>邀请好友</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <link rel="stylesheet" type="text/css" href="css/public.css">
    <link rel="stylesheet" type="text/css" href="css/invite.css">
</head>
<body>
<img src="http://zww-image-prod.oss-cn-shanghai.aliyuncs.com/static/%E8%83%8C%E6%99%AF%E8%8A%B1%E7%BA%B9.png"
     style="position: fixed;width:100%;height: 100%;top: 0;left:0;z-index:1"/>
<div id="inviteBox" style="z-index: 2;position: relative">
    <br/>
    <br/>
    <div class="banner" style="text-align: center"><img src="http://zww-image-prod.oss-cn-shanghai.aliyuncs.com/static/%E6%A0%87%E9%A2%98.png"
                                                        width="88%"></div>
    <p style="text-align: center;color: palevioletred;font-size: 14px">我的邀请码：<?php echo $invite_code?></p>
    <p style="text-align: center;color: #117a8b;font-size: 7px;margin-top: 0.2rem;">快来分享给你的好友吧</p>
    <p style="text-align: center">
        <a onclick="callFriends()"><img src="http://zww-image-prod.oss-cn-shanghai.aliyuncs.com/static/%E5%BE%AE%E4%BF%A1%E5%A5%BD%E5%8F%8B.png" style="width: 50px;height: 50px"></a>
        &nbsp&nbsp&nbsp&nbsp
        <a onclick="callCircle()"><img src="http://zww-image-prod.oss-cn-shanghai.aliyuncs.com/static/%E6%9C%8B%E5%8F%8B%E5%9C%88.png" style="width: 50px;height: 50px"></a>
    </p>
    <p style="float: right;background: url(http://zww-image-prod.oss-cn-shanghai.aliyuncs.com/static/%E9%82%80%E8%AF%B7.png);width: 60px;height: 40px;font-size: 5px;text-align: center;color: #808080" >已邀请<br/><?php echo $invite_num?>人</p>

    <br/>
    <div class="titbox">
        <div class="imgbox">邀请规则</div>
    </div>
    <div class="textBox"><p>&gt;1 邀请好友下载365抓娃娃注册并登录，在设置里填写您的邀请码之后，双方立即各获得10个Hi币。</p>

        <p>&gt;2 您与您所邀请的好友均首次完成任意金额的充值之后，双方可再获得20个金币。</p>

        <p>&gt;3 每位用户邀请无上限，仅前10次分享可获得Hi币。</p></div>
    <p class="infoBottom">最终解释权归365抓娃娃所有</p></div>
<script type="text/javascript" src="http://zww-file.oss-cn-shanghai.aliyuncs.com/js/zepto.min.js"></script>
<script src="http://zww-file.oss-cn-shanghai.aliyuncs.com/js/public.js" type="text/javascript"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js" type="application/javascript"></script>
<script>
    function callFriends() {
        location.href = "shareToWXFriend://webview"
    }
    function callCircle() {
        location.href = "shareToWXCircle://webview"
    }

    var imgUrl = "http://zww-image-dev.oss-cn-shanghai.aliyuncs.com/238640877592622509.jpg";  //图片LOGO注意必须是绝对路径
    var lineLink = "http://p.365zhuawawa.com/?r=share/invite&memberId=".<?=$invite_code?>;   //网站网址，必须是绝对路径
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