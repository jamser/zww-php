<?php
namespace frontend\modules\api\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

use common\base\ErrCode;
use common\base\Response;
use common\models\User;
use common\models\UploadFile;
use common\models\user\ChangeLog;


/**
 * 文件控制器
 */
class FileController extends Controller
{
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['logout', 'signup', 'login'],
                'rules' => [
                    [
                        'actions' => ['upload','upload-avatar'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function getUploadApi() {
        if(YII_ENV==='prod') {
            $api = new \nextrip\upload\UploadApiCloud;
        } else {
            $api = new \nextrip\upload\UploadApiLocalServer;
        }
        return $api;
    }
    
    /**
     * 上传文件
     * @param type $type
     */
    public function actionUpload($type) {
        $user = Yii::$app->user->identity;
        $types = [
            'avatar'=> UploadFile::TYPE_AVATAR,
            'cover'=> UploadFile::TYPE_COVER
        ];
        if(!isset($types[$type])) {
            Response::error(ErrCode::INVALID_PARAMS, '无效类型');
        }
        $uploadFile = new UploadFile([
            'user_id'=>(int)$user->id,
            'type'=>$types[$type],
            'file'=>UploadedFile::getInstanceByName('fileVal')
        ]);
        $path = '/'.$type.'s/'.date('Ym/d').'/'
                .md5(date('His'). uniqid()).'.'. strtolower($uploadFile->file->getExtension());
        if($uploadFile->saveFile($this->getUploadApi(), $path, 'img')) {
            Response::success([
                'id'=>$uploadFile->id,
                'url'=>$uploadFile->url,
            ]);
        } else {
            $firstErrors = $uploadFile->getFirstErrors();
            Response::error(ErrCode::FORM_VALIDATE_FAIL, $firstErrors?array_shift($firstErrors):'保存文件失败');
        }
    }
    
    /**
     * 上传头像
     */
    public function actionUploadAvatar() {
        $user = Yii::$app->user->identity;
        $uploadFile = new UploadFile([
            'user_id'=>(int)$user->id,
            'file'=>UploadedFile::getInstanceByName('fileVal')
        ]);
        $path = '/avatars/'.date('Ym/d').'/'
                .md5(date('His'). uniqid()).'.'. strtolower($uploadFile->file->getExtension());
        if($uploadFile->saveFile($this->getUploadApi(), $path, 'img')) {
            $changeLog = new ChangeLog([
                'user_id'=>(int)$user->id,
                'field'=>'avatar',
                'old_value'=>$user->avatar,
                'new_value'=>$uploadFile->url,
                'remark'=>'用户更换头像'
            ]);
            $user->avatar = '//'.$uploadFile->url;
            $user->updateAttributes(['avatar']);
            $changeLog->save(false);
            Response::success([
                'url'=>$uploadFile->url,
                'width'=>$uploadFile->width,
                'height'=>$uploadFile->height
            ]);
        } else {
            $firstErrors = $uploadFile->getFirstErrors();
            Response::error(ErrCode::FORM_VALIDATE_FAIL, $firstErrors?array_shift($firstErrors):'保存失败');
        }
        
        
    }
    
}
