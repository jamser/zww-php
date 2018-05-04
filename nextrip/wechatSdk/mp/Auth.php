<?php

namespace WechatSdk\mp;

use WechatSdk\utils\Bag;
use WechatSdk\helper\Url;
use WechatSdk\helper\Helper;

/**
 * OAuth 网页授权获取用户信息
 */
class Auth {

    /**
     * 应用ID
     *
     * @var string
     */
    protected $appId;

    /**
     * 应用secret
     *
     * @var string
     */
    protected $appSecret;

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
     * @var \WechatSdk\utils\Bag
     */
    protected $authorizedUser;

    const API_USER = 'https://api.weixin.qq.com/sns/userinfo';
    const API_TOKEN_GET = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    const API_TOKEN_REFRESH = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';
    const API_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    
    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->http = new Http(); // 不需要公用的access_token
        $this->input = new Input();
    }

    /**
     * 删除input值
     * @param array $keys 需要删除的输入值数组
     */
    public function delInput($keys) {
        foreach ($keys as $key) {
            if ($this->input->has($key)) {
                $this->input->forget($key);
            }
        }
    }

    /**
     * 获取已授权用户
     *
     * @return \WechatSdk\utils\Bag | null
     */
    public function user($code) {
        if ($this->authorizedUser) {
            return $this->authorizedUser;
        }
        
        $permission = $this->getAccessPermission($code);
        
        if ($permission['scope'] !== 'snsapi_userinfo') {
            $user = new Bag(array('openid' => $permission['openid']));
        } else {
            $user = $this->getUser($permission['openid'], $permission['access_token']);
        }

        return $this->authorizedUser = $user;
    }

    /**
     * 通过授权获取用户
     *
     * @param string $to
     * @param string $scope
     * @param string $state
     *
     * @return Bag | null
     */
    public function authorize($to = null, $scope = 'snsapi_userinfo', $state = null, $getUser=true) {
        if($state===null) {
            $state = !empty($_SESSION['wcMpState']) ? $_SESSION['wcMpState'] : null;
        }
        $getState = $this->input->get('state');
        $code = $this->input->get('code');
        if(!$code || !$getState || $getState!=$state) {
            $this->getCode($to, $scope, $state);
        }

        if($getUser) {
            return $this->user($code);
        } else {
            return $code;
        }
    }

    public function getCode($to, $scope, $state=null) {
        $to === null && ($to = Url::current());
        $state === null && ($state = Helper::randStr(16));
        $_SESSION['wcMpState'] = $state;
        $params = array(
            'appid' => $this->appId,
            'redirect_uri' => $to,
//            'redirect_uri' => 'http://p.365zhuawawa.com?r=game/index',
//            'redirect_uri' => 'http://192.168.2.224:8080/icrane/api/wx/getAccessToken',
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state,
        );

        $url = self::API_URL . '?' . http_build_query($params) . '#wechat_redirect';
        Url::redirect($url);
    }
    
    /**
     * 获取access token
     *
     * @param string $code
     *
     * @return string
     */
    public function getAccessPermission($code) {
        $params = array(
            'appid' => $this->appId,
            'secret' => $this->appSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
        );

        return $this->http->get(self::API_TOKEN_GET, $params);
    }

    /**
     * 获取用户信息
     *
     * @param string $openId
     * @param string $accessToken
     *
     * @return array
     */
    public function getUser($openId, $accessToken) {
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
