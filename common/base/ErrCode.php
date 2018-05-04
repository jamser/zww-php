<?php

namespace common\base;

class ErrCode {
    
    const NONE = 0;
    
    const INVALID_PARAMS = 10001;//参数不合法
    const MISS_PARAMS = 10002;//缺少参数
    const FORM_VALIDATE_FAIL = 10003;//表单验证失败
    const ACCESS_DEINED = 10004;//无权限查看
    
    const USER_NAME_EXISTS = 20001;//用户名已存在
    const USER_SAVE_FAIL = 20002;//用户保存失败
    const USER_DATA_SAVE_FAIL = 20003;//用户资料保存失败
    
    const ORDER_SAVE_FAIL = 30001;//订单保存失败
    const ORDER_PAY_ERROR = 30002;//订单支付失败
    
    const WALLET_VIRTUAL_MONEY_NOT_ENOUGH = 40001;//钱包虚拟金额不足
    const WALLET_VIRTUAL_MONEY_SAVE_FAIL = 40002;//钱包虚拟金额保存失败
    
    #支付错误
    
    public static $descriptions = [
        self::NONE => '成功',
        self::INVALID_PARAMS => '参数不合法',
        
        self::USER_NAME_EXISTS => '用户名已存在',
        self::USER_SAVE_FAIL => '用户保存失败',
        self::USER_DATA_SAVE_FAIL => '用户资料保存失败',
    ];
    
}
