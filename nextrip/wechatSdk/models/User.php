<?php

namespace WechatSdk\models;

/*
 * 微信用户模型
 */
class User {

    /**
     * 公众平台用户唯一ID
     */
    public $unionid;

    /**
     * 对于公众账号唯一ID
     * 用户openid
     * @var string
     */
    public $openid;

    /**
     * 用户昵称
     * @var string
     */
    public $nickname;

    /**
     * 性别
     * @var integer
     */
    public $sex;

    /**
     * 国家
     * @var string
     */
    public $country;

    /**
     * 省份
     * @var string
     */
    public $province;

    /**
     * 城市
     * @var string
     */
    public $city;

    /**
     * 头像
     * @var string
     */
    public $headimgurl;

    /**
     * 用户特权信息 json字符串 网页获取资料返回
     * @var string
     */
    public $privilege;

    /**
     * 是否已经关注 通过unionId获取时返回
     * @var
     */
    public $subscribe;

    /**
     * 用户关注时间 通过unionId获取时返回
     * @var integer
     */
    public $subscribe_time;

    /**
     * 公众号运营者对粉丝的备注
     * @var string
     */
    public $remark;

    /**
     * 用户所在的分组ID
     * @var int 
     */
    public $groupid;
    
    /**
     * 第三方用户信息
     * @var array
     */
    protected $_openInfo;

    /**
     * 当前openId所使用的应用ID
     * @var integer
     */
    protected $_appId;
    
    /**
     * 是否获取了全部信息
     * @var bool
     */
    protected $_hasAllInfo;


    /**
     * @param string $appId 微信应用ID
     * @param array $openInfo 第三方资料
     * @param bool $hasAllInfo 资料是否完整 正常情况下可以获取到用户的所有资料 , 值设为1 ;  有时候由于权限问题不能获取到所有的用户资料 设置值为0
     */
    public function __construct($appId, $openInfo, $hasAllInfo) {
        $this->_appId = $appId;
        if($openInfo) {
            $this->setAttributesByOpenInfo($openInfo, $hasAllInfo);
        }
    }
    
    /**
     * @param array $openInfo 第三方资料 
     * @param bool $hasAllInfo 资料是否完整 
     */
    public function setAttributesByOpenInfo($openInfo, $hasAllInfo) {
        $this->_openInfo = $openInfo;
        $this->_hasAllInfo = $hasAllInfo;
        foreach($openInfo as $key=>$val) {
            $this->$key = $val;
        }
    }
    
    /**
     * 判断当前的用户资料是否完整
     * @return bool 
     */
    public function hasAllInfo() {
        return $this->_hasAllInfo ? 1 : 0;
    }
    
    /**
     * 获取所有数据
     * @return array 
     */
    public function getOpenInfo() {
        return $this->_openInfo;
    }
    
    /**
     * 获取应用ID
     * @return string
     */
    public function getAppId() {
        return $this->_appId;
    }
    
    /**
     * 获取性别
     * @return integer
     */
    public function getGender() {
        return (int) $this->sex;
    }

    /**
     * 获取头像 最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空
     * @return string
     */
    public function getAvatar($size=0) {
        $avatar = '';
        if ($this->headimgurl) {
            if(strpos($this->headimgurl, 'http')===0) {
                $this->headimgurl = strtr($this->headimgurl, [
                    'http://'=>'//',
                    'https://'=>'//',
                ]);
            }
            $avatar = $this->headimgurl;
            $allowSizes = [0,46,64,96,132];
            if(in_array($size, $allowSizes)) {
                if($size!=0) {
                    $avatar = substr($avatar, 0, strlen($avatar)-1).$size;
                }
            } else {
                throw new \Exception("无效的头像大小 {$size}");
            }
        }
        return $avatar;
    }
    
    /**
     * 获取unionId 
     * @param bool $autoGenerate 当不存在时是否由appNumId和openId生成一个值
     * @return string
     */
    public function getUnionId($autoGenerate=true) {
        return $this->unionid ? $this->unionid : ($autoGenerate ? static::generateUnionId($this->openid, $this->_appId) : '');
    }
    
    /**
     * 自动创建一个unionId
     * @param string $openId 第三方ID
     * @param string $appId 微信号的APPID
     * @return string
     */
    public static function generateUnionId($openId, $appId) {
        return $appId . '_' . $openId;
    }
}
