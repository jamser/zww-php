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
                if(res.err_msg == "get_brand_wcpay_request:success" ) {
                     //支付成功 跳转到订单
                     window.location.href = "/call/order/view?id=<?=$order->id?>";
                } else {
                    alert('微信支付失败 : ' + res.err_code+res.err_desc+res.err_msg);
                    //window.location.href = "/trade/order/view?id=<?=$order->id?>";
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
    callpay();
    </script>
