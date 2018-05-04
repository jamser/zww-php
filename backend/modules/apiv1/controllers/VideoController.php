<?php

namespace backend\modules\apiv1\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use comm\video\models\Video;
use backend\models\VideoCreateForm;
use common\base\ErrCode;
use common\base\Response;
use comm\video\eventHandlers\Video as VideoEventHandlers;


/**
 * FileController implements the CRUD actions for File model.
 */
class VideoController extends Controller
{

    /**
     * 修改视频状态
     * @param int $id 视频ID
     */
    public function actionChangeStatus($id) {
        $model = $id>0 ? Video::findAcModel((int)$id) : null;
        if(!$model) {
            return Response::error(404, '找不到对应的视频');
        }
        $postData = Yii::$app->getRequest()->post();
        if(!isset($postData)) {
            return Response::error(400, '缺少状态值');
        }

        $model->changeStatus((int)$postData['status']);
        Response::success();
    }
    
    /**
     * 删除视频
     * @param int $id 视频ID
     */
    public function actionDelete($id) {
        $model = $id>0 ? Video::findAcModel((int)$id) : null;
        if(!$model) {
            return Response::error(404, '找不到对应的视频');
        }
        $model->delete();
        Response::success();
    }
    
    /**
     * 文件列表
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new VideoCreateForm;
        if($model->load($_POST, 'data') && ($videoModel=$model->save())) {
            return Response::success($videoModel->id);
        }
        $errors = $model->getFirstErrors();
        return Response::error(ErrCode::FORM_VALIDATE, $errors ? array_shift($errors) : '数据不能为空');
    }
    
    /**
     * 创建视频声音 暂时只向阿里云MTS提交了转码请求 , 并不处理或标记处理结果
     * @param int $id
     */
    public function actionCreateMp3($id) {
        $id = (int)$id;
        $video = $id ? Video::findAcModel($id) : null;
        if(!$video) {
            return Response::error(404, '找不到对应的视频');
        }
        
        VideoEventHandlers::runAddMp3($video);
        
        return Response::success();
    }
}
