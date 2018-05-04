<?php

namespace backend\controllers;

use Yii;


use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use common\base\Response;
use common\base\ErrCode;
use common\models\File;
use common\models\FileSearch;

use backend\models\PublishAssetForm;


/**
 * FileController implements the CRUD actions for File model.
 */
class FileController extends Controller
{

    /**
     * Lists all File models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FileSearch(); 
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams); 
        $dataProvider->setSort([
            'attributes'=>[
                'id'
            ],
            'defaultOrder' => [
                'id' => 'DESC'
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'=>$searchModel
        ]);
    }
    
    public function actionCopyTo($id, $toType) {
        $file = $this->findModel($id);
        $fromOssConfig = $toOssConfig = null;
        $ossConfigs = Yii::$app->params['aliOss'];
        foreach($ossConfigs as $type=>$ossConfig) {
            if($toType===$type) {
                $toOssConfig = $ossConfig;
            }
            if(strpos($file->url, $ossConfig['publicHost'])===0) {
                $fromOssConfig = $ossConfig;
            }
        }
        if(!$fromOssConfig || !$toOssConfig) {
            return Response::renderMsg('操作失败', '源bucket或目标bucket不存在');
        }
        if($fromOssConfig['bucket']==$toOssConfig['bucket']) {
            return Response::renderMsg('操作失败', '文件所在的oss bucket 和迁移的目标Bucket相同');
        }
        $fromObject = substr($file->url, strlen($fromOssConfig['publicHost'])+1);
        $ossClient = new \OSS\OssClient($toOssConfig['accessKeyId'], $toOssConfig['accessKeySecret'], $toOssConfig['endpoint'], true);
        $ossClient->copyObject($fromOssConfig['bucket'], $fromObject, 
                $toOssConfig['bucket'], $fromObject);
        
        return Response::renderMsg('操作成功', '复制的文件位置为：'.$toOssConfig['publicHost'].$fromObject);
    }

    /**
     * 发布资源
     * @param string $source 来源位置  以项目目录/webAsset 为起点
     * @param string $target 目标位置 处于 static下
     */
    public function actionPublishAsset($source, $target) {
        $form = new PublishAssetForm();
        if($form->load($_GET,'') && ($url=$form->save())) {
            return Response::renderMsg('上传成功!', 'URL : '.$url);
        }
        $errors = $form->getFirstErrors();
        $error = array_shift($errors);
        return Response::renderMsg('上传失败!', $error);
    }

    /**
     * Displays a single File model.
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
     * Creates a new File model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->render('create');
    }

    /**
     * Updates an existing File model.
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
     * Deletes an existing File model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        return Response::error(ErrCode::ACCESS_DEINED, '文件暂时不能删除..');
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the File model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return File the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = File::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
