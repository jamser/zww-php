<?php


/* @var $this yii\web\View */
/* @var $user common\models\User */

use yii\helpers\Html;
use common\models\Setting;

$this->params['bodyCssClass'] = 'gray-bg';
$this->title = '账号设置';

?>
<div class="user-update">
    
    <div class="form-group">
        <div class="weui-cell weui-cell_vcode">
            <div class="weui-cell__hd">
                <label class="weui-label">手机号</label>
            </div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="tel" placeholder="请输入手机号">
            </div>
            <div class="weui-cell__ft">
                <button class="weui-vcode-btn">获取验证码</button>
            </div>
        </div>
    </div>
    
    <div class="weui-btn-area">
        <a class="weui-btn weui-btn_primary" href="javascript:" id="showTooltips">确定</a>
    </div>
    
</div>