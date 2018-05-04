<?php
namespace api\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use common\base\Response;

/**
 * 文件控制器
 */
class FileController extends \yii\web\Controller
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
                        'actions' => ['upload-avatar'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 上传头像
     */
    public function actionUploadAvatar() {
        Response::success();
    }
    
}
