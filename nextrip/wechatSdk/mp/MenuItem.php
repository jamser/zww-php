<?php

namespace WechatSdk\mp;

use WechatSdk\utils\MagicAttributes;
use Closure;

/**
 * 菜单项
 *
 * @property array $sub_button
 */
class MenuItem extends MagicAttributes {

    /**
     * 实例化菜单
     *
     * @param string $name
     * @param string $type
     * @param [] $propertys
     */
    public function __construct($name, $propertys = []) {
        $this->with('name', $name);

        if ($propertys) {
            foreach($propertys as $key=>$value) {
                if($key==='name') {
                    continue;
                }
                $this->with($key, $value);
            }
        }
    }

    /**
     * 设置子菜单
     *
     * @param array $buttons
     *
     * @return MenuItem
     */
    public function buttons($buttons) {
        if ($buttons instanceof Closure) {
            $buttons = $buttons($this);
        }

        if (!is_array($buttons)) {
            throw new Exception('子菜单必须是数组或者匿名函数返回数组', 1);
        }

        $this->with('sub_button', $buttons);

        return $this;
    }

    /**
     * 添加子菜单
     *
     * @param MenuItem $button
     */
    public function button(MenuItem $button) {
        $subButtons = $this->sub_button;

        $subButtons[] = $button;

        $this->with('sub_button', $subButtons);
    }

    /**
     * 通过一个按钮数组 返回一个item 
     * @param array $buttonData 按钮数组
     * @return static
     */
    public static function getByButtonData($buttonData) {
        if(!empty($buttonData['sub_button'])) {
            $item = new static($buttonData['name']);
            foreach($buttonData['sub_button'] as $subButtonData) {
                $item->button(static::getByButtonData($subButtonData));
            }
        } else {
            $item = new static($buttonData['name'], $buttonData);
        }
        return $item;
    }
}
