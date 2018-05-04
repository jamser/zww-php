<?php
namespace backend\modules\doll\controllers;

use Yii;
use yii\web\Controller;
use common\services\doll\ElasticsearchService;

class GameController extends Controller{
    public function actionIndex(){
        $log_type = Yii::$app->getRequest()->get('log_type',null);
        $log_date = Yii::$app->getRequest()->get('log_date',null);
        $page = Yii::$app->getRequest()->get('page',null);
        $alldata = ElasticsearchService::all();
        $totalPages = $alldata['hits']['total'];
        $pages = new \yii\data\Pagination([
            'totalCount'=>$totalPages
        ]);
        $size = $pages->getLimit();
        if($page){
            $from = ($page-1) * $size;
        }else{
            $from = 0;
        }
        if($log_type){
            $data = ElasticsearchService::typeSearch($log_type,$size,$from);
        }elseif($log_date){
            $data = ElasticsearchService::dateSearch($log_date,$size,$from);
        }else{
            $data =ElasticsearchService::allSearch( $start='2018-03-20', $end='2018-03-23',$size,$from);
        }

        return $this->render('index',[
            'data' => $data,
            'pages' => $pages,
        ]);
    }

    public function actionInsert(){
        $service = new ElasticsearchService();
        $service->insert();
        echo 1;
    }
}