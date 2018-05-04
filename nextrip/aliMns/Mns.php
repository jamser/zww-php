<?php
namespace nextrip\aliMns;

require_once(\dirname(__FILE__).'/sdk/mns-autoloader.php');

use AliyunMNS\Client;
use AliyunMNS\Requests\SendMessageRequest;
use AliyunMNS\Requests\CreateQueueRequest;
use AliyunMNS\Requests\BatchReceiveMessageRequest;
use AliyunMNS\Exception\MnsException;
use AliyunMNS\Exception\MessageNotExistException;
use AliyunMNS\Responses\ReceiveMessageResponse;

class Mns extends \yii\base\Component {
    
    /**
     * APP ID
     * @var string
     */
    private $accessId;

    /**
     * APP secret
     * @var string
     */
    private $accessKey;
    
    /**
     * 内网地址 杭州
     * @var string
     */
    private $publicEndPoint;
    
    /**
     * 内网地址 杭州
     * @var string
     */
    private $privacyEndPoint;
    
    /**
     * 处理
     * @var Client
     */
    protected $client;
    
    public function setAccessId($accessId) {
        $this->accessId = $accessId;
    }

    public function setAccessKey($accessKey) {
        $this->accessKey = $accessKey;
    }
    
    public function setPublicEndPoint($endPoint) {
        $this->publicEndPoint = $endPoint;
    }
    
    public function setPrivacyEndPoint($endPoint) {
        $this->privacyEndPoint = $endPoint;
    }
   
    /**
     * 获取消息处理类
     * @return Client
     */
    public function getClient() {
        return $this->client ? $this->client : 
                ($this->client=new Client(
                        YII_ENV==='prod' ? $this->privacyEndPoint : $this->publicEndPoint,
                        $this->accessId,
                        $this->accessKey
                ));
    }
    
    /**
     * @param string $queueName 队列名称
     * @return CreateQueueResponse $response: the CreateQueueResponse
     *
     * @throws QueueAlreadyExistException if queue already exists
     * @throws InvalidArgumentException if any argument value is invalid
     * @throws MnsException if any other exception happends 
     */
    public function createQueue($queueName) {
        $client = $this->getClient();

        $request = new CreateQueueRequest($queueName);
        return $client->createQueue($request);
    }
    
    /**
     * Returns a queue reference for operating on the queue
     * this function does not create the queue automatically.
     *
     * @param string $queueName  the queue name
     *
     * @return Queue $queue: the Queue instance
     */
    public function getQueueRef($queueName) {
        return $this->getClient()->getQueueRef($queueName);
    }
    
    
    /**
     * 发送消息
     * @param string $queueName
     * @return SendMessageResponse containing the messageId and bodyMD5
     * @throws QueueNotExistException if queue does not exist
     * @throws InvalidArgumentException if any argument value is invalid
     * @throws MalformedXMLException if any error in xml
     * @throws MnsException if any other exception happends
     */
    public function sendMessage($queueName, $messageBody, $delaySeconds=null, $priority=null) {
        $queue = $this->getQueueRef($queueName);
        $request = new SendMessageRequest($messageBody, $delaySeconds, $priority);
        return $queue->sendMessage($request);
    }
    
    /**
     * 接收消息
     * @param string $queueName 队列名称
     * @param integer $waitSeconds 等待时间
     * @return \AliyunMNS\Responses\ReceiveMessageResponse containing the messageBody and properties
     *          the response is same as PeekMessageResponse,
     *          except that the receiptHandle is also returned in receiveMessage
     *
     * @throws QueueNotExistException if queue does not exist
     * @throws MessageNotExistException if no message exists in the queue
     * @throws MnsException if any other exception happends
     */
    public function receiveMessage($queueName, $waitSeconds=null) {
        $queue = $this->getQueueRef($queueName);
        return $queue->receiveMessage($waitSeconds);
    }
    
    /**
     * 批量接收消息
     * @param string $queueName 队列名称
     * @param integer $numOfMessages 个数
     * @param integer $waitSeconds 等待时间
     * @return \AliyunMNS\Responses\BatchReceiveMessageResponse
     *            the received messages
     *
     * @throws QueueNotExistException if queue does not exist
     * @throws MessageNotExistException if no message exists
     * @throws MnsException if any other exception happends
     */
    public function batchReceiveMessage($queueName, $numOfMessages, $waitSeconds=null) {
        $queue = $this->getQueueRef($queueName);
        $request = new BatchReceiveMessageRequest($numOfMessages, $waitSeconds);
        return $queue->batchReceiveMessage($request);
    }
    
    /**
     * 删除消息 
     * @param string $queueName 队列名称
     * @param $receiptHandle the receiptHandle returned from receiveMessage
     * @return ReceiveMessageResponse
     *
     * @throws QueueNotExistException if queue does not exist
     * @throws InvalidArgumentException if the argument is invalid
     * @throws ReceiptHandleErrorException if the $receiptHandle is invalid
     * @throws MnsException if any other exception happends
     */
    public function deleteMessage($queueName,$receiptHandle) {
        $queue = $this->getQueueRef($queueName);
        return $queue->deleteMessage($receiptHandle);
    }
    
    /**
     * 修改消息的可见时间
     * @param string $queueName 队列名称
     * @param $receiptHandle the receiptHandle returned from receiveMessage
     * @param integer $visibilityTimeout 秒数 从现在起该时间以后可见
     * @return ChangeMessageVisibilityResponse
     *
     * @throws QueueNotExistException if queue does not exist
     * @throws MessageNotExistException if the message does not exist
     * @throws InvalidArgumentException if the argument is invalid
     * @throws ReceiptHandleErrorException if the $receiptHandle is invalid
     * @throws MnsException if any other exception happends 
     */
    public function changeMessageVisibility($queueName, $receiptHandle, $visibilityTimeout) {
        $queue = $this->getQueueRef($queueName);
        return $queue->changeMessageVisibility($receiptHandle, $visibilityTimeout);
    }
}



