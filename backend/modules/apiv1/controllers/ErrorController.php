<?php

namespace backend\modules\apiv1\controllers;

use Yii;
use frontend\controllers\Controller;
use common\base\ErrCode;
use common\base\Response;
use yii\helpers\ArrayHelper;

class ErrorController extends Controller {

    public $defaultAction = 'show';

    public function actionShow() {
        $exception = Yii::$app->errorHandler->exception;
        $code = 0;
        $message = '未知错误';
        if ($exception !== null) {
            $code = $exception->getCode();
            $message = $exception->getMessage();
        }
        Response::error($code ? $code : 500, $message);
    }

}
