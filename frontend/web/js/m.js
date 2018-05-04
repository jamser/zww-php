
var PayModule = (function (global){
    var _payId,_payParams,_useDefaultCallback=true, loading, payParamsLoading;
    
    var module = {
        payCallback:null,
        //设置支付回调结果
        setPayCallback : function(callback, useDefaultCallback) {
            this.payCallback = callback;
            _useDefaultCallback = useDefaultCallback ? true : false;
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
            if(id===_payId && _payParams) {
                callback(id, _payParams);
                return false;
            } else {
                //显示正在加载
                payParamsLoading = true;
                loading = weui.loading('loading');
            }
            $.ajax({
                url: '/api/pay/pay-params',
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
        pay:function(payId, params) {
            _payId = payId;
            if(!params) {
                this.getPayParams(_payId, function(payId,params){
                    _payId = payId;
                    _payParams = params;
                    PayModule.callWxPay();
                });
            } else {
                _payParams = params;
                PayModule.callWxPay();
            }
        },
        //调用微信JS api 支付
        wxPayJsApiCall : function ()
        {
            WeixinJSBridge.invoke(
                    'getBrandWCPayRequest',
                    _payParams,
                    function (res) {
                        if (res.err_msg === "get_brand_wcpay_request:ok") {
                            payResult = true;
                            PayModule.updatePayStatus(_payId);
                        } else {
                            var payResult = false;
                            console.log('微信支付失败', res);
                            if(_useDefaultCallback) {
                                weui.alert('调用微信支付失败:'+res.err_desc);
                            }
                        }
                        if(PayModule.payCallback) {
                            PayModule.payCallback(_payId, payResult, res);
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
        },
        updatePayStatus(id) {
            loading = weui.loading('loading');
            $.ajax({
                url:'/api/pay/update-status',
                data:{
                    id:id
                },
                type:'GET',
                dataType:'json',
                success:function(r) {
                    loading.hide();
                    if(r.code===0) {
                        console.log('更新支付状态成功');
                    } else {
                        weui.alert(r.msg);
                    }
                },
                error:function() {
                    loading.hide();
                    //weui.alert('网络连接错误');
                }
                
            });
                            $.get('/pay/update-status',{id:_payId}, function(r){});
        }
    };
    
    return module;
})(window);


var SmsCodeModule = (function (global){
    var module= {
        sendCodeTimeout:0,
        timer:function() {
            var that = this;
            if(that.sendCodeTimeout>0) {
                $('#btn_send_code').html(that.sendCodeTimeout+'秒重发').attr('disabled', true);
                 setTimeout(function(){
                    that.sendCodeTimeout--;
                    that.timer();
                },1000);
            } else {
                $('#btn_send_code').html('获取验证码').removeAttr('disabled');
            }
        },
        sendCode:function() {
            var phoneNum = $('#po_phone_num').val(); 
            if(!(/^1[3|4|5|7|8]\d{9}$/.test(phoneNum))){ 
                FlashMsg.error("手机号码有误，请重填");  
                return false; 
            }
            if(this.sendCodeTimeout<=0) {
                //允许发送
                $('#btn_send_code').attr('disabled', true).html('发送中..');
                var that = this;
                $.ajax({
                    url:'/apiv1/common/send-sms-code',
                    data:{
                        _csrf:csrfToken,
                        phoneNum:phoneNum,
                        type:'tripContact'
                    },
                    type:'POST',
                    dataType:'json',
                    success:function(r) {
                        if(isResponseOk(r)) {
                            that.sendCodeTimeout = 60;
                            $('#btn_send_code').attr('disabled', true);
                            that.timer();
                        } else {
                            FlashMsg.error(r.msg);
                            $('#btn_send_code').removeAttr('disabled').html('获取验证码');
                        }
                    },
                    error:function(r) {
                        FlashMsg.error('网络连接出错了...请重试');
                        $('#btn_send_code').removeAttr('disabled').html('获取验证码');
                    },
                });
            }
        },
    }
    return module;
});