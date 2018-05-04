<?php

namespace nextrip\aliMns;

class Message extends MnsBase {

    //发送消息到指定的消息队列
    public function sendMessage($queueName, $msgbody, $DelaySeconds = 0, $Priority = 8) {
        $VERB = "POST";
        $CONTENT_BODY = $this->generatexml($msgbody, $DelaySeconds, $Priority);
        $CONTENT_MD5 = base64_encode(md5($CONTENT_BODY));
        $contentType = $this->contentType;
        $GMT_DATE = $this->getGMTDate();
        $CanonicalizedMQSHeaders = array(
            'x-mns-version' => $this->mnsVersion
        );
        $RequestResource = "/queues/" . $queueName . "/messages";
        $sign = $this->getSignature($VERB, $CONTENT_MD5, $contentType, $GMT_DATE, $CanonicalizedMQSHeaders, $RequestResource);
        $headers = array(
            'Host' => $this->queueOwnerId . "." . $this->mnsUrl,
            'Date' => $GMT_DATE,
            'Content-Type' => $contentType,
            'Content-MD5' => $CONTENT_MD5,
        );
        foreach ($CanonicalizedMQSHeaders as $k => $v) {
            $headers[$k] = $v;
        }
        $headers['Authorization'] = $this->getHeaderAuthorization($sign);
        $request_uri = '//' . $this->queueOwnerId . '.' . $this->mnsUrl . $RequestResource;
        $data = $this->requestCore($request_uri, $VERB, $headers, $CONTENT_BODY);
        //返回状态，正确返回ok和返回值数组,错误返回错误代码和错误原因数组！
        $msg = array();
        $error = $this->errorHandle($data[0]);
        if ($error) {
            $msg['state'] = $error;
            $msg['msg'] = $this->getXmlData($data[1]);
        } else {
            $msg['state'] = "ok";
            $msg['msg'] = $this->getXmlData($data[1]);
        }
        return $msg;
    }

    //接收指定的队列消息 
    public function receiveMessage($queue, $second) {
        $VERB = "GET";
        $CONTENT_BODY = "";
        $CONTENT_MD5 = base64_encode(md5($CONTENT_BODY));
        $contentType = $this->contentType;
        $GMT_DATE = $this->getGMTDate();
        $CanonicalizedMQSHeaders = array(
            'x-mns-version' => $this->mnsVersion
        );
        $RequestResource = "/queues/" . $queue . "/messages?waitseconds=" . $second;
        $sign = $this->getSignature($VERB, $CONTENT_MD5, $contentType, $GMT_DATE, $CanonicalizedMQSHeaders, $RequestResource);
        $headers = array(
            'Host' => $this->queueOwnerId . "." . $this->mnsUrl,
            'Date' => $GMT_DATE,
            'Content-Type' => $contentType,
            'Content-MD5' => $CONTENT_MD5
        );
        foreach ($CanonicalizedMQSHeaders as $k => $v) {
            $headers[$k] = $v;
        }
        $headers['Authorization'] = $this->getHeaderAuthorization($sign);
        $request_uri = '//' . $this->queueOwnerId . '.' . $this->mnsUrl . $RequestResource;
        $data = $this->requestCore($request_uri, $VERB, $headers, $CONTENT_BODY);
        //返回状态，正确返回ok和返回值数组,错误返回错误代码和错误原因数组！
        $msg = array();
        $error = $this->errorHandle($data[0]);
        if ($error) {
            $msg['state'] = $error;
            $msg['msg'] = $this->getXmlData($data[1]);
        } else {
            $msg['state'] = "ok";
            $msg['msg'] = $this->getXmlData($data[1]);
        }
        isset($msg['MessageBody']) && $msg['MessageBody'] = base64_decode($msg['MessageBody']);
        return $msg;
    }
    
