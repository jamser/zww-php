<?php

namespace common\base;

use Yii;
use Exception;
use ErrorCode;

class Response {

    /**
     * 显示一个成功的结果
     * @param [] $result
     */
    public static function success($result = [], $unescapedUnicode=true) {
        static::show(ErrCode::NONE, $result, null, $unescapedUnicode);
    }

    /**
     * 返回错误
     * @param integer $code 错误码
     * @param string $msg 错误信息
     */
    public static function error($code, $msg, $unescapedUnicode=true) {
        if(!$msg) {
            throw new Exception('缺少参数错误描述');
        }
        static::show($code, [], $msg, $unescapedUnicode);
    }

    /**
     * 输出一个结果给客户端
     * @param integer $code 结果码 见ErrorCode::E_...
     * @param [] $result 返回结果
     * @param string $msg 错误消息
     */
    public static function show($code, $result = [], $msg = null, $unescapedUnicode=true) {
        $return = [
            'code' => $code,
            'result' => $result,
            'msg' => $msg
        ];

        $responseContent = $unescapedUnicode ?  json_encode($return, JSON_UNESCAPED_UNICODE) : json_encode($return) ;
        $response = Yii::$app->getResponse();
        $response->format = 'json';
        $response->content = $responseContent;
        $response->send();
        Yii::$app->end();
    }

    public static function renderWebErrorPage($title, $message, $buttons=[], $layout=null) {
        $app = Yii::$app;
        if($layout) {
            $app->layout = $layout;
        }
        return $app->controller->render('//error/friendlyMsg', [
            'title'=>$title,
            'message'=>$message,
            'buttons'=>$buttons
        ]);
    }

    public static function renderMsg($title, $message, $buttons=[], $layout=null) {
        $app = Yii::$app;
        if($layout) {
            $app->layout = $layout;
        }
        return $app->controller->render('//base/friendlyMsg', [
            'title'=>$title,
            'message'=>$message,
            'buttons'=>$buttons
        ]);
    }

}
