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
$receiptHandle = "930773716403834880";//pub接口调用返回的messageId，消息有时间限制，会过期

// 4. 现在消息已经处理完了。我们可以从队列里删除这条消息了。
try
{
    // 5. 直接调用deleteMessage即可。
    $res = $queue->deleteMessage($receiptHandle);
    echo "DeleteMessage Succeed! \n";
}
catch (MnsException $e)
{
    // 6. 这里CatchException并做异常处理
    // 6.1 如果是receiptHandle已经过期，那么ErrorCode是MessageNotExist，表示通过这个receiptHandle已经找不到对应的消息。
    // 6.2 为了保证receiptHandle不过期，VisibilityTimeout的设置需要保证足够消息处理完成。并且在消息处理过程中，也可以调用changeMessageVisibility这个函数来延长消息的VisibilityTimeout时间。
    echo "DeleteMessage Failed: " . $e . "\n";
    echo "MNSErrorCode: " . $e->getMnsErrorCode() . "\n";
    return;
}