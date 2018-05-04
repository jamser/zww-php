<?php

/**
 * 替换指定的字符串 $limit 次
 * @param string $find 查找的字符
 * @param string $replacement 替换的字符串
 * @param string $subject 目标字符串
 * @param integer $limit 替换的次数 0为替换全部
 */
function strReplaceLimit($find, $replacement, $subject, $limit = 0) {
    if ($limit == 0) {
        return str_replace($find, $replacement, $subject);
    } else {
        $ptn = '/' . preg_quote($find) . '/';
        return preg_replace($ptn, $replacement, $subject, $limit);
    }
}

/**
 * 产生一个随机字符串
 * @param int $length
 * @return string
 */
function randStr($length) {
    $codeRand = "0123456789asdfghjklmyuiopqwertnbvcxzASDFGHJKLMYUIOPQWERTNBVCXZ";
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $key = rand(0, 61);
        $string .=$codeRand[$key];
    }
    return $string;
}

/**
 * 产生一个随机数字字符串
 * @param int $length
 * @return string
 */
function randNumStr($length) {
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $key = rand(0, 9);
        $string .= $key;
    }
    return $string;
}

/**
 * 剪裁字符串
 * @param string $string 要剪裁的字符串
 * @param integer $length 长度
 * @param string $dot 省略符
 * @return string
 */
function cutstr($string, $length, $dot = ' ...') {
    $charset = 'utf-8';
    if (strlen($string) <= $length) {
        return $string;
    }

    $pre = chr(1);
    $end = chr(1);

    $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end), $string);
    //$length = $length - ($oriStrLen - $repStrLen);

    $strcut = '';
    if (strtolower($charset) == 'utf-8') {

        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {

            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t <= 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }

            if ($n >= $length) {
                break;
            }
        }
        if ($n > $length) {
            $n -= $tn;
        }

        $strcut = substr($string, 0, $n);
    } else {
        for ($i = 0; $i < $length; $i++) {
            $strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
        }
    }

    $strcut = str_replace(array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

    $pos = strrpos($strcut, chr(1));
    if ($pos !== false) {
        $strcut = substr($strcut, 0, $pos);
    }
    return $strcut . $dot;
}

/**
 * 循环生成路径
 * @param string $pathname 路径名称
 * @param integer $mode 权限
 */
function mkdirs($pathname, $mode = 0755) {
    is_dir(dirname($pathname)) || mkdirs(dirname($pathname), $mode);
    return is_dir($pathname) || @mkdir($pathname, $mode);
}

/**
 * 将 br 转换为 \n
 * @param string $text
 * @return string
 */
function br2nl($text) {
    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $text);
}

/**
 * 检测字符串是否由纯英文，纯中文，中英文混合组成
 * @param string
 * @return en:纯英文;zh:纯中文;mix:中英文混合
 */
function checkStrPart($str = '') {
    if (trim($str) == '') {
        return '';
    }
    $m = mb_strlen($str, 'utf-8');
    $s = strlen($str);
    if ($s == $m) {
        return 'en';
    }
    if ($s % $m == 0 && $s % 3 == 0) {
        return 'zh';
    }
    return 'mix';
}


/**
 * 判断是否微信浏览器
 * @reutrn bool
 */
function isWeixinBrowser() {
    if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    }
    return false;
}

/**
 * 获取访问设备类型
 * @reutrn string ios/android/unknow
 */
function getAgentDeviceType() {
    $httpUserAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if(stripos($httpUserAgent, 'iphone')!==false || stripos($httpUserAgent, 'ipad')!==false || stripos($httpUserAgent, 'ios')!==false){
        $deviceType = 'ios';
    } else if(stripos($httpUserAgent, 'android')!==false){
        $deviceType = 'android';
    } else {
        $deviceType = 'unknow';
    }
    return $deviceType;
}

/**
 * 根据两点间的经纬度计算距离
 * @param float $lat 纬度值
 * @param float $lng 经度值
 */
function getDistance($lat1, $lng1, $lat2, $lng2) {
    //将角度转为狐度
    $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
    $radLat2 = deg2rad($lat2);
    $radLng1 = deg2rad($lng1);
    $radLng2 = deg2rad($lng2);
    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;
    $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
    return $s;
}

/**
 * 过滤GET参数里的amp;
 * @param [] $params
 * @return []
 */
function filterParamsAmp($params) {
    if ($params) {
        foreach ($params as $key => $value) {
            if (strpos($key, 'amp;') === 0) {
                $key = substr($key, 4);
                $params[$key] = $value;
            }
        }
    }
    return $params;
}

