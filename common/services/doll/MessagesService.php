<?php
namespace common\services\doll;

use Yii;

class MessagesService extends \common\services\BaseService{
    //发送微信模板消息
    public function sendMessage($access_token,$message_url,$data,$touser){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;
        $template_msg=array('touser'=>$touser,'template_id'=>'qh1jlbmnW-CeSQV6Fi5LvB1CoPFC0s4odcOauP5fcvI','url'=>$message_url,'topcolor'=>'#FF0000','data'=>$data);

        $curl = curl_init($url);
        $header = array();
        $header[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        // 不输出header头信息
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // 伪装浏览器
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
        // 保存到字符串而不是输出
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($curl, CURLOPT_POST, 1);
        // 请求数据
        curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($template_msg));
        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }

    //获取access_token
    public function getAccessToken(){
        $appid = "wx42ac1f22ae0225f3";
        $appsecret = "6382ef530107642121fa2743b110aaa4";
        $request_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret.'';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        $token = $result['access_token'];
        return $token;
    }

    //SMTP发送邮件
    public function actionSendMail()
    {
        $smtpserver = "smtp.mxhichina.com";//SMTP服务器
        $smtpserverport =25;//SMTP服务器端口
        $smtpusermail = "notify@wanyiguo.com";//SMTP服务器的用户邮箱
//        $smtpemailto = "yuxiuhong@wanyiguo.com";//发送给谁
        $smtpemailto = $_POST['toemail'];
        $smtpuser = "notify@wanyiguo.com";//SMTP服务器的用户帐号，注：部分邮箱只需@前面的用户名
        $smtppass = "Zww123456!";//SMTP服务器的用户密码
//        $mailtitle = "机器概率异常报警";//邮件主题
        $mailtitle = $_POST['title'];//邮件主题
//        $mailcontent = "请检查机器";//邮件内容
        $mailcontent = "<h1>".$_POST['content']."</h1>";//邮件内容
        $mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
        //************************ 配置信息 ****************************
        $smtp = new \Smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
        $smtp->debug = true;//是否显示发送的调试信息
        $state = $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);

        echo "<div style='width:300px; margin:36px auto;'>";
        if($state==""){
            echo "对不起，邮件发送失败！请检查邮箱填写是否有误。";
            exit();
        }
        echo "恭喜！邮件发送成功！！";
        echo "</div>";
    }

    private function response($text)
    {
        return json_decode($text, true);
    }
}