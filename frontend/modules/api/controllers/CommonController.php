<?php

namespace frontend\modules\api\controllers;

use Yii;
use yii\filters\AccessControl;

use common\base\Response;
use common\base\ErrCode;
use common\models\SendSmsCodeForm;

use WechatSdk\mp\QRCode;

/**
 * Common 普通控制器
 */
class CommonController extends \yii\web\Controller
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
                        'actions'=> ['index', 'get-qrcode'],
                        'allow'=>true
                    ],
                    [
                        'actions' => ['send-sms-code'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }
    
    public function actionIndex() {
        
    }

    public function actionSendSmsCode() {
        $form = new SendSmsCodeForm([
            'user'=>Yii::$app->user->getIdentity()
        ]);
        if($form->load(Yii::$app->getRequest()->post(),'') && $form->send()) {
            return Response::success();
        }
        $firstErrors = $form->getFirstErrors();
        Response::error(ErrCode::FORM_VALIDATE_FAIL, $firstErrors ? array_shift($firstErrors) : '数据不能为空');
    }
    
    public function actionGetQrcode($type, $id) {
        $id = (int)$id;
        $cache = Yii::$app->getCache();
        
        $mpConfig = Yii::$app->params['wechatMps']['nt1'];
        
        $qrcodeRet = $cache->get($type.'Qrcode:'.$id);
        $qrCode = new QRCode($mpConfig['appId'], $mpConfig['appSecret']);
        if($qrcodeRet===false) {
            $qrcodeRet = $qrCode->forever( $type.':'. $id, QRCode::SCENE_QR_FOREVER_STR);
            $cache->set($type.'Qrcode:'.$id, $qrcodeRet, 864000);
        }
        return Response::success($qrCode->show($qrcodeRet->get('ticket')));
    }
}
