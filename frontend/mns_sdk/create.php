<?php
// require sdk里自带的一个autoload文件即可
require_once('mns-autoloader.php');
// 代码里需要用的一些php class
use AliyunMNS\Client;
use AliyunMNS\Requests\SendMessageRequest;
use AliyunMNS\Requests\CreateQueueRequest;
use AliyunMNS\Exception\MnsException;

// 1. 首先初始化一个client
$accessId = "LTAIiRG3VWVjAIpU";
$accessKey = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
$endPoint = "http://1792180091275324.mns.cn-shanghai.aliyuncs.com/";
$client = new Client($endPoint, $accessId, $accessKey);
// 2. 生成一个CreateQueueRequest对象。CreateQueueRequest还可以接受一个QueueAttributes参数，用来初始化生成的queue的属性。
// 2.1 对于queue的属性，请参考help.aliyun.com/document_detail/27476.html
$queueName = "aliyun-iot-gbHwjqaIekS";//队列名称，一台机器对应一个
$request = new CreateQueueRequest($queueName);
try
{
    $res = $client->createQueue($request);
    // 2.2 CreateQueue成功
    echo "QueueCreated! \n";
}
catch (MnsException $e)
{
    // 2.3 可能因为网络错误，或者Queue已经存在等原因导致CreateQueue失败，这里CatchException并做对应的处理
    echo "CreateQueueFailed: " . $e . "\n";
    echo "MNSErrorCode: " . $e->getMnsErrorCode() . "\n";
    return;
}
