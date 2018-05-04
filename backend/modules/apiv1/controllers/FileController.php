<?php

namespace backend\modules\apiv1\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

use nextrip\helpers\Format;
use nextrip\helpers\File as FileHelper;

use common\models\File;
use common\models\VideoSnapshotJob;
use common\base\ErrCode;
use common\base\Response;
use common\helpers\Mp4ToMp3;
use common\helpers\VideoSnapshot;

use OSS\OssClient;
use OSS\Core\OssException;

/**
 * FileController implements the CRUD actions for File model.
 */
class FileController extends Controller
{

    /**
     * 文件列表
     * @return mixed
     */
    public function actionList($type=null)
    {
        $userId = (int)Yii::$app->user->id;
        $dataProvider = new ActiveDataProvider([
            'query' => File::find()->where('user_id='.$userId.' AND '.($type ? 'type='.(int)$type : ''))->orderBy('id DESC'),
            //'pagination'=>['pageSize'=>20]
        ]);
        $models = $dataProvider->getModels();
        $ret = [
            'pageCount'=>$dataProvider->getCount(),
            'models'=>[]
        ];
        foreach($models as $model) {
            $ret['models'][] = $model->toArray();
        }
        return Response::success($ret);
    }

    /**
     * 上传文件
     * @param int $type 上传类型 goodsCover
     **/
    public function actionUploadImg($type) {
        $ossConfig = Yii::$app->params['aliOss']['img'];
        
        $userId = Yii::$app->user->id;
        $image =  UploadedFile::getInstanceByName('img');
        if(!$image) {
            Response::error(ErrCode::UPLOAD_FILE, '找不到上传的文件');
        } else if($image->getHasError()) {
            Response::error(ErrCode::UPLOAD_FILE, '文件上传失败, 错误代码 : '.$image->error);
        }
        $ext = strtolower($image->getExtension());
        if(!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            Response::error(ErrCode::UPLOAD_FILE, '图片格式不正确, 只支持jpg, png');
        }
        $imgSize = getimagesize($image->tempName);
        if(!$imgSize) {
            Response::error(ErrCode::UPLOAD_FILE, '获取上传图片尺寸失败');
        }
        
        if($type==='goodsCover') {
            $savePath = 'goods/user/'.$userId.'/'.date('Ym').'/'.time(). '_'. randStr(6).'.'.$ext;
            $this->upload($image->tempName, $savePath, $ossConfig);
            
            $fileModel = new File();
            $fileModel->user_id = $userId;
            $fileModel->type = File::TYPE_IMG;
            $fileModel->name = '商品封面_'.date('Y-m-d H:i:s');
            $fileModel->url = $ossConfig['publicHost'].'/'.$ossConfig['prefix'].$savePath;
            $fileModel->data = Format::toStr([
                'width'=>(int)$imgSize[0],
                'height'=>(int)$imgSize[1],
            ]);
            $fileModel->save(false);
            return Response::success($fileModel->toArray());
        }
        
        return Response::error(400, '类型'.$type.'无效');
    }


    protected function upload($filePath, $savePath, $ossConfig) {
        
        $accessKeyId = $ossConfig['accessKeyId'];
        $accessKeySecret = $ossConfig['accessKeySecret'];
        $endpoint = YII_ENV!=='prod' ?  $ossConfig['endpointPublic'] :  $ossConfig['endpointInner'];
        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);

