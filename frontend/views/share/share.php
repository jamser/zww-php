<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
    <title>365线上抓娃娃365天抓不停！</title>
    <link rel="stylesheet" href="video11/css/style.css">
    <script>
        document.documentElement.style.fontSize=window.innerWidth/375*50+"px";
    </script>
</head>
<body>
<div class="wrap">
    <section>
        <div class="title">
            <img src="video11/images/title.png" alt="">
        </div>

        <div class="video">

            <a href="Javascript:;" class="pand"><img src="video11/images/view.png" alt=""></a>

          <div class="txt_pand" style="display: none;"><video src="./video11/images/share.mp4" controls="controls" autoplay="autoplay" style="width: 100%"></video></div>

        </div>
        <div class="play">
            <img src="video11/images/play.png" alt="">
        </div>
        <div class="content">
            <h5>
                <a href="Javascript:;">分享 · 邀请码<a>
            </h5>
            <h3>
                <a href="Javascript:;"><?php echo 123456 ?></a>
            </h3>
            <ul>
                <li>
                    <span></span>
                    邀请好友无上限
                </li>
                <li>
                    <span></span>
                    每天签到就送娃娃币
                </li>
                <li>
                    <span></span>
                    炒鸡萌萌哒的娃娃、易抓取、上新快
                </li>
            </ul>
            <div class="button">
<!--                --><?php
//                if($deviceType=='ios') {
//
//                } else {
//
//                }
//                ?>
                <button id="btn"><img src="video11/images/download.png" alt=""></button>
            </div>
        </div>
    </section>
</div>
</body>
<script>
</script>
</html>
<script src="js/jquery-3.1.1.min.js"></script>
<script>
    $(function(){
        $('.pand').click(function(){
            $(this).hide();
            $(".txt_pand").show();
        })
    })

    /***
     * 判断是苹果还是安卓
     * */
    $(document).on('click','#btn',function(){
//        alert(1111);return false;
        $.ajax({
            url:'?r=share/is-ios-android',
            type:'get',
//            async:false,
            success:function(msg){
                if(msg==2){
//                    window.location.href='https://itunes.apple.com/cn/app/365%E6%8A%93%E5%A8%83%E5%A8%83/id1314921684?mt=8';
                    window.location.replace("https://itunes.apple.com/cn/app/365%E6%8A%93%E5%A8%83%E5%A8%83/id1314921684?mt=8");
                }else if(msg==1){
//                    window.location.href='http://365zhuawawa.com/app/zhuawawa365.apk';
                    window.location.replace("http://a.app.qq.com/o/simple.jsp?pkgname=com.wanyiguo.zww365");
                }else{
                    alert('没有该类型的手机');
                }
            }
        })
    })
    /***
     * 邀请码
     * */
</script>