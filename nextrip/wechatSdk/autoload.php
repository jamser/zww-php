<?php

spl_autoload_register(function ($class) {
    if (false !== stripos($class, 'WechatSdk')) {
        require_once __DIR__.'/'.str_replace('\\', '/', substr($class, 10)).'.php';
    }
});
