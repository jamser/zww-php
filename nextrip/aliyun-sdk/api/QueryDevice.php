<!--查询产品的设备列表-->
<?php
include_once '../aliyun-php-sdk-core/Config.php';
use \Iot\Request\V20170420 as Iot;
//设置你的AccessKeyId/AccessSecret/ProductKey
$accessKeyId = "LTAIiRG3VWVjAIpU";
$accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
$iClientProfile = DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessSecret);
$client = new DefaultAcsClient($iClientProfile);

$request = new Iot\QueryDeviceRequest();
$request->setProductKey("gbHwjqaIekS");
$request->setCurrentPage(1);
    $request->setPageSize(10);
    $response = $client->getAcsResponse($request);
    print_r("\r\n");
    print_r($response);