    /**
     *  批量接收指定的队列消息 
     * @param string $queue 队列名称
     * @param integer $second 秒数
     * @param integer $num 数量 
     * @return [] 格式 : ['state'=>'ok'//或错误 , 'msgs'=>[ {msg1}, {msg2} ]]
     */
    public function receiveMessages($queue, $second, $num) {
        $VERB = "GET";
        $CONTENT_BODY = "";
        $CONTENT_MD5 = base64_encode(md5($CONTENT_BODY));
        $contentType = $this->contentType;
        $GMT_DATE = $this->getGMTDate();
        $CanonicalizedMQSHeaders = array(
            'x-mns-version' => $this->mnsVersion
        );
        $RequestResource = "/queues/" . $queue . "/messages?numOfMessages={$num}&waitseconds=" . $second;
        $sign = $this->getSignature($VERB, $CONTENT_MD5, $contentType, $GMT_DATE, $CanonicalizedMQSHeaders, $RequestResource);
        $headers = array(
            'Host' => $this->queueOwnerId . "." . $this->mnsUrl,
            'Date' => $GMT_DATE,
            'Content-Type' => $contentType,
            'Content-MD5' => $CONTENT_MD5
        );
        foreach ($CanonicalizedMQSHeaders as $k => $v) {
            $headers[$k] = $v;
        }
        $headers['Authorization'] = $this->getHeaderAuthorization($sign);
        $request_uri = '//' . $this->queueOwnerId . '.' . $this->mnsUrl . $RequestResource;
        $data = $this->requestCore($request_uri, $VERB, $headers, $CONTENT_BODY);
        //返回状态，正确返回ok和返回值数组,错误返回错误代码和错误原因数组！
        $msg = array();
        $error = $this->errorHandle($data[0]);
        if ($error) {
            $msg['state'] = $error;
            $msg['request'] = $this->getXmlData($data[1]);
        } else {
            $msg['state'] = "ok";
            $mainData = $this->getXmlData($data[1]);//格式为 : [Message=>[]]
            $decodeMsgs = [];
            if(isset($mainData['Message']['MessageId'])) {
                $mainData['Message']['MessageBody'] = base64_decode($mainData['Message']['MessageBody']);
                $decodeMsgs = [ $mainData['Message']];
            } else {
                foreach($mainData['Message'] as $oneMsgData) {
                    isset($oneMsgData['MessageBody']) && $oneMsgData['MessageBody'] = base64_decode($oneMsgData['MessageBody']);
                    $decodeMsgs[] = $oneMsgData;
                }
            }
            $msg['msgs'] = $decodeMsgs;
        }
        return $msg;
    }

    //删除已经被接收过的消息
    public function deleteMessage($queueName, $receiptHandle) {
        $VERB = "DELETE";
        $CONTENT_BODY = "";
        $CONTENT_MD5 = base64_encode(md5($CONTENT_BODY));
        $contentType = $this->contentType;
        $GMT_DATE = $this->getGMTDate();
        $CanonicalizedMQSHeaders = array(
            'x-mns-version' => $this->mnsVersion
        );
        $RequestResource = "/queues/" . $queueName . "/messages?ReceiptHandle=" . $receiptHandle;
        $sign = $this->getSignature($VERB, $CONTENT_MD5, $contentType, $GMT_DATE, $CanonicalizedMQSHeaders, $RequestResource);
        $headers = array(
            'Host' => $this->queueOwnerId . "." . $this->mnsUrl,
            'Date' => $GMT_DATE,
            'Content-Type' => $contentType,
            'Content-MD5' => $CONTENT_MD5
        );
        foreach ($CanonicalizedMQSHeaders as $k => $v) {
            $headers[$k] = $v;
        }
        $headers['Authorization'] =  $this->getHeaderAuthorization($sign);
        $request_uri = '//' . $this->queueOwnerId . '.' . $this->mnsUrl . $RequestResource;
        $data = $this->requestCore($request_uri, $VERB, $headers, $CONTENT_BODY);
        //返回状态，正确返回ok,错误返回错误代码！
        $error = $this->errorHandle($data[0]);
        if ($error) {
            $msg['state'] = $error;
        } else {
            $msg['state'] = "ok";
        }
        return $msg;
    }

