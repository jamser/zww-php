<?php
namespace frontend\controllers;

use backend\modules\doll\models\NoOrderNumber;
use backend\modules\erp\models\DollInfo;
use common\helpers\MyFunction;
use common\models\Account;
use common\models\Member;
use common\models\MemberAdd;
use common\models\MemberWx;
use common\models\WechatAdd;
use Yii;
use yii\web\Controller;
use Illuminate\Support\Facades\Input;
use yii\web\UploadedFile;

class TestController extends Controller
{
    public $enableCsrfValidation=false;
    public $layout = '/mobile';
    public function actionIndex()
    {
        return $this->render('data');
    }
//    投币
    public function actionCoin(){
        $request_url = 'http://dev.365zhuawawa.com?r=ali/pub-coin';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        print_r($result);
    }
//    向上
    public function actionForward(){
        $request_url = 'http://dev.365zhuawawa.com?r=ali/pub-forward';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        print_r($result);
    }
//    向下
    public function actionBackward(){
        $request_url = 'http://dev.365zhuawawa.com?r=ali/pub-backward';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        print_r($result);
    }
//    向左
    public function actionLeft(){
        $request_url = 'http://dev.365zhuawawa.com?r=ali/pub-left';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        print_r($result);
    }
//    向右
    public function actionRight(){
        $request_url = 'http://dev.365zhuawawa.com?r=ali/pub-right';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        print_r($result);
    }
//    停止
    public function actionStop(){
        $request_url = 'http://dev.365zhuawawa.com?r=ali/pub-stop';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        print_r($result);
    }
//    抓
    public function actionClaw(){
        $request_url = 'http://dev.365zhuawawa.com?r=ali/pub-claw';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        print_r($result);
    }
//    查询
    public function actionQuery(){
        $request_url = 'http://dev.365zhuawawa.com?r=ali/pub-query';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        print_r($result);
    }
//  添加接口
    public function actionInsert(){
        $data = $_REQUEST['msg'];
//        print_r($data);die;
//        json格式转换为数组
        $json = json_decode($data,true);
//        print_r($json);die;
//        接值
        $MessageId =$json['MessageId'];
        $RequestId =$json['RequestId'];
        $Success =$json['Success'];
//echo $RequestId;die;
        $sql = Yii::$app->db->createCommand("insert into db_from_boy (id,MessageId,RequestId,Success) values (null,'$MessageId','$RequestId','$Success')")->execute();
        if($sql){
            echo 1;
        }else{
            echo 0;
        }
    }
//    查询接口
       public function actionSelect(){
           $sql = Yii::$app->db->createCommand("select * from db_from_boy order by id desc limit 0,20")->queryAll();
//           print_r($sql);die;
           $data = json_encode($sql);
           return $data;
       }
//    base64解码
       public function actionDecode(){
           $request_url = 'http://192.168.2.83/login/frontend/web/index.php?r=mns/receive';
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $request_url);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           $result = curl_exec($ch);
           curl_close($ch);
//           print_r($result);
           $data = json_decode($result,true);
//           print_r($data);
           $res = base64_decode($data['payload']);
       }
//    查数据库的值
       public function actionDecode_select(){
           $sql = "select `id`,`data` from t_mns_data order by id desc limit 0,20";
           $datas = Yii::$app->db->createCommand($sql)->queryAll();
//           $array =array();
           $result = [];
           foreach($datas as $data){
               $data = json_decode($data['data'], true);
               $data['payload'] = base64_decode($data['payload']);
               $data['timestamp'] = date('m-d H:i:s');
               $str= '';
               foreach($data as $key=>$val) {
                   $str .= $key.':'.$val.' ';
               }
               $result[] = $str;
           }
//           print_r($data);die;

//           echo $data['id'].' - '.$data['payload'];die;

//           print_r($dataCode);die;
           //$result = json_encode($data);
           return implode('<br/><br/>', $result);
       }
