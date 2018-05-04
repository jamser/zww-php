<?php

namespace WechatSdk\web;

use WechatSdk\helper\Helper;
use WechatSdk\helper\Url;
use WechatSdk\utils\Http;
use WechatSdk\utils\Bag;

/**
 * 网站登录
 */
class Auth {

    const API_USER = 'https://api.weixin.qq.com/sns/userinfo';
    const API_TOKEN_GET = 'https://api.weixin.qq.com/sns/oauth2/access_token';//https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code
    const API_TOKEN_REFRESH = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';//https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=APPID&grant_type=refresh_token&refresh_token=REFRESH_TOKEN
    const API_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    
    /**
     * 应用ID
     *
     * @var string
     */
    private $appId;

    /**
     * 应用secret
     *
     * @var string
     */
    private $appSecret;

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * 输入
     *
     * @var Bag
     */
    protected $input;

    /**
     * 已授权用户
     *
     * @var \WechatSdk\Utils\Bag
     */
    protected $authorizedUser;
    
    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->http = new Http();
        $this->input = new Bag(filter_input_array(INPUT_GET));
    }
    
    public function getCode($redirectUri, $state, $scope='snsapi_login') {
        $redirectUri === null && ($redirectUri = Url::current());
        $state === null && ($state = Helper::randStr(16));
        $_SESSION['wcWebState'] = $state;
        $url = "https://open.weixin.qq.com/connect/qrconnect?appid={$this->appId}&redirect_uri={$redirectUri}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
        Url::redirect($url);
    }

    /**
     * 通过授权获取用户
     * @param string $return 返回值 user 或 userAccessToken
     * @param string $to
     * @param string $scope
     * @param string $state
     *
     * @return Bag | null
     */
    public function authorize($return='user', $to = null, $scope = 'snsapi_login', $state = null) {
        if($state===null) {
            $state = !empty($_SESSION['wcWebState']) ? $_SESSION['wcWebState'] : null;
        }
        $getState = $this->input->get('state');
        $code = $this->input->get('code');
        if(!$code || !$getState || $getState!=$state) {
            $this->getCode($to, $state, $scope);
        }
        
        $userAccessToken = $this->getUserAccessToken($code);
        if(!empty($userAccessToken->errcode)) {
            throw new \Exception('微信授权错误 : '.$userAccessToken->errcode.'-'.(!empty($userAccessToken->errmsg) ? $userAccessToken->errmsg : '未知错误'), $userAccessToken->errcode);
        }
        return $return === 'userAccessToken' ? $userAccessToken : $this->getUser($userAccessToken->openid, $userAccessToken->access_token);
    }

    /**
     * 获取access token
     *
     * @param string $code
     *
     * @return string
     */
    public function getUserAccessToken($code) {
        $params = array(
            'appid' => $this->appId,
            'secret' => $this->appSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
        );

        return new Bag($this->http->get(self::API_TOKEN_GET, $params));
    }
    
    /**
     * 刷新token
     * @param string $refreshToken 
     */
    public function refreshAccessToken($refreshToken) {
        $params = array(
            'appid' => $this->appId,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        );

        return $this->http->get(self::API_TOKEN_REFRESH, $params);
    }

    /**
     * 获取用户信息
     *
     * @param string $openId
     * @param string $accessToken
     *
     * @return array
     */
    protected function getUser($openId, $accessToken) {
        $queries = array(
            'access_token' => $accessToken,
            'openid' => $openId,
            'lang' => 'zh_CN',
        );

        $url = self::API_USER . '?' . http_build_query($queries);

        return new Bag($this->http->get($url));
    }

    
    /**
     * 设置退出前执行函数
     * @param function $func 可执行函数
     * @param array $params 对应的参数
     * @return \WechatSdk\mp\Auth
     */
    public function setBeforeExit($func, $params) {
        Url::setBeforeExit($func, $params);
        return $this;
    }
}
