<?php

namespace nextrip\aliMns;

/**
 * 消息服务
 */
class MnsBase {

    public $accessKey = '';
    public $accessSecret = '';
    public $contentType = 'text/xml;utf-8';
    public $mnsVersion = '2015-06-06';
    public $queueOwnerId = '';
    public $mnsUrl = '';

    function __construct($key, $secret, $queueOwnerId, $mnsUrl) {
        $this->accessKey = $key;
        $this->accessSecret = $secret;
        $this->queueOwnerId = $queueOwnerId;
        $this->mnsUrl = $mnsUrl;
    }

    //curl 操作	 受保护的方法
    /**
     * @return [] 如: array (
     *   0 => 'HTTP/1.1 200 OK
     *       Date: Wed, 28 Oct 2015 02:06:19 GMT
     *       Content-Type: text/xml;charset=utf-8
     *       Content-Length: 2276
     *       Connection: keep-alive
     *       Server: AliyunMNS
     *       x-mns-request-id: 56302D9BB8C115C72842FEEE
     *       x-mns-version: 2015-06-06',
     *   1 => '<?xml version="1.0"?>
     *       <Messages xmlns="http://mns.aliyuncs.com/doc/v1">
     *         <Message>
     *           <MessageId>D2D1DEC96E4678D4-1-150A9598DE9-200000001</MessageId>
     *           <MessageBodyMD5>3AED23FB70BD99FA907F5212F50DF7B9</MessageBodyMD5>
     *           <MessageBody>YToyOntzOjI6ImlkIjtpOjEwNTgyODc4O3M6MTA6ImVycm9yQ291bnQiO2k6MDt9</MessageBody>
     *           <ReceiptHandle>1-ODU4OTkzNDU5My0xNDQ1OTk4Mjc5LTEtOA==</ReceiptHandle>
     *           <EnqueueTime>1445950229993</EnqueueTime>
     *           <FirstDequeueTime>1445952551937</FirstDequeueTime>
     *           <NextVisibleTime>1445998279000</NextVisibleTime>
     *           <DequeueCount>3</DequeueCount>
     *           <Priority>8</Priority>
     *         </Message>
     *       </Messages>'
     * )
     */
    protected function requestCore($request_uri, $request_method, $request_header, $request_body = "") {
        if ($request_body != "") {
            $request_header['Content-Length'] = strlen($request_body);
        }
        $_headers = array();
        foreach ($request_header as $name => $value)
            $_headers[] = $name . ": " . $value;
        $request_header = $_headers;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request_method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_header);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($ch, CURLOPT_TIMEOUT,120);   //只需要设置一个秒的数量就可以  
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $data = explode("\r\n\r\n", $res);
    }

    //获取错误Handle  受保护的方法
    protected function errorHandle($headers) {
        preg_match('/HTTP\/[\d]\.[\d] ([\d]+) /', $headers, $code);
        if ($code[1]) {
            if ($code[1] / 100 > 1 && $code[1] / 100 < 4)
                return false;
            else
                return $code[1];
        }
    }

    //签名函数	受保护的方法
    protected function getSignature($VERB, $CONTENT_MD5, $contentType, $GMT_DATE, $CanonicalizedMQSHeaders = array(), $CanonicalizedResource = "/") {
        $order_keys = array_keys($CanonicalizedMQSHeaders);
        sort($order_keys);
        $x_mqs_headers_string = "";
        foreach ($order_keys as $k) {
            $x_mqs_headers_string .= join(":", array(strtolower($k), $CanonicalizedMQSHeaders[$k] . "\n"));
        }
        $string2sign = sprintf(
                "%s\n%s\n%s\n%s\n%s%s", $VERB, $CONTENT_MD5, $contentType, $GMT_DATE, $x_mqs_headers_string, $CanonicalizedResource
        );
        $sig = base64_encode(hash_hmac('sha1', $string2sign, $this->accessSecret, true));
        return $sig;
    }
    
    protected function getHeaderAuthorization($sign) {
        return "MNS {$this->accessKey}:{$sign}";
    }

    //获取时间 受保护的方法
    protected function getGMTDate() {
        date_default_timezone_set("UTC");
        return date('D, d M Y H:i:s', time()) . ' GMT';
    }

    //解析xml	受保护的方法
    protected function getXmlData($strXml) {
        $pos = strpos($strXml, 'xml');
        if ($pos) {
            $xmlCode = simplexml_load_string($strXml, 'SimpleXMLElement', LIBXML_NOCDATA);
            $arrayCode = $this->get_object_vars_final($xmlCode);
            return $arrayCode;
        } else {
            return '';
        }
    }

    //解析obj	受保护的方法
    protected function get_object_vars_final($obj) {
        if (is_object($obj)) {
            $obj = get_object_vars($obj);
        }
        if (is_array($obj)) {
            foreach ($obj as $key => $value) {
                $obj[$key] = $this->get_object_vars_final($value);
            }
        }
        return $obj;
    }

}
