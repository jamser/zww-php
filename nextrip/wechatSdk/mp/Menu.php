<?php

namespace WechatSdk\mp;

use Closure;

/**
 * 菜单
 *
 * @property array $sub_button
 */
class Menu {

    const API_CREATE = 'https://api.weixin.qq.com/cgi-bin/menu/create';
    const API_GET = 'https://api.weixin.qq.com/cgi-bin/menu/get';
    const API_DELETE = 'https://api.weixin.qq.com/cgi-bin/menu/delete';

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret) {
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }

    /**
     * 设置菜单
     *
     * @return bool
     */
    public function set($menus) {
        if ($menus instanceof Closure) {
            $menus = $menus($this);
        }

        if (!is_array($menus)) {
            throw new Exception('子菜单必须是数组或者匿名函数返回数组', 1);
        }

        $menus = $this->extractMenus($menus);

        $this->http->jsonPost(self::API_CREATE, array('button' => $menus));

        return true;
    }

    /**
     * 获取菜单
     *
     * @return array
     */
    public function get() {
        $menus = $this->http->get(self::API_GET);

        return empty($menus['menu']['button']) ? array() : $menus['menu']['button'];
    }

    /**
     * 删除菜单
     *
     * @return bool
     */
    public function delete() {
        $this->http->get(self::API_DELETE);

        return true;
    }

    /**
     * 转menu为数组
     *
     * @param array $menus
     *
     * @return array
     */
    protected function extractMenus(array $menus) {
        foreach ($menus as $key => $menu) {
            $menus[$key] = $menu->toArray();

            if ($menu->sub_button) {
                $menus[$key]['sub_button'] = $this->extractMenus($menu->sub_button);
            }
        }

        return $menus;
    }

    /**
     * 通过菜单数组获取所有的items
     * @param [] $menuData
     * @return MenuItem[]
     */
    public static function items($menuData) {
        $menuItems = [];
        foreach($menuData as $buttonData) {
            $menuItems[] = MenuItem::getByButtonData($buttonData);
        }
        return $menuItems;
    }
    
    /**
     * 验证数组格式的菜单是否合法
     * @param array $menuData
     * @return true|string 返回true 或者 错误的信息
     */
    public static function validateData($menuData) {
        $error = null;
        if(empty($menuData)) {
            $error = '菜单按钮数据为空';
        } else if(!is_array($menuData)) {
            $error = '菜单按钮不是一个数组';
        } else {
            $count = count($menuData);
            if($count<1 || $count>3) {
                $error = '一级菜单数量必须在1-3个之间';
                goto RETURN_MENU_ERROR;
            }
            $i = 1;
            foreach($menuData as $buttonData) {
                if( ($error = static::validateButtonData($buttonData, $i++, 0)) ) {
                    goto RETURN_MENU_ERROR;
                }
            }
        }
        RETURN_MENU_ERROR:
        return $error===null ? true : $error;
    }
    
    /**
     * 验证按钮数据是否合法
     * @param array $buttonData 按钮数据
     * @param integer $num 一级菜单序号 1-3
     * @param integer $subNum 二级菜单序号 0-5 0表示一级菜单
     * 
     */
    public static function validateButtonData($buttonData, $num, $subNum) {
        $error = null;
        $position = "第 $num 列菜单".($subNum ? "第{$subNum}个子菜单" : '');
        if(empty($buttonData['name'])) {
            $error = $position.' 名称为空';
            goto RETURN_BUTTON_ERROR;
        }
        if(!empty($buttonData['sub_button'])) {//包含子菜单
            if($subNum!==0) {
                $error = $position.' 包含一组子菜单';
                goto RETURN_BUTTON_ERROR;
            }
            $subButtonCount = count($buttonData['sub_button']);
            if($subButtonCount>5) {
                $error = "第 $num 列菜单 子菜单元素超过5个";
                goto RETURN_BUTTON_ERROR;
            }
            $n = 1;
            foreach($buttonData['sub_button'] as $subButtonData) {
                if(true!==($validateMsg = static::validateButtonData($subButtonData, $num, $n++))) {
                    $error = $validateMsg;
                    goto RETURN_BUTTON_ERROR;
                }
            }
        } else {//不包含子菜单
            switch ($buttonData['type']) {
                case 'view'://打开URL
                    if(empty($buttonData['url'])) {
                        $error = $position. ' url属性不能为空';
                    }
                    break;
                case 'click'://点击事件
                case 'pic_sysphoto'://弹出系统拍照发图 
                case 'pic_photo_or_album'://弹出拍照或者相册发图
                case 'pic_weixin'://弹出微信相册发图器
                case 'location_select'://弹出地理位置选择器
                case 'scancode_push'://扫描二维码推送消息
                case 'scancode_waitmsg'://扫描二维码 显示等待消息返回
                    if(empty($buttonData['key'])) {
                        $error = $position. ' key属性不能为空';
                    }
                    break;
                case 'media_id'://发送多媒体消息 需要填写多媒体ID
                case 'view_limited'://跳转图文消息URL 需要填写多媒体ID
                    if(empty($buttonData['media_id'])) {
                        $error = $position. ' media_id属性不能为空';
                    }
                    break;
                default:
                    $error = $position. " 类型为 {$buttonData['type']} 不在支持的范围内";
                    break;
            }
        }
        RETURN_BUTTON_ERROR:
        return $error===null ? true : $error;
    }
}
