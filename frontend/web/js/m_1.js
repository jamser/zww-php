
var PayModule = (function (global){
    var orderId, multiOrderIds, payParams, multiPayParams, loading;
    
    var module = {
        payCallback:null,
        //设置支付回调结果
        setPayCallback : function(callback) {
            this.payCallback = callback;
            return this;
        },
        //设置支付参数
        setPayParams : function(jsApiPayParams) {
            payParams = jsApiPayParams;
            return this;
        },
        //获取参数
        getPayParams : function(id, callback) {
            
            if(payParamsLoading) {
                return false;
            }
            if(id===orderId && payParams) {
                callback(id, payParams);
                return false;
            } else {
                //显示正在加载
                payParamsLoading = true;
                loading = weui.loading('loading');
            }
            $.ajax({
                url: '/call/order/pay-params',
                type: 'GET',
                data:{
                    id:id
                },
                dataType: 'json',
                success: function (r) {
                    payParamsLoading = false;
                    loading.hide();
                    if (r.code===0) {
                        callback(id, r.result.payParams);
                    } else {
                        weui.alert(r.msg);
                    }
                },
                error: function () {
                    weui.alert('网络连接错误..');
                    payParamsLoading = false;
                    loading.hide();
                }
            });
        },
        //支付 参数为支付ID
        pay:function(payId) {
            
        },
        //支付订单
        payOrder: function(id, orderPayParams) {
            orderId = id;
            if(!orderPayParams) {
                this.getPayParams(id, function(id,orderPayParams){
                    orderId = id;
                    payParams = orderPayParams;
                    PayModule.callWxPay();
                });
            } else {
                payParams = orderPayParams;
                PayModule.callWxPay();
            }
        },
        //获取参数
        getMultiPayParams : function(ids, callback) {
            
            if(payParamsLoading) {
                return false;
            }
            if(ids===multiOrderIds && multiPayParams) {
                callback(ids, multiPayParams);
                return false;
            } else {
                //显示正在加载
                payParamsLoading = true;
                loading = weui.loading('loading');
            }
            $.ajax({
                url: '/call/order/multi-pay-params',
                type: 'GET',
                data:{
                    ids:ids
                },
                dataType: 'json',
                success: function (r) {
                    payParamsLoading = false;
                    loading.hide();
                    if (r.code===0) {
                        callback(ids, r.result.payParams);
                    } else {
                        weui.alert(r.msg);
                    }
                },
                error: function () {
                    weui.alert('网络连接错误..');
                    payParamsLoading = false;
                    loading.hide();
                }
            });
        },
        //支付订单
        multiPayOrder: function(ids, orderPayParams) {
            multiOrderIds = ids;
            if(!multiOrderPayParams) {
                this.getMultiPayParams(ids, function(ids,orderPayParams){
                    multiOrderIds = ids;
                    multiOrderPayParams = orderPayParams;
                    PayModule.callWxPay();
                });
            } else {
                multiOrderPayParams = orderPayParams;
                PayModule.callWxPay();
            }
        },
        
        //调用微信JS api 支付
        wxPayJsApiCall : function ()
        {
            WeixinJSBridge.invoke(
                    'getBrandWCPayRequest',
                    payParams,
                    function (res) {
                        if(!PayModule.payCallback) {
                            if (res.err_msg === "get_brand_wcpay_request:ok") {
                                $.get('/pay/update-status',{id:orderId});
                                showPaySuccess(orderId);
                                $('#btn_pay_'+orderId).remove();
                            } else {
                                console.log('微信支付失败', res);
                                alert('微信支付失败 '+ (res.err_desc ? ' : '+res.err_desc : ''));
                            }
                        } else {
                            PayModule.payCallback(orderId,res);
                        }

                    }
            );
        },
        callWxPay : function ()
        {
            if (typeof WeixinJSBridge === "undefined") {
                if (document.addEventListener) {
                    document.addEventListener('WeixinJSBridgeReady', PayModule.wxPayJsApiCall, false);
                } else if (document.attachEvent) {
                    document.attachEvent('WeixinJSBridgeReady', PayModule.wxPayJsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', PayModule.wxPayJsApiCall);
                }
            } else {
                this.wxPayJsApiCall();
            }
        }
    };
    
    return module;
})(window);