//    视频的接口
      public function actionVideo(){
          $request_url = 'http://101.132.166.121:3000/';
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $request_url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $result = curl_exec($ch);
          curl_close($ch);
//           print_r($result);
          echo $result;
//          $data = json_decode($result,true);
      }
//       接口
       public function actionControl(){
           $request_url = 'http://192.168.2.111/login/api/web/index.php?r=socket/ali/pub';
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $request_url);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           $result = curl_exec($ch);
           curl_close($ch);
           print_r($result);
       }

       
       public function actionV3Control() {
           //Yii::info("v3-control running");
           $file = fopen(Yii::getAlias('@frontend').'/runtime/v3-control.log','a+');
           fwrite($file, "v3-control running..".date('Y-m-d H:i:s')."\n");
           fclose($file);
           return "hello world";
       }

    public function actionTest(){
        $appid = "wx42ac1f22ae0225f3";
        $appsecret = "6382ef530107642121fa2743b110aaa4";
        $request_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret.'';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
//        $result = $this->response($result);
//        $data = array('access_token'=>$result['access_token']);
//        $data = json_encode($data);
        print_r($result);
    }

//    public function actionDe(){
//       WechatAdd::deleteAll();
//    }
//
//    public function actionUp(){
//        $update = "update wechat_add set add_flg = 1 WHERE id<17677";
//        Yii::$app->db->createCommand($update)->execute();
//    }

    //获得公众号关注人信息
    public function actionData(){
        $access_token = $_GET['access_token'];
        $num = MemberAdd::find()->count();
        $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=$access_token&next_openid=";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $data = $this->response($result);
        $opData=$data['data']['openid'];
        for($i=0;$i<count($opData);$i++){
            $openid = $opData[$i];
            $userInfo = MemberAdd::find()->where(['openid'=>$openid])->asArray()->one();
            if(empty($userInfo)){
                $myfunction = new MyFunction();
                $myfunction->addOp($opData[$i]);
            }else{
                continue;
            }
        }
    }

    //通过openid获得用户信息
    public function actionUn($access_token){
        $openids = MemberAdd::find()->where(['unionid'=> null])->all();
        foreach($openids as $k=>$v){
            $openid = $v['openid'];
            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            $data = $this->response($result);
            if(isset($data['unionid'])){
                $unionid = $data['unionid'];
            }else{
                continue;
            }
            $myfunction = new MyFunction();
            $myfunction->updateUn($openid,$unionid);
        }
    }

    //关注公众号加币
    public function actionAdd(){
        $db = Yii::$app->db;
        $trans = $db->beginTransaction();
        try{
            $unionid = MemberAdd::find()->where(['add_flg'=>0])->asArray()->all();
            foreach($unionid as $k=>$v){
                $unionid = $v['unionid'];
                if(empty($unionid)){
                    continue;
                }else{
                    $userInfo = MemberWx::find()->where(['union_id'=>$unionid])->asArray()->one();
                    $user_id = $userInfo['user_id'];
                    if($user_id){
                        echo 2;
//                $memberInfo = Member::find()->where(['id'=>$user_id])->asArray()->one();
                        $memberInfo = Account::find()->where(['id'=>$user_id])->asArray()->one();
                        $coins = $memberInfo['coins'] + 20;
                        $member_id = $memberInfo['id'];
                        $myfunction = new MyFunction();
                        $myfunction->addCoins($unionid,$user_id,$coins,$member_id);
                    }else{
                        echo 1;
                        continue;
                    }
                }
            } $trans->commit();
        } catch (\Exception $ex) {
            $trans->rollback();
            throw $ex;
        }
    }

    public function actionAddNew(){
        $unionidData = MemberAdd::find()->where(['add_flg'=>0])->asArray()->all();
        $unionids = '';
        foreach($unionidData as $k=>$v){
            $union_id = $v['unionid'];
            $unionids .="'".$union_id."'".',';
        }
        $unionids = rtrim($unionids,',');
        $sql1 = "select user_id,union_id from member_wx WHERE union_id in ($unionids)";
        $idData = Yii::$app->db->createCommand($sql1)->queryAll();
        $user_ids = '';
        $union_ids = [];
        foreach($idData as $k=>$v){
            $user_id = $v['user_id'];
            $un_id = $v['union_id'];
            $user_ids .= $user_id.',';
            $union_ids[$user_id]=$un_id;
        }
        $user_ids = rtrim($user_ids,',');
        $account_sql = "select * from account WHERE id in ($user_ids)";
        $accountData = Yii::$app->db->createCommand($account_sql)->queryAll();

        foreach($accountData as $k=>$v){
            $id = $v['id'];
            $coins = $v['coins']+20;
            $uni_id = $union_ids[$id];
            $myfunction = new MyFunction();
            $myfunction->addCoins($uni_id,$id,$coins,$id);
            echo 1;
        }
    }

