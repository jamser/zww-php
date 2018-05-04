<?php
// require sdk里自带的一个autoload文件即可
require_once('mns-autoloader.php');
// 代码里需要用的一些php class
use AliyunMNS\Client;
use AliyunMNS\Exception\MnsException;

// 1. 首先初始化一个client
$accessId = "LTAIiRG3VWVjAIpU";
$accessKey = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
$endPoint = "http://1792180091275324.mns.cn-shanghai.aliyuncs.com/";
$client = new Client($endPoint, $accessId, $accessKey);
$queueName = "aliyun-iot-gbHwjqaIekS   、、";//删除队列，队列名

try {
    $client->deleteQueue($queueName);
    echo "DeleteQueue Succeed! \n";
} catch (MnsException $e) {
    echo "DeleteQueue Failed: " . $e;
    return;
}