<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>登录</title>
    <link rel="stylesheet" type="text/css" href="./products/css/reset.css" />

    <script type="text/javascript" src="./products/js/setfontsize.js">
    </script>
</head>

<body>
<div class="wrap">
    <div><img src="./products/img/Common/Common_BgImage_Top.png" /></div>

    <section class="login">

        <div class="login-1">
            <div class="username">
                <div class="oint">
                    <input type="text" />
                </div>
            </div>
            <div class="password">
                <div class="oint2">
                    <input type="password" />
                </div>
            </div>
            <div class="login_register">
                <input type="button" class="confire_login"/>
                <input type="button" class="register"/>

            </div>
            <div class="Forgot_password">

                <a href="javascript:void(0)">忘记密码? </a>
            </div>

        </div>

        <div class="Other_login_modes">
            <i></i><span>其他登录方式</span><i></i>

        </div>
        <div class="WeChat_login">
            <a href="javascript:void(0)"></a>
        </div>
        <div class="Agree_user_privacy">
            <span></span>
            <a href="javascript:void(0)">登录表示同意用户协议与隐私条款</a>
        </div>
    </section>

</div>

</body>
<style type="text/css">
    /*html,
    body {
        font-size: 100px;
        width: 100%;
        height: 100%;
    }
    * {
        margin: 0;
        padding: 0;
    }*/

    .wrap {
        width: 100%;
        height: 100%;
    }

    .wrap img {
        display: block;
        width: 100%;
        height: 100%;
    }

    .login {
        width: 5.6rem;
        height: 7.3rem;
        position: absolute;
        left: 0.4rem;
        top: 2rem;
        background: url("./products/img/User/User_Login_LoginBgImage.png") no-repeat;
        background-size: 5.6rem 7.3rem;
    }

    .login-1 {
        width: 4.05rem;
        height: 3.5rem;
        margin-top: 1.45rem;
        margin-left: 0.8rem;
    }
    .login-1 .username {
        width: 100%;
        height: 0.95rem;
        background: url("./products/img/User/User_Login_PhoneImage.png") no-repeat;
        background-size: 100% 0.75rem;
        position: relative;
    }

    .login-1 .username .oint {
        width: 3.15rem;
        height: 0.45rem;
    }

    .login-1 .username .oint input {
        display: block;
        width: 3.15rem;
        height: 0.45rem;
        outline: none;
        background: url("./products/img/User/User_Login_TextBg.png")no-repeat;
        background-size: 3.15rem 0.45rem;
        position: absolute;
        left: 0.8rem;
        top: 0.15rem;
        border: none;
    }

    .login-1 .password {
        width: 100%;
        height: 0.95rem;
        background: url("./products/img/User/User_Register_PassImage.png") no-repeat;
        background-size: 100% 0.75rem;
        position: relative;
    }

    .login-1 .password .oint2 {
        width: 3.15rem;
        height: 0.45rem;
    }

    .login-1 .password .oint2 input {
        display: block;
        width: 3.15rem;
        height: 0.45rem;
        outline: none;
        background: url("./products/img/User/User_Login_TextBg.png") no-repeat;
        background-size: 3.15rem 0.45rem;
        position: absolute;
        left: 0.8rem;
        top: 0.15rem;
        border: none;
    }

    .login-1 .login_register {
        width: 100%;
        height: 0.5rem;
        margin-top: 0.25rem;
        display: flex;
        justify-content: space-between;
    }

    .login-1 .login_register .confire_login {
        display: block;
        width: 1.75rem;
        height: 0.5rem;
        background: url("./products/img/User/User_Login_Sure.png") no-repeat;
        background-size: 1.75rem 0.5rem;
        border: none;
        outline: none;
    }

    .login-1 .login_register .register {
        display: block;
        width: 1.75rem;
        height: 0.5rem;
        background: url("./products/img/User/User_Login_Register.png") no-repeat;
        background-size: 1.75rem 0.5rem;
        border: none;
        outline: none;
    }

    .login-1 .Forgot_password {
        width: 100%;
        margin-top: 0.4rem;
        display: flex;
        justify-content: center;
    }

    .login-1 .Forgot_password a {
        font-size: 0.24rem;
        color: #7b4a63;
    }

    .Other_login_modes {
        width: 4.65rem;
        height: 0.6rem;
        margin: 0 auto;
        text-align: center;
        line-height: 0.6rem;
        display: flex;
        justify-content: space-between;
        font-size: 0.3rem;
        color: #af6d48;
    }

    .Other_login_modes i {
        display: block;
        width: 1.35rem;
        height: 0.05rem;
        background: white;
        margin-top: 0.3rem;
    }

    .WeChat_login {
        width: 100%;
        height: 1.1rem;
    }

    .WeChat_login a {
        display: block;
        width: 2.3rem;
        height: 0.7rem;
        background: url("./products/img/User/User_Login_WeChat.png") no-repeat;
        background-size: 2.3rem 0.7rem;
        margin: 0 auto;
    }

    .Agree_user_privacy {
        width: 100%;
        text-align: center;
        position: relative;
    }

    .Agree_user_privacy span {
        display: block;
        width: 0.2rem;
        height: 0.2rem;
        background: url("./products/img/User/User_Login_Seleted.png") no-repeat;
        background-size: 0.2rem 0.2rem;
        position: absolute;
        left: 1rem;
        top: 0.05rem;
    }
    .Agree_user_privacy a {
        color: #935876;
        font-size: 0.2rem;
    }
</style>

</html>