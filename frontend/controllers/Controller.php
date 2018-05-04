<?php

namespace frontend\controllers;

class Controller extends \yii\web\Controller {
    
    public function init() {
        parent::init();
        if(isMobile()) {
            $this->layout = 'mobile';
        }
    }
    
}

