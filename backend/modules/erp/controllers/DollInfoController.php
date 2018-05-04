<?php

namespace backend\modules\erp\controllers;


use Yii;
use backend\modules\erp\models\DollInfo;
use backend\modules\erp\models\DollInfoSearch;
use yii\web\Controller;
//use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use common\helpers\MyFunction;
use OSS\OssClient;
require_once("../Classes/PHPExcel.php");

class DollInfoController extends Controller
{
    
    public function actions()
    {
        return [
            'error' => ['class' => 'yii\web\ErrorAction'],
        ];
    }
    
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index','add','create','update','view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all DollInfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DollInfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Displays a single DollInfo model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new DollInfo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreate1()
//    {
//        $model = new DollInfo();
//
//        if ($model->load(Yii::$app->request->post())) {
//            $model->img_url = UploadedFile::getInstance($model, "img_url");
//            $dir = "public/uploads";
//            if (!is_dir($dir)){
//                mkdir($dir);
//            }
//            $fileName = date("HiiHsHis") . $model->img_url->baseName . "." . $model->img_url->extension;
//            $dir = $dir . "/" . $fileName;
//            $model->img_url->saveAs($dir);
//            $img_url = "http://dev-admin.365zhuawawa.com/public/uploads/" . $fileName;
//            $dollInfo = array('dollName'=>$model->dollName,'dollTotal'=>$model->dollTotal,'dollNumber'=>$model->dollNumber,'addTime'=>$model->addTime,'dollCode'=>$model->dollCode,'img_url'=>$img_url);
//            $myfunction = new MyFunction();
//            $myfunction->addDoll($dollInfo);
//            return $this->redirect('index.php?r=erp/doll-info/index');
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }


    public function actionCreate()
    {
        $model = new DollInfo();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\bootstrap\ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            $accessKeyId = "LTAIiRG3VWVjAIpU";
            $accessKeySecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
            $endpoint = "http://oss-cn-shanghai.aliyuncs.com/";
            $bucket = "zww-image-dev";
            $ossClient = new OssClient($accessKeyId,$accessKeySecret,$endpoint);
            $object = "dolls/".date('HiiHsHis').'.jpg';
            $model->img_url = UploadedFile::getInstance($model, 'img_url');
            $imgInfo = $this->object2array($model->img_url);
            $img_url = $imgInfo['tempName'];
            if(!$img_url) {
                throw new \Exception('必须要上传图片');
            }
            $content = file_get_contents($img_url);
            $ossClient->putObject($bucket, $object, $content);

            $img_url = "http://zww-image-dev.oss-cn-shanghai.aliyuncs.com/dolls/".date('HiiHsHis').'.jpg';
            $addTime = date("Y-m-d H:i:s",time());
            $dollInfo = array('dollName'=>$model->dollName,'dollTotal'=>$model->dollTotal,'addTime'=>$addTime,'dollCode'=>$model->dollCode,
                'img_url'=>$img_url,'agency'=>$model->agency,'size'=>$model->size,'type'=>$model->type,'note'=>$model->note,'dollCoins'=>$model->dollCoins,'deliverCoins'=>$model->deliverCoins
                ,'redeemCoins'=>$model->redeemCoins);
            $db = Yii::$app->db;
            $sql = "select * from doll_info WHERE dollName='$model->dollName' OR dollCode='$model->dollCode'";
            $data = $db->createCommand($sql)->execute();
            if(empty($data)){
                $myfunction = new MyFunction();
                $myfunction->addDoll($dollInfo);
                return $this->redirect('/erp/doll-info/index');
            }else{
                throw new \Exception('娃娃编码或娃娃名称重复，请确认');
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'img_url' => '',
            ]);
        }
    }

    function object2array($object) {
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        }
        else {
            $array = $object;
        }
        return $array;
    }

    /**
     * Updates an existing DollInfo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $data = $this->object2array($model);
        $img__url = $data['img_url'];
        $addTime = $data['addTime'];

        if ($model->load(Yii::$app->request->post())) {
            $id = $model->id;
            $dollName = $model->dollName;
            $dollTotal = $model->dollTotal;
            $dollCode = $model->dollCode;
            $agency = $model->agency;
            $size = $model->size;
            $type = $model->type;
            $note = $model->note;
            $dollCoins = $model->dollCoins;
            $deliverCoins = $model->deliverCoins;
            $redeemCoins = $model->redeemCoins;
            $img = $model->img_url=UploadedFile::getInstance($model, 'img_url');
            if($img){
                $accessKeyId = "LTAIiRG3VWVjAIpU";
                $accessKeySecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
                $endpoint = "http://oss-cn-shanghai.aliyuncs.com/";
                $bucket = "zww-image-dev";
                $ossClient = new OssClient($accessKeyId,$accessKeySecret,$endpoint);
                $object = "dolls/".date('HiiHsHis').'.jpg';
                $imgInfo = $this->object2array($img);
                $img_url = $imgInfo['tempName'];
                $content = file_get_contents($img_url);
                $ossClient->putObject($bucket, $object, $content);

                $img_url = "http://zww-image-dev.oss-cn-shanghai.aliyuncs.com/dolls/".date('HiiHsHis').'.jpg';
            }else{
                $img_url = $data['img_url'];
            }
            $myfunction = new MyFunction();
            $myfunction->updateDollInfo($id,$dollName,$dollTotal,$img_url,$addTime,$dollCode,$agency,$size,$type,$note,$dollCoins,$deliverCoins,$redeemCoins);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'img_url' => $img__url,
            ]);
        }
    }

    /**
     * Deletes an existing DollInfo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DollInfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DollInfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DollInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAdd(){
        $model = new DollInfo();
        if ($model->load(Yii::$app->request->post())) {
            $model->agency = UploadedFile::getInstance($model, 'agency');
            $excelInfo = $this->object2array($model->agency);
            $excel_url = $excelInfo['tempName'];
            //文件名为文件路径和文件名的拼接字符串
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');//创建读取实例
            /*
             * log()//方法参数
             * $file_name excal文件的保存路径
             */
            $objPHPExcel = $objReader->load($excel_url);//加载文件
            $sheet = $objPHPExcel->getSheet(0);//取得sheet(0)表
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            for($i=2;$i<=$highestRow;$i++)
            {
                $city = $sheet->getCell("A".$i)->getValue();
                $code = $sheet->getCell("C".$i)->getValue();
                $myfunction =new MyFunction();
                $myfunction->updateCity($city,$code);
            }
            return $this->redirect('/erp/order/index');
        } else {
            return $this->render('add',[
                'model' => $model,
            ]);
        }
    }
}