        return $ossClient->uploadFile($ossConfig['bucket'], $ossConfig['prefix'].$savePath, $filePath);
    }

    /**
     * 提交mp3转换任务
     * @param type $id
     */
    public function actionMp3JobSubmit($id) {
        $file = $this->findModel($id);
        $mp4ToMp3 = new Mp4ToMp3($file);
        $mp4ToMp3->createMp3();
        Response::success();
    }
    
    /**
     * 查询mp3转换任务
     * @param type $id
     */
    public function actionMp3JobQuery($id) {
        $file = $this->findModel($id);
        $mp4ToMp3 = new Mp4ToMp3($file);
        
        $jobResultList = $file->getArrayFormatAttribute('data', 'mp3Job.response.JobResultList');
        if(!$jobResultList) {
            if($file->getArrayFormatAttribute('data', 'mp3')) {
                Response::error(ErrCode::MTS_TRANSCODE_DONE, '多媒体任务已经完成, 请刷新页面');
            } else {
                Response::error(ErrCode::MTS_TRANSCODE_FAILED, '多媒体任务不存在 , 请重新提交');
            }
        }
        
        $ret = $mp4ToMp3->getMp3JobResult($jobResultList);
        
        $ossConfig = Yii::$app->params['aliOss']['media'];
        
        if($ret->State==='TranscodeSuccess') {
            $mp3File = new File([
                'user_id'=>$file->user_id,
                'type'=>File::TYPE_MP3,
                'name'=>$file->name.'_mp3',
                'url'=>$ossConfig['publicHost'].'/'.$ret->Output->OutputFile->Object,
                'data'=>  Format::toStr([
                    'fromFile'=>$file->id
                ])
            ]);
            $mp3File->save(false);
            
            $fileData = $file->getArrayFormatAttribute('data');
            $fileData['mp3'] = [
                'id'=>$mp3File->id,
                'url'=>$mp3File->url
            ];
            unset($fileData['mp3Job']);
            $file->data = Format::toStr($fileData);
            $file->updateAttributes(['data']);
            Response::success($fileData['mp3']);
        } else if($ret->State==='Transcoding') {
            Response::error(ErrCode::MTS_TRANSCODE_FAILED, '多媒体转码正在进行中..');
        } else {
            Response::error(ErrCode::MTS_TRANSCODE_FAILED, '多媒体转码还未完成, 状态为: '.$ret->State);
        }
        
    }
    
    /**
     * 截图任务
     * @param type $id
     * @param type $ms
     * @param type $name
     */
    public function actionSnapshot($id, $ms, $name) {
        $model = new VideoSnapshotJob();
        $model->ms = (int)$ms;
        $model->snapshot_name = trim($name);
        $model->file_id = (int)$id;
        if($model->save()) {
            if($model->status==VideoSnapshotJob::STATUS_SUBMIT_OK) {
                Response::success([
                    'id'=>$model->id
                ]);
            } else {
                $jobResult = unserialize($model->job_result);
                $response = $jobResult['submit'];
                Response::error(ErrCode::VIDEO_SNAPSHOT_ERROR, '截图任务提交失败:'.$response->SnapshotJob->Message);
            }
        } else {
            $firstErrors = $model->getFirstErrors();
            $firstError = array_shift($firstErrors);
            Response::error(ErrCode::FORM_VALIDATE, '截图任务提交失败:'.$firstError);
        }
        
        $videoSnapshot = new VideoSnapshot($file);
        $videoSnapshot->handleMp4($ms);
        
        Response::success();
        if($ret->SnapshotJob->State!='Success') {
            Response::error(ErrCode::MTS_TRANSCODE_FAILED, '截图失败 : '.$ret->SnapshotJob->Message);
        } else {
            $ossConfig = Yii::$app->params['aliOss']['media'];
            
            $url = $ossConfig['publicHost'].'/'.$ret->SnapshotJob->SnapshotConfig->OutputFile->Object;
            $filePath = Yii::getAlias('@backend/runtime/temp').'/'.uniqid().'.jpg';
            FileHelper::grabFileAndSave('https:'.$url, $filePath);
            
            $imgSize = getimagesize($filePath);
            
            $sameNameFile = File::find()->where('name=:name AND type='.File::TYPE_PANO_IMG,[
                ':name'=>$name
            ])->one();
            
            if($sameNameFile) {
                $thumbFile = $sameNameFile;
                $thumbFile->url=$ossConfig['publicHost'].'/'.$ret->SnapshotJob->SnapshotConfig->OutputFile->Object;
                $thumbFile->data = Format::toStr([
                        'fromFile'=>$file->id,
                        'width'=>$imgSize[0],
                        'height'=>$imgSize[1],
                        'ms'=>$ms
                    ]);
            } else {
                $thumbFile = new File([
                    'user_id'=>$file->user_id,
                    'type'=>File::TYPE_PANO_IMG,
                    'name'=>$name,
                    'url'=>$ossConfig['publicHost'].'/'.$ret->SnapshotJob->SnapshotConfig->OutputFile->Object,
                    'data'=>  Format::toStr([
                        'fromFile'=>$file->id,
                        'width'=>$imgSize[0],
                        'height'=>$imgSize[1],
                        'ms'=>$ms
                    ])
                ]);
            }
            $thumbFile->save(false);
            
            @unlink($filePath);
            
            Response::success([
                'id'=>$thumbFile->id,
                'url'=>$thumbFile->url,
                'width'=>$imgSize[0],
                'height'=>$imgSize[1]
            ]);
        }
    }
    
    /**
     * 查询截图任务
     * @param type $id
     */
    public function actionSnapshotJobQuery($id) {
        $job = VideoSnapshotJob::findAcModel((int)$id);
        if(!$job) {
            Response::error(ErrCode::NOT_FOUND, '找不到对应的任务');
        } else if($job->status==VideoSnapshotJob::STATUS_JOB_OK || $job->status==VideoSnapshotJob::STATUS_JOB_ERROR) {
            Response::error(ErrCode::VIDEO_SNAPSHOT_ERROR, '任务已经完成了');
        }
        
        $file = $job->getFile();
        $videoSnapshot = new VideoSnapshot($file);
        $jobRet = unserialize($job->job_result);
        $jobSubmitRet = $jobRet['submit'];
        $ret = $videoSnapshot->getJobResult($jobSubmitRet);
        
        $ossConfig = Yii::$app->params['aliOss']['media'];
        
        if($ret->State==='Success') {
            $thumbFile = new File([
                'user_id'=>$file->user_id,
                'type'=>File::TYPE_PANO_IMG,
                'name'=>$job->snapshot_name,
                'url'=>$ossConfig['publicHost'].'/'.$ret->SnapshotConfig->OutputFile->Object,
            ]);
            
            
            $filePath = Yii::getAlias('@backend/runtime/temp').'/'.uniqid().'.jpg';
            FileHelper::grabFileAndSave('https:'.$thumbFile->url, $filePath);
            
            $imgSize = getimagesize($filePath);
            
            $thumbFile->data = serialize([
                'fromFile'=>$file->id,
                'width'=>$imgSize[0],
                'height'=>$imgSize[1],
            ]);
            $thumbFile->save(false);

            $job->status = VideoSnapshotJob::STATUS_JOB_OK;
            $job->snapshot_file_id = (int)$thumbFile->id;
            $job->updateAttributes(['status', 'snapshot_file_id']);
            
            Response::success([
                'id'=>$thumbFile->id,
                'width'=>$imgSize[0],
                'height'=>$imgSize[1],
                'url'=>$thumbFile->url
            ]);
        } else {
            Response::error(ErrCode::VIDEO_SNAPSHOT_ERROR, '截图任务失败 : '.$ret->Message.'..');
        }
        
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
