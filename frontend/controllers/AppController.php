<?php
namespace frontend\controllers;

use Yii;

class AppController extends Controller
{
    public $enableCsrfValidation = false;
    public $layout = false;
    
    public function actionLog() {
//        $userId = Yii::$app->getRequest()->post('userId', '');
        $key = Yii::$app->getRequest()->post('key', '');
        $value = Yii::$app->getRequest()->post('value', '');
        
        $file = fopen(Yii::getAlias('@frontend')."/runtime/logs/androidDebug.log",'a+');
        fwrite($file, " {$key} {$value} \n");
        fclose($file);
    }
    
    public function actionUserLogin() {
        
    }
    
    /**
     * 红包
     * @param type $key
     * @return type
     */
    public function actionRedPacket($key=null, $code=null) {
        $verifyKey = md5('wxRedPacket20180122');
        if($key===$verifyKey) {
            //微信登录  然后给对应的用户加币， 并且保存记录
            #获取UNION_ID
            Yii::$app->session->open();
            $mp = \nextrip\wechat\models\Mp::findAcModel('defaultMp');
            $wechatMpAuth = new \WechatSdk\mp\Auth($mp->app_id, $mp->app_secret);
            $wechatMpAuth->setBeforeExit([Yii::getLogger(), 'flush'], [true]);
            $authScope = filter_input(INPUT_GET, 'as')==='snsapi_base' ? 'snsapi_base' : 'snsapi_userinfo';

            
            try {
                if($authScope==='snsapi_base') {
                    $openIdData = $wechatMpAuth->authorize(null, 'snsapi_base')->all();
                    $userApi = new \WechatSdk\mp\User($mp->app_id, $mp->app_secret);
                    $openData = $userApi->get($openIdData['openid']);
                    if (!$openData->get('subscribe')) {
                        $wechatMpAuth->delInput(['code','state']);
                        goto AUTH_SNSAPI_USERINFO;
                    }
                } else {
                    AUTH_SNSAPI_USERINFO:
                    $code = $wechatMpAuth->authorize(\WechatSdk\helper\Url::current(['as' => 'snsapi_userinfo', 'code' => null]), 'snsapi_userinfo', null, false);
                }
            } catch (\WechatSdk\mp\Exception $ex) {
                $wechatMpAuth->delInput(['code','state']);
                $code = $wechatMpAuth->authorize(\WechatSdk\helper\Url::current(['as' => 'snsapi_userinfo', 'code' => null]), 'snsapi_userinfo', null, false);
            }
            
            $redirectUrl = "http://h5.365zhuawawa.com/H5/index.html?channel=h5fudanzhexue&code=".$code;
            return $this->redirect($redirectUrl);
        }
        return $this->redirect("http://h5.365zhuawawa.com/H5/index.html");
        //return $this->redirect(['/game/index', 'channel'=>'h5fudanzhexue']);
        //return $this->redirect(['/share/download']);
    }
}

