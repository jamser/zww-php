<?php

namespace backend\modules\apiv1\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\Region;

/**
 * Default controller for the `apiv1` module
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
    
    public function actionGetRegionJson() {
        $regions = Region::getAllFormatRegions();
        $jsonData = json_encode($regions);
        echo <<<EOT
var regionData = $jsonData;
EOT;
        Yii::$app->end();
    }
}
