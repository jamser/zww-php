<?php
include_once '../aliyun-php-sdk-core/Config.php';
use \Iot\Request\V20170420 as Iot;
//设置你的AccessKeyId/AccessSecret/ProductKey
$accessKeyId = "LTAIiRG3VWVjAIpU";
$accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
$iClientProfile = DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
$client = new DefaultAcsClient($iClientProfile);

$request = new Iot\ApplyDeviceWithNamesRequest();
$request->setProductKey(1122226666);
$request->setDeviceName("DeviceName.1=device_a&DeviceName.3=device_c&DeviceName.2=device_b&DeviceName.4=device01");
$response = $client->getAcsResponse($request);
print_r("\r\n");
print_r($response);