    //查看消息，但不改变消息状态（是否被查看或接收）
    public function peekMessage($queueName) {
        $VERB = "GET";
        $CONTENT_BODY = "";
        $CONTENT_MD5 = base64_encode(md5($CONTENT_BODY));
        $contentType = $this->contentType;
        $GMT_DATE = $this->getGMTDate();
        $CanonicalizedMQSHeaders = array(
            'x-mns-version' => $this->mnsVersion
        );
        $RequestResource = "/queues/" . $queueName . "/messages?peekonly=true";
        $sign = $this->getSignature($VERB, $CONTENT_MD5, $contentType, $GMT_DATE, $CanonicalizedMQSHeaders, $RequestResource);
        $headers = array(
            'Host' => $this->queueOwnerId . "." . $this->mnsUrl,
            'Date' => $GMT_DATE,
            'Content-Type' => $contentType,
            'Content-MD5' => $CONTENT_MD5
        );
        foreach ($CanonicalizedMQSHeaders as $k => $v) {
            $headers[$k] = $v;
        }
        $headers['Authorization'] = $this->getHeaderAuthorization($sign);
        $request_uri = '//' . $this->queueOwnerId . '.' . $this->mnsUrl . $RequestResource;
        $data = $this->requestCore($request_uri, $VERB, $headers, $CONTENT_BODY);
        //返回状态，正确返回ok和返回内容数组,错误返回错误代码和错误原因数组！
        $msg = array();
        $error = $this->errorHandle($data[0]);
        if ($error) {
            $msg['state'] = $error;
            $msg['msg'] = $this->getXmlData($data[1]);
        } else {
            $msg['state'] = "ok";
            $msg['msg'] = $this->getXmlData($data[1]);
        }
        return $msg;
    }

    //修改未被查看消息时间，
    public function changeMessageVisibility($queueName, $receiptHandle, $visibilitytimeout) {

        $VERB = "PUT";
        $CONTENT_BODY = "";
        $CONTENT_MD5 = base64_encode(md5($CONTENT_BODY));
        $contentType = $this->contentType;
        $GMT_DATE = $this->getGMTDate();
        $CanonicalizedMQSHeaders = array(
            'x-mns-version' => $this->mnsVersion
        );
        $RequestResource = "/queues/" . $queueName . "/messages?ReceiptHandle=" . $receiptHandle . "&VisibilityTimeout=" . $visibilitytimeout;

        $sign = $this->getSignature($VERB, $CONTENT_MD5, $contentType, $GMT_DATE, $CanonicalizedMQSHeaders, $RequestResource);

        $headers = array(
            'Host' => $this->queueOwnerId . "." . $this->mnsUrl,
            'Date' => $GMT_DATE,
            'Content-Type' => $contentType,
            'Content-MD5' => $CONTENT_MD5
        );
        foreach ($CanonicalizedMQSHeaders as $k => $v) {
            $headers[$k] = $v;
        }
        $headers['Authorization'] = $this->getHeaderAuthorization($sign);
        $request_uri = '//' . $this->queueOwnerId . '.' . $this->mnsUrl . $RequestResource;
        $data = $this->requestCore($request_uri, $VERB, $headers, $CONTENT_BODY);
        //返回状态，正确返回ok,错误返回错误代码！
        $error = $this->errorHandle($data[0]);
        if ($error) {
            $msg['state'] = $error;
            $msg['msg'] = $this->getXmlData($data[1]);
        } else {
            $msg['state'] = "ok";
            $msg['msg'] = $this->getXmlData($data[1]);
        }
        return $msg;
    }

    //数据转换到xml
    private function generatexml($msgbody, $DelaySeconds = 0, $Priority = 8) {
        header('Content-Type: text/xml;');
        $dom = new \DOMDocument("1.0", "utf-8");
        $dom->formatOutput = TRUE;
        $root = $dom->createElement("Message"); //创建根节点
        $dom->appendchild($root);
        $price = $dom->createAttribute("xmlns");
        $root->appendChild($price);
        $priceValue = $dom->createTextNode('//mns.aliyuncs.com/doc/v1/');
        $price->appendChild($priceValue);

        $msg = array('MessageBody' => $msgbody, 'DelaySeconds' => $DelaySeconds, 'Priority' => $Priority);
        foreach ($msg as $k => $v) {
            $msg = $dom->createElement($k);
            $root->appendChild($msg);
            if($k=='MessageBody'){
                $titleText = $dom->createTextNode(base64_encode($v));  
            }else{
                $titleText = $dom->createTextNode($v);  
            }
            $msg->appendChild($titleText);
        }
        return $dom->saveXML();
    }

}
