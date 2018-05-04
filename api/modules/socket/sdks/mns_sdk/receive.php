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
$queueName = "aliyun-iot-gbHwjqaIekS";
$queue = $client->getQueueRef($queueName);
$receiptHandle = "930773716403834880";//pub接口调用返回的messageId
try
{
    // 1. 直接调用receiveMessage函数
    // 1.1 receiveMessage函数接受waitSeconds参数，无特殊情况这里都是建议设置为30
    // 1.2 waitSeconds非0表示这次receiveMessage是一次http long polling，如果queue内刚好没有message，那么这次request会在server端等到queue内有消息才返回。最长等待时间为waitSeconds的值，最大为30。
    $res = $queue->receiveMessage(30);
    print_r($res);
    echo "ReceiveMessage Succeed! \n";
    // 2. 获取ReceiptHandle，这是一个有时效性的Handle，可以用来设置Message的各种属性和删除Message。具体的解释请参考：help.aliyun.com/document_detail/27477.html 页面里的ReceiptHandle
    $receiptHandle = $res->getReceiptHandle();
}
catch (MnsException $e)
{
    // 3. 像前面的CreateQueue和SendMessage一样，我们认为ReceiveMessage也是有可能出错的，所以这里加上CatchException并做对应的处理。
    echo "ReceiveMessage Failed: " . $e . "\n";
    echo "MNSErrorCode: " . $e->getMnsErrorCode() . "\n";
    return;
}
// 这里是用户自己的处理消息的逻辑。Sample里就直接略过这一步了。
// 如果这里发生了程序崩溃或卡住等异常情况，对应的Message会在VisibilityTimeout之后重新可见，从而可以被其他进程处理，避免消息丢失。