//    public function actionDd(){
//        $unionid = MemberAdd::find()->where(['add_flg'=>1])->asArray()->all();
//        foreach($unionid as $k=>$v){
//            $unionid = $v['unionid'];
//            $userInfo = MemberWx::find()->where(['union_id'=>$unionid])->asArray()->one();
//            $user_id = $userInfo['user_id'];
//            if($user_id){
//                echo 2;
//                $memberInfo = Member::find()->where(['id'=>$user_id])->asArray()->one();
//                $member_id = $memberInfo['id'];
//                $de_sql ="delete from t_member_charge_history WHERE member_id ='$member_id' AND charge_method = '关注公众号奖励'";
//                Yii::$app->db->createCommand($de_sql)->execute();
//            }else{
//                echo 1;
//                continue;
//            }
//        }
//    }

//    public function actionAddUp(){
//        $unionid = MemberAdd::find()->where(['add_flg'=>1])->asArray()->all();
//        foreach($unionid as $k=>$v){
//            $unionid = $v['unionid'];
//            $userInfo = MemberWx::find()->where(['union_id'=>$unionid])->asArray()->one();
//            $user_id = $userInfo['user_id'];
//            if($user_id){
//                echo 2;
//                $memberInfo = Member::find()->where(['id'=>$user_id])->asArray()->one();
//                $coins = $memberInfo['coins'] + 20;
//                $member_id = $memberInfo['id'];
//                $myfunction = new MyFunction();
//                $myfunction->addUp($coins,$member_id);
//            }else{
//                echo 1;
//                continue;
//            }
//        }
//    }

    private function response($text)
    {
        return json_decode($text, true);
    }

//    public function actionMp(){
//        return $this->render('test');
//    }
//
//    public function actionUpdate(){
//        $sql = "update member_add set add_flg =0 WHERE unionid ='onRio0YGgMF5cc_-r6ruPl4fSZz8'";
//        Yii::$app->db->createCommand($sql)->execute();
//    }

//    public function actionUp(){
//        $sql = "SELECT * FROM t_member_charge_history WHERE charge_date LIKE '2018-01-15%' AND charge_method='关注公众号奖励'";
//        $data = Yii::$app->db->createCommand($sql)->queryAll();
//        foreach($data as $k=>$v){
//            $id = $v['member_id'];
//            $unSql = "select * from member_wx WHERE user_id = $id";
//            $unData = Yii::$app->db->createCommand($unSql)->queryAll();
//            $open_id = $unData[0]['open_id'];
//            $upSql = "update member_add set add_flg = 0 WHERE openid='$open_id'";
//            Yii::$app->db->createCommand($upSql)->execute();
//            echo 1;
//        }
//    }

