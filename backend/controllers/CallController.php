<?php

namespace backend\controllers;

use Yii;
use common\models\call\Caller;
use yii\data\ActiveDataProvider;
use backend\models\search\CallOrderSearch;
use common\models\call\CallerApplyReview;

class CallController extends Controller {
    
    /**
     * 订单列表
     */
    public function actionOrderList() {
        $searchModel = new CallOrderSearch();
        $params = Yii::$app->getRequest()->get();
        $dataProvider = $searchModel->search($params);

        return $this->render('call-order-list', [
            'searchModel'=>$searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
}

