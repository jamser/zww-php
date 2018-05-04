<?php

namespace frontend\modules\call\controllers;

use yii\web\Controller;

/**
 * Default controller for the `call` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    public function actionComment() {
        return $this->render('comment');
    }
}
