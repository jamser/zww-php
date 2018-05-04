<?php

namespace backend\controllers;

use Yii;
use common\models\call\Caller;
use yii\data\ActiveDataProvider;
use backend\models\search\CallerSearch;
use common\models\call\CallerApplyReview;

/**
 * Caller控制
 */
class CallerController extends \yii\web\Controller {
    
    /**
     * Caller列表
     */
    public function actionList() {
        $searchModel = new CallerSearch();
        $params = Yii::$app->getRequest()->get();
        $dataProvider = $searchModel->search($params);

        return $this->render('caller-list', [
            'searchModel'=>$searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * 申请列表
     */
    public function actionApplyList() {
        $searchModel = new CallerSearch();
        $params = Yii::$app->getRequest()->get() + ['status' => Caller::STATUS_WAITING_FOR_REVIEW];
        $dataProvider = $searchModel->search($params);

        return $this->render('apply-list', [
            'searchModel'=>$searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionReview($id, $returnUrl=null) {
        $id = (int)$id;
        $caller = Caller::findOne($id);
        
        $review = new CallerApplyReview([
            'user_id'=>$caller->user_id,
            'review_admin_id'=>0
        ]);
        if($review->load(Yii::$app->getRequest()->post()) && $review->save()) {
            $caller->updateAttributes([
                'status'=> $review->pass
            ]);
            return $this->redirect($returnUrl ? $returnUrl : ['apply-list']);
        }
        
        return $this->render('review', [
            'caller'=>$caller,
            'model'=>$review
        ]);
    }
    
}