/**
 * 获取一个规则的域名
 * @param string $url URL链接
 * @return string 不带 http:// , https:// 以及 / 的域名信息
 */
function getDomain($url) {
    $parse = parse_url($url);
    return $parse['host'];
}

function gmtIso8601($time) {
    $dtStr = date("c", $time);
    $mydatetime = new DateTime($dtStr);
    $expiration = $mydatetime->format(DateTime::ISO8601);
    $pos = strpos($expiration, '+');
    $expiration = substr($expiration, 0, $pos);
    return $expiration."Z";
}

/**
 * 获取笛卡尔积 接受多个数组参数  生成这多个数组的笛卡尔积
 **/
function descartes() {
    $t = func_get_args();                                    // 获取传入的参数
    if(func_num_args() == 1) {                               // 判断参数个数是否为1
        $ret = [];
        foreach($t[0] as $row) {
            $ret[] = [$row];
        }
        return $ret;
        //return call_user_func_array( __FUNCTION__, [$t[0]] );  // 回调当前函数，并把第一个数组作为参数传入
    }

    $a = array_shift($t);        // 将 $t 中的第一个元素移动到 $a 中，$t 中索引值重新排序
    if( !is_array($a) ) {
        $a = array($a);
    }

    $a = array_chunk($a, 1);     // 分割数组 $a ，为每个单元1个元素的新数组
    do {
        $r = array();
        $b = array_shift($t);
        if( !is_array($b) ) {
            $b = array($b);
        }
        foreach($a as $p) {
            foreach(array_chunk($b, 1) as $q) {
                $r[] = array_merge($p, $q);
            }
        }
       $a = $r;
    } while($t);
    
    return $r;
}


/**
 * 判断是否手机浏览器
 * @return bool 
 **/
function isMobile() {
    $useragent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
    {
        return true;
    } else {
        return false;
    }
}

/**
 * 获取设备类型 
 * @return string android/ios/空字符串
 */
function getDeviceType() {
    $deviceType = '';
    $userGgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if(strpos($userGgent, 'ipad')!==false 
            || strpos($userGgent, 'ipod')!==false 
            || strpos($userGgent, 'iphone')!==false 
    ) {
        $deviceType = 'ios';
    } else if(strpos($userGgent, 'android')!==false ) {
        $deviceType = 'android';
    }
    return $deviceType;
}

function getBrowser() {
    $agent = $_SERVER["HTTP_USER_AGENT"];
    if (strpos($agent, 'MSIE') !== false || strpos($agent, 'rv:11.0')) //ie11判断
        return "ie";
    else if (strpos($agent, 'Firefox') !== false)
        return "firefox";
    else if (strpos($agent, 'Chrome') !== false)
        return "chrome";
    else if (strpos($agent, 'Opera') !== false)
        return 'opera';
    else if ((strpos($agent, 'Chrome') == false) && strpos($agent, 'Safari') !== false)
        return 'safari';
    else
        return 'unknown';
}

/**
 * 获取浏览器版本号
 * @return string
 */
function getBrowserVer() {
    if (empty($_SERVER['HTTP_USER_AGENT'])) {    //当浏览器没有发送访问者的信息的时候
        return 'unknow';
    }
    $agent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/MSIE\s(\d+)\..*/i', $agent, $regs))
        return $regs[1];
    elseif (preg_match('/FireFox\/(\d+)\..*/i', $agent, $regs))
        return $regs[1];
    elseif (preg_match('/Opera[\s|\/](\d+)\..*/i', $agent, $regs))
        return $regs[1];
    elseif (preg_match('/Chrome\/(\d+)\..*/i', $agent, $regs))
        return $regs[1];
    elseif ((strpos($agent, 'Chrome') == false) && preg_match('/Safari\/(\d+)\..*$/i', $agent, $regs))
        return $regs[1];
    else
        return 'unknow';
}

/**
 * 获取微信版本号
 * @return type
 */
function getWechatVersion() {
    $version = null;
    $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if (preg_match('/MicroMessenger\/(\d+)\..*/i', $agent, $regs)) {
        $version = $regs[1];
    }
    return $version;
}


/**
 * 获取URL的文件扩展名
 * @param string $url
 * @return string
 */
function getUrlExt($url) {
    $pathParts = explode('.', parse_url($url, PHP_URL_PATH));
    return strtolower(array_pop($pathParts));
}

/**
 * 获取当前的IP
 * @return string
 */
function getIp() {
    $onlineip = '';
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $onlineip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $onlineip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $onlineip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $onlineip = $_SERVER['REMOTE_ADDR'];
    }
    return $onlineip;
}