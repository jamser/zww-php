<script type="text/javascript">
    //调用微信JS api 支付
    function jsApiCall()
    {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            <?php echo $jsApiParams; ?>,
            function(res){
                //WeixinJSBridge.log(res.err_msg);
                //alert(res.err_code+res.err_desc+res.err_msg);
                console.log("wechat pay ", res);
                if(res.err_msg == "get_brand_wcpay_request:ok" ) {
                     // 使用以上方式判断前端返回,微信团队郑重提示：res.err_msg将在用户支付成功后返回    ok，但并不保证它绝对可靠。 
                     window.location.href = "/pay/update-status?id=<?=$payId?>";
                } else {
                    weui.alert('微信支付失败');
                }
            }
        );
    }

    function callpay()
    {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall();
            }
    }
    <?php if($autopay):?>
        callpay();
    <?php endif;?>
</script>
