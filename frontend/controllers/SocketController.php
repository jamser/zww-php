<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;

class SocketController extends Controller{
    //阿里套接口接口调用
    function actionPub($control,$productKey,$deviceName)
    {
        $data = array
        (
            'control' => $control,
            'productKey' => $productKey,
            'deviceName' => $deviceName,
        );
        $query = http_build_query($data);
        $options = array(
            'http' => array(
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
                    "Content-Length: ".strlen($query)."\r\n".
                    "User-Agent:MyAgent/1.0\r\n",
                'method'  => "POST",
                'content' => $query,
            ),
        );
        $url = "http://127.0.0.1/login/api/web/index.php?r=socket/ali/pub";
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context, -1, 40000);
        return $result;
    }
}