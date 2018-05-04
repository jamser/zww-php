微信 SDK

## 目录结构
    app APP应用
    helper 帮助类
    models 微信模型
    mp  公众账号应用
    utils 工具类
    web WEB应用
    LogInterface.php 日志类接口
    autoload.php 自动载入类

## 安装

环境要求：PHP >= 5.3.0

##配置
    (1).扩展 类 CacheInterface  LockInterface StorageBase
    (2).配置Confg文件中的 $cacheClass, $storeClass, $lockClass 为对应扩展类的位置

## 使用授权

    ```php
    <?php
        require_once "wechatSdk/autoload.php"; // 路径请修改为你具体的实际路径
        
        ## 网页授权(扫描二维码登录)
        $wechatWebAuth = new \WechatSdk\web\Auth($appId, $appSecret);
        //\WechatSdk\helper\Url::setBeforeExit([Yii::getLogger(), 'flush'], [true]);//do something
        $openData = $wechatWebAuth->authorize();
    
        ## 公众账号获取openId
        $wechatMpAuth = new \WechatSdk\mp\Auth($appId, $appSecret);
        $openIdData = $wechatMpAuth->authorize(null, 'snsapi_base')->all();

        ## 公众账号通过openId获取用户资料
        $userApi = new WechatMpUser($appId, $appSecret);
        $openData = $userApi->get($openIdData['openid']);

        ## 公众账号授权并获取用户资料
        $wechatMpAuth = new \WechatSdk\mp\Auth($appId, $appSecret);
        //\WechatSdk\helper\Url::setBeforeExit([Yii::getLogger(), 'flush'], [true]);//do something
        $openData = $wechatMpAuth->authorize(WechatUrl::current(['as' => 'snsapi_userinfo', 'code' => null, 'state' => null]), 'snsapi_userinfo')->all();
        

        #较为完善的公众账号授权 加入重试机制
        $mpKey = 'mp key'; 
        $appId = 'app id'; 
        $appSecret = 'app secret'; 
        $wechatMpAuth = new \WechatSdk\mp\Auth($appId, $appSecret);
        $wechatMpAuth->setBeforeExit([Yii::getLogger(), 'flush'], [true]);
        $authScope = filter_input(INPUT_GET, 'as')==='snsapi_base' ? 'snsapi_base' : 'snsapi_userinfo';
        try {
            if($authScope==='snsapi_base') {
                $openIdData = $wechatMpAuth->authorize(null, 'snsapi_base')->all();
                $userApi = new \WechatSdk\mp\User($appId, $appSecret);
                $openData = $userApi->get($openIdData['openid']);
                if (!$openData->get('subscribe')) {
                    $wechatMpAuth->delInput(['code','state']);
                    goto AUTH_SNSAPI_USERINFO;
                }
            } else {
                AUTH_SNSAPI_USERINFO:
                $openData = $wechatMpAuth->authorize(\WechatSdk\helper\Url::current(['as' => 'snsapi_userinfo', 'code' => null, 'state' => null]), 'snsapi_userinfo')->all();
            }
        } catch (\WechatSdk\mp\Exception $ex) {
            $wechatMpAuth->delInput(['code','state']);
            $openData = $wechatMpAuth->authorize(\WechatSdk\helper\Url::current(['as' => 'snsapi_userinfo', 'code' => null, 'state' => null]), 'snsapi_userinfo')->all();
        }
        
        $openUser = new \WechatSdk\models\User($mpKey, $openData, 1);
        

##使用微信支付