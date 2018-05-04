<?php

namespace backend\modules\apiv1;

use Yii;

/**
 * apiv1 module definition class
 */
class Module extends \yii\base\Module
{

    public function beforeAction($action) {
        Yii::$app->controller->actionParams['isApiRequest'] = 'v1';
        Yii::$app->errorHandler->errorAction = '/apiv1/error/show';
        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
