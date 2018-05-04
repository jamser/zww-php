(function() {
    function ajax(Url, successFn, failureFn) {
        //1.创建XMLHttpRequest对象
        var xhr = null;
        if (window.XMLHttpRequest) {
            xhr = new XMLHttpRequest();
        } else {
            xhr = new ActiveXObject('Microsoft.XMLHTTP');
        }
        //2.打开与服务器的链接
        xhr.open('get', Url, true);
        //3.发送给服务器
        xhr.send(null);
        //4.响应就绪
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    if (successFn) {
                        successFn(xhr.responseText);
                    }
                } else {
                    if (failureFn) {
                        failureFn();
                    } else {
                        console.log(xhr.status);
                    }
                }
            }
        }
    }

  

    function getScript(source, callback) {
        var scriptElement = document.querySelector('script[src="' + source + '"]')
        if (scriptElement) {
            scriptElement.parentElement.removeChild(scriptElement)
        }
        setTimeout(function() {
            var script = document.createElement('script');
            var prior = document.getElementsByTagName('script')[0];
            script.async = 1;

            script.onload = script.onreadystatechange = function(_, isAbort) {
                if (isAbort || !script.readyState || /loaded|complete/.test(script.readyState)) {
                    script.onload = script.onreadystatechange = null;
                    script = undefined;

                    if (!isAbort) { if (callback) callback(); }
                }
            };

            script.src = source;
            prior.parentNode.insertBefore(script, prior);
        }, 0)

    }
    var setShareData = function(data) {
        if (!/micromessenger/i.test(navigator.userAgent)) {
            return
        }
        var shareData={}
        for(var index in data){
            if(data.hasOwnProperty(index)){
                shareData[index]=data[index]
            }
        }
        shareData.title=data.title ? data.title : '12123123';
        shareData.summary=data.summary ? data.summary : '3213123';
        shareData.pic=data.pic ?  data.pic :  'share_default.jpg';
        shareData.url=data.url ? data.url : location.href;
        var url = encodeURIComponent(decodeURIComponent(location.href.split('#')[0]))
        ajax('//m.paomianfan.com/api/user/getWXSignPackage?url=' + url, function(data) {
            var res = JSON.parse(data)
            if (res && res.data) {
                shareData.WXconfig = res.data
                getScript('/resource/lib/share.js'/*'//qzonestyle.gtimg.cn/qzone/qzact/common/share/share.js'*/, function() {
                    // console.error(shareData);
                    setShareInfo(shareData)

                })
            }
        })
    }
    window.ajax=ajax;
    window.setShareData=setShareData;
})()