//    public function actionDe(){
//        $sql = "delete from doll_order_goods WHERE member_id=164";
//        Yii::$app->db->createCommand($sql)->execute();
//    }

    public function actionChart(){
        $day = array();
        $first_price = array();
        $h_price = array();
        $z_price = array();
        $m_price = array();
        for($i=0;$i>-3;$i--){
            $time = strtotime("$i day");
            $times = date("Y-m-d",$time);
            $sql = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='首充包'";
            $price_f = Yii::$app->db_php->createCommand($sql)->queryAll();
            $sql_h = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='寒假包'";
            $price_h = Yii::$app->db_php->createCommand($sql_h)->queryAll();
            $sql_z = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='钻石！'";
            $price_z = Yii::$app->db_php->createCommand($sql_z)->queryAll();
            $sql_m = "SELECT price FROM charge_info WHERE day = '$times' AND charge_name='超多钻石！'";
            $price_m = Yii::$app->db_php->createCommand($sql_m)->queryAll();
            array_unshift($day,$times);
            array_unshift($first_price,$price_f[0]['price']);
            array_unshift($h_price,$price_h[0]['price']);
            array_unshift($z_price,$price_z[0]['price']);
            array_unshift($m_price,$price_m[0]['price']);
        }

        return $this->render('chart',[
            'day'=>$day,
            'first_price'=>$first_price,
            'h_price'=>$h_price,
            'z_price'=>$z_price,
            'm_price'=>$m_price,
        ]);
    }

    //模板消息
    public function actionMessage(){
        $access_token = '6_u3fCYit8EiEaS4-fblvPzY6OFECmyr4TW_RKQI5XErkybQ2vmWPlmE2ai8YNnh4Adib_nBWTrSO2O-AIn5Luh0OQOJR1pX6x5iE8fTWnd1PmJXRS8M_OE4NzJP4LRXjAHAZFL';
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;//access_token改成你的有效值
        $time = date('Y-m-d H:i:s',time());

        $data = array(
            'first' => array(
                'value' => '机器概率异常报警',
                'color' => '#FF0000'
            ),
            'keyword1' => array(
                'value' => $time,
                'color' => '#FF0000'
            ),
            'keyword2' => array(
                'value' => '机器概率',
                'color' => '#FF0000'
            ),
            'remark' => array(
                'value' => '请尽快查看邮件检查机器',
                'color' => '#FF0000'
            )
        );
        $template_msg=array('touser'=>'opTs00zQv8_uGqCq4HR-g8ob9Drw','template_id'=>'qh1jlbmnW-CeSQV6Fi5LvB1CoPFC0s4odcOauP5fcvI','url'=>'http://p-admin.365zhuawawa.com/doll/alarm/alarm-data','topcolor'=>'#FF0000','data'=>$data);

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

    //通过openid获得用户信息
    public function actionXxx($access_token){
        $openids = MemberAdd::find()->where(['unionid'=> null])->all();
        foreach($openids as $k=>$v){
            $openid = $v['openid'];
            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            $data = $this->response($result);
            if(isset($data['unionid'])){
                $unionid = $data['unionid'];
            }else{
                continue;
            }
            $myfunction = new MyFunction();
            $myfunction->updateUn($openid,$unionid);
        }
    }

    //通过表上传数据
    public function actionTestAdd(){
        $model = new DollInfo();
        if ($model->load(Yii::$app->request->post())) {
            $model->dollCode = UploadedFile::getInstance($model, 'dollCode');
            $excelInfo = $this->object2array($model->dollCode);
            $excel_url = $excelInfo['tempName'];
            //文件名为文件路径和文件名的拼接字符串
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');//创建读取实例
            /*
             * log()//方法参数
             * $file_name excal文件的保存路径
             */
            $objPHPExcel = $objReader->load($excel_url);//加载文件
            $sheet = $objPHPExcel->getSheet(0);//取得sheet(0)表
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            for($i=2;$i<=$highestRow;$i++)
            {
                $doll_code = $sheet->getCell("B".$i)->getValue();
                $dollCoins = $sheet->getCell("D".$i)->getValue();
                $deliverCoins= $sheet->getCell("C".$i)->getValue();
                print_r($doll_code);die;
                $sql = "update doll_info set dollCoins='$dollCoins',deliverCoins='$deliverCoins' WHERE dollCode='$doll_code'";
                print_r($sql);die;
                Yii::$app->db->createCommand($sql)->execute();
            }
        } else {
            return $this->render('add',[
                'model' => $model,
            ]);
        }
    }

    function object2array($object) {
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        }
        else {
            $array = $object;
        }
        return $array;
    }
}