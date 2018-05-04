短信验证码模块

##依赖于 helpers

##安装 
    #运行 data/init.sql

##配置方式:
 *      添加阿里大鱼apiKey api发送参数
 *      在配置文件params-local中添加
 *          ...
 *              aliDayu=>[
 *                  'apiKey'=>'',//阿里大鱼的apiKey
 *                  'apiSecret'=>''//阿里大鱼的apiSecret
 *              ]
 *          ...
 * 使用方式 : 
 *  1.发送验证码
       $smsCode = new SmsCode([
           'userId=>1,
           'type'=>'register',
           'phoneNum'=>'13012345678',
       ]);
       if($smsCode->send()) {
           //发送成功...
       } else {
           //发送失败...
           echo $smsCode->errorMsg;
       }
       
   2.对验证码进行验证
       $smsCode = new SmsCode([...]);
       if($smsCode->verify()) {
           //验证成功...
       } else {
           //验证失败...
           echo $smsCode->errorMsg;
       }