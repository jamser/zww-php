<!--发消息给设备并同步返回响应-->
<?php
include_once '../aliyun-php-sdk-core/Config.php';
use \Iot\Request\V20170420 as Iot;
//设置你的AccessKeyId/AccessSecret/ProductKey
$accessKeyId = "LTAIiRG3VWVjAIpU";
$accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
$iClientProfile = DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
$client = new DefaultAcsClient($iClientProfile);

$request = new Iot\RRpcRequest();
$request->setProductKey("gbHwjqaIekS");
$request->setDeviceName("device_001");
$request->setRequestBase64Byte(base64_encode("{'control':'coin'}"));
$request->setTimeout(5000);
$response = $client->getAcsResponse($request);
print_r($response);