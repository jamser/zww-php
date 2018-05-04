/**
 * 笛卡儿积组合
 * @param {array} list 列表
 * @return {array} 笛卡儿积数组
 */
function descartes(list)
{
    //parent上一级索引;count指针计数
    var point  = {};

    var result = [];
    var pIndex = null;
    var tempCount = 0;
    var temp   = [];

    //根据参数列生成指针对象
    for(var index in list)
    {
        if(typeof list[index] === 'object')
        {
            point[index] = {'parent':pIndex,'count':0};
            pIndex = index;
        }
    }

    //单维度数据结构直接返回
    if(pIndex === null)
    {
        return list;
    }

    //动态生成笛卡尔积
    while(true)
    {
        
        for(index in list)
        {
            tempCount = point[index].count;
            temp.push(list[index][tempCount]);
        }

        //压入结果数组
        result.push(temp);
        temp = [];

        //检查指针最大值问题
        while(true)
        {
            if(point[index].count+1 >= list[index].length)
            {
                point[index].count = 0;
                pIndex = point[index].parent;
                if(pIndex === null)
                {
                    return result;
                }

                //赋值parent进行再次检查
                index = pIndex;
            }
            else
            {
                point[index].count++;
                break;
            }
        }
    }
}

/**
 * 判断是否在数组当中
 * @param {string} value 要判断的值
 * @param {array} arr 查询的数组
 * @return {bool} 
 */
function inArray(value,arr) {
    var ret = false;
    for(var k in arr) {
        if(arr[k]===value) {
            return true;
        }
    }
    return ret;
}

function time() {
    var date=new Date();
    var time=date.getTime().toString();
    return parseInt(time.substring(0,time.length-3));
}

function formatTime (timestamp, now) {
	var dur = now - timestamp;
	if(dur < 60){
		return dur+' 秒前';
	}else if(dur < 3600){
		return parseInt(dur/60)+' 分钟前';
	}else if(dur < 86400){
		return parseInt(dur/3600)+' 小时前';
	}else if(dur < 2592000){
		return parseInt(dur/86400)+' 天前';
	}else{
            var s = new Date(timestamp*1000);
            return (s.getMonth()+1)+"月"+s.getDate()+"日";
	}
}

function formatDate(fmt, date) {
    var o = {
        "M+": date.getMonth() + 1, //月份 
        "d+": date.getDate(), //日 
        "h+": date.getHours(), //小时 
        "m+": date.getMinutes(), //分 
        "s+": date.getSeconds(), //秒 
        "q+": Math.floor((date.getMonth() + 3) / 3), //季度 
        "S": date.getMilliseconds() //毫秒 
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o) {
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length === 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    }
    return fmt;
}

/**
 * 判断是否微信浏览器
 * @reutrn {bool}
 */
function isWeixinBrowser() {
    var u = navigator.userAgent;
    return u && u.indexOf('MicroMessenger')>=0;
}


/**
 * 获取设备类型 
 * @return {string} android/ios/空字符串
 */
function getDeviceType() {
    var u = navigator.userAgent;
    var deviceType = '';
    if(u && ( u.indexOf('Android') > -1 || u.indexOf('Adr') > -1)) {
        deviceType = 'android';
    } else if(u && (!!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/))) {
        deviceType = 'ios';
    }
    return deviceType;
}

/**
 * 获取浏览器信息
 * @return {object} 空的时候返回 {type:null,version:null} 否则返回 如 {type: "chrome", version: "48.0.2564.23"}
 */
function getBrowserInfo() {
    var ua = navigator.userAgent.toLowerCase();
    var s;
    return ((s = ua.match(/msie ([\d.]+)/)) ? {type:'ie',version:s[1]} :
            (s = ua.match(/firefox\/([\d.]+)/)) ? {type:'firefox',version:s[1]} :
            (s = ua.match(/chrome\/([\d.]+)/)) ? {type:'chrome',version:s[1]} :
            (s = ua.match(/opera.([\d.]+)/)) ? {type:'opera',version:s[1]} :
            (s = ua.match(/version\/([\d.]+).*safari/)) ? {type:'safari',version:s[1]} : {type:null,version:null});

}

/**
 * 判断请求是否OK
 * @param {object} res
 * @returns {Boolean}
 */
function isResponseOk(res) {
    return !!(res.code===0 || res.code==='0|OK');
}

/**
 * 是否手机号
 * @param {type} str 手机号
 * @returns {Boolean}
 */
function checkMobile(str) {
    var re = /^1\d{10}$/;
    return re.test(str);
}