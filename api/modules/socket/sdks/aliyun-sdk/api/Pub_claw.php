<!--发布消息到Topic,抓接口-->
<?php
include_once '../aliyun-php-sdk-core/Config.php';
use \Iot\Request\V20170420 as Iot;
//设置你的AccessKeyId/AccessSecret/ProductKey
$accessKeyId = "LTAIiRG3VWVjAIpU";
$accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
$iClientProfile = DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
$client = new DefaultAcsClient($iClientProfile);

$request = new Iot\PubRequest();
$request->setProductKey("gbHwjqaIekS");
$request->setTopicFullName("/gbHwjqaIekS/device_001/get"); //消息发送到的Topic全名.
$request->setMessageContent(base64_encode("{'control':'claw'}"));
$request->setQos(0);
$response = $client->getAcsResponse($request);
$arr = object_to_array($response);
print_r(json_encode($response));

function object_to_array($obj){
    if(is_array($obj)){
        return $obj;
    }
    $_arr = is_object($obj)? get_object_vars($obj) :$obj;
    foreach ($_arr as $key => $val){
        $val=(is_array($val)) || is_object($val) ? object_to_array($val) :$val;
        $arr[$key] = $val;
    }

    return $arr;

}