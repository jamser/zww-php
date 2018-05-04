<?php

namespace frontend\modules\call\controllers;

use Yii;
use common\models\PcallUser;
use common\models\search\PcallUserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use nextrip\smsCode\SmsCode;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use common\extensions\cloudStorage\CloudStorage;
use common\models\UploadImage;

/**
 * UserController implements the CRUD actions for PcallUser model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'index', 'view', 'update', 'delete', 'send-sms-code', 'upload-img'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all PcallUser models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PcallUserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PcallUser model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PcallUser model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PcallUser();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PcallUser model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PcallUser model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PcallUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return PcallUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PcallUser::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function getSmsParams() {
        $params = Yii::$app->params['smsCode'];
        if(empty($params['templateParams'])) {
            $params['templateParams'] = [];
        }
        return $params;
    }
    
    
    public function actionSendSmsCode($phone) {
        if(!preg_match('/^1[34578]\d{9}$/', $phone)) {
            return json_encode([
            'code'=>'ERROR',
            'msg'=>'手机号码不正确'
        ]);
        }
        $smsCode = new SmsCode([
            'userId'=>(int)Yii::$app->user->id,
            'phoneNum'=>$phone,
            'type'=>'setPhone'
        ]);
        $ret = $smsCode->send([
            'sign'=>'叫你起床', 
            'templateCode'=>'SMS_12475232',
            'templateParams'=>[]
        ]);
        return json_encode([
            'code'=>$ret ? 'OK' : 'ERROR',
            'msg'=>$ret ? null : $smsCode->errorMsg
        ]);
    }
    
    /**
     * 上传照片
     */
    public function actionUploadImg() {
        if(isset($_FILES['file'])) {
            
            $file = UploadedFile::getInstanceByName('file');
            
            try {
                $uploadRet = $this->uploadImage($file->tempName, (int)Yii::$app->user->id);
                $code = 'OK';
                $msg = null;
            } catch (\Exception $ex) {
                $uploadRet = null;
                $code = 'ERROR';
                $msg = $ex->getMessage();
            }
            
            return json_encode([
                'code'=>'OK',
                'msg'=>null,
                'data'=>$uploadRet
            ]);
        }
        return json_encode([
            'code'=>"ERROR",
            'msg'=>'找不到文件',
        ]);
    }

    
    public function uploadImage($tempImageFile, $userId) {
        $imageSize = getimagesize($tempImageFile);
        if(!$imageSize) {
            throw new \Exception('获取不到图片的尺寸:');
        } else if($imageSize[0]<200) {
            throw new \Exception('图片宽度不能少于200像素:');
        } else if($imageSize[1]<200) {
            throw new \Exception('图片高度不能少于200像素:');
        }
        //保存图片到云存储
        $tempImageFileContent = file_get_contents($tempImageFile);
        $path = \common\modules\feed\models\Feed::getPictureStorePath(Yii::$app->user->id, 'jpg', md5($tempImageFileContent), randStr(16));
        $fileInfo = CloudStorage::uploadUserPicture($tempImageFile, $path);

        $uploadImageModel = UploadImage::add($userId, UploadImage::TYPE_CALL_COVER, $fileInfo['url'], $imageSize[0], $imageSize[1]);
        if($fileInfo) {
            return [
                'id'=>$uploadImageModel->id,
                'url'=>$fileInfo['url'],
                'width'=>$imageSize[0],
                'height'=>$imageSize[1]
            ];
        } else {
            throw new \Exception('上传图片到云服务器失败:'.CloudStorage::getError());
        }
        
    }
}
