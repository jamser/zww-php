<?php

include_once __DIR__.'/../../sdks/aliyun-sdk/aliyun-php-sdk-core/Config.php';
use \Iot\Request\V20170420 as Iot;

class Control {
    
    protected $accessKeyId = "LTAIiRG3VWVjAIpU";
    
    protected $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
    
    protected $endPoint = 'cn-shanghai';
    
    protected $productKey = 'gbHwjqaIekS';

    protected $deviceName = null;


    public function __construct() {
    }

    protected function action($deviceName, $control) {
        $iClientProfile = \DefaultProfile::getProfile($this->endPoint, $this->accessKeyId, $this->accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Iot\PubRequest();
        $request->setProductKey($this->productKey);
        $request->setTopicFullName("/{$this->productKey}/{$deviceName}/control"); //消息发送到的Topic全名.
        $request->setMessageContent(base64_encode($control)); // Base64 String.
        $request->setQos(0);
        return $client->getAcsResponse($request);
    }
    
    public function left($deviceName) {
        return $this->action($deviceName,'{"control":"left"}');
    }
    
    public function right($deviceName) {
        return $this->action($deviceName,'{"control":"right"}');
    }
    
    public function backward($deviceName) {
        return $this->action($deviceName,'{"control":"backward"}');
    }
    
    public function forward($deviceName) {
        return $this->action($deviceName,'{"control":"forward"}');
    }
    
    public function stop($deviceName) {
        return $this->action($deviceName,'{"control":"stop"}');
    }
    
    public function coin($deviceName) {
        return $this->action($deviceName,'{"control":"coin"}');
    }

    public function claw($deviceName) {
        return $this->action($deviceName,'{"control":"claw"}');
    }
    
    
}

