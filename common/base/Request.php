<?php

namespace common\base;

class Request extends \yii\web\Request {
    
    public function getUserIP() {
        return getIp();
    }
    
}

