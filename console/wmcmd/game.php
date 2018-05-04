<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/class/Control.php';

use Workerman\Worker;
use PHPSocketIO\SocketIO;
use AliyunMNS\Client;
use AliyunMNS\Exception\MnsException;
use Yii;

// listen port 2021 for socket.io client
$io = new SocketIO(2345);
$control = new Control();
$io->on('connection', function($socket)use($io, $control) {
    $socket->on('coin', function($data)use($io, $control) {
        
        $roomId = $data && isset($data['roomId']) ? $data['roomId'] : null;
        $key = $data && isset($data['key']) ? $data['key'] : null;
        
        #todo 校验roomId 和 key
        
        //设置一个redis的玩家值
        
        $deviceName = "device_". str_repeat('0', 3-strlen($roomId)).$roomId;
        $response = $control->coin($deviceName);
        
        $io->emit('ready', [
            'roomId'=>$roomId,
            'key'=>$key,
            'response'=>$response
        ]);
    });

    $socket->on('control', function($data)use($io, $control) {
        $action = $data && isset($data['action']) ? $data['action'] : null;
        $key = $data && isset($data['key']) ? $data['key'] : null;
        $roomId = $data && isset($data['roomId']) ? $data['roomId'] : null;

        //上下左右动作处理
        $deviceName = "device_". str_repeat('0', 3-strlen($roomId)).$roomId;
        switch ($action) {
            case 'forward':
                $controlCommand = '{"control":"forward"}';
                //$io->emit('forward', $data);
                break;
            case 'backward':
                $controlCommand = '{"control":"backward"}';
                //$io->emit('backward',$data);
                break;
            case 'left':
                $controlCommand = '{"control":"left"}';
                //$io->emit('left',$data);
                break;
            case 'right':
                $controlCommand = '{"control":"right"}';
                //$io->emit('right',$data);
                break;
            case 'stop':
                $controlCommand = '{"control":"stop"}';
                //$io->emit('right',$data);
                break;
            case 'claw':
                $controlCommand = '{"control":"claw"}';
                //$io->emit('right',$data);
                break;
            default:
                $controlCommand = null;
                break;
        }
        if($controlCommand) {
            
            $response = $control->{$action}($deviceName);
            echo "control: send iot request ". json_encode($response, JSON_UNESCAPED_UNICODE)."\n";
            //$res = json_encode($response);
            $io->emit("control",[
                'action'=>$action
            ]);
        }
       
    });
    
    $socket->on('listenMsg', function($data)use($io) {
        //echo json_encode($data, JSON_UNESCAPED_UNICODE);
        $action = $data && isset($data['action']) ? $data['action'] : null;
        $key = $data && isset($data['key']) ? $data['key'] : null;
        $roomId = $data && isset($data['roomId']) ? $data['roomId'] : null;
        if(!$roomId) {
            echo "listenMsg Err: no room id;";
            $io->emit('end', ['action'=>'end']);
        } else {
            $accessId = "LTAIiRG3VWVjAIpU";
            $accessKey = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
            $endPoint = "http://1792180091275324.mns.cn-shanghai.aliyuncs.com/";
            $client = new Client($endPoint, $accessId, $accessKey);
            $queue = $client->getQueueRef('devicequeue'. str_repeat('0', 3-strlen($roomId)).$roomId);
            $queue->setBase64(false);
            $time = time();
            try {
                $req = new AliyunMNS\Requests\BatchReceiveMessageRequest(10,0.01);
                $res = $queue->batchReceiveMessage($req);
                $msgs = $res->getMessages();
                foreach($msgs as $msg) {
                    $body = $msg->getMessageBody();
                    $enqueueTime = $msg->enqueueTime;
                    $io->emit('feedback', $body);
                    //echo "listenMsg : 收到阿里云队列消息 {$body}";
                    $receiptHandle = $msg->getReceiptHandle();
                    $queue->deleteMessage($receiptHandle);
                }
            } catch (MnsException $e) {
                //echo "ReceiveMessage Failed: " . $e . "\n";
                //echo "MNSErrorCode: " . $e->getMnsErrorCode() . "\n";
                $io->emit('noMsg');
            }
            
        }
    });
});

Worker::runAll();