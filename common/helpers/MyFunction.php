<?php
namespace common\helpers;

use backend\modules\erp\models\DollOrderGoods;
use common\models\MemberAddr;
use Yii;
use common\models\Member;
use common\models\WechatUnionid;
use common\models\MemberToken;
use backend\modules\erp\models\DollInfo;
use backend\modules\erp\models\DollOrder;
use backend\modules\erp\models\DollOrderItem;

class MyFunction{
    function addUser($userInfo,$token){
        $redis = Yii::$app->redis;
        $unionid = $userInfo['unionid'];
        $openid = $userInfo['openid'];
        $user_check = WechatUnionid::find()->where(['union_id'=>$unionid])->one();
        $userid = $user_check['id'];
        $redis->set("$token","$userid");
        if($userInfo['sex'] == 1){
            $sex = 'm';
        }elseif($userInfo['sex'] == 2){
            $sex = 'f';
        }else{
            $sex = 'n';
        }
        $date = date("Y-m-d H:i:s",time());
        if($user_check){
            $update_sql = "update t_member set last_login_date = '$date' WHERE id=$userid";
            Yii::$app->db->createCommand($update_sql)->execute();
            $token_sql = "update t_member_token set token = '$token' WHERE member_id=$userid";
            Yii::$app->db->createCommand($token_sql)->execute();
            $union_sql = "update wechat_unionid set updated_at='$date' WHERE id=$userid";
            Yii::$app->db_php->createCommand($union_sql)->execute();
        }else{
            $inviteCode = rand(10000000, 99999999);
            $name = $userInfo['nickname'];
            $icon = $userInfo['headimgurl'];
            $member_sql = "insert into t_member(memberID,name,gender,icon_real_path,register_date,last_login_date) VALUES ('$inviteCode','$name'
                           ,'$sex','$icon','$date','$date')";
            Yii::$app->db->createCommand($member_sql)->execute();
            $memberInfo = Member::find()->where(['memberId'=>$inviteCode])->asArray()->one();
            $id = $memberInfo['id'];
            $appid = Yii::$app->params['appid'];
            $union_sql = "insert into wechat_unionid(id,open_id,union_id,app_id,created_at,updated_at) VALUES ('$id','$openid','$unionid','$appid','$date','$date')";
            Yii::$app->db_php->createCommand($union_sql)->execute();
            $token_sql = "insert into t_member_token(token,member_id) VALUES ('$token','$id')";
            Yii::$app->db->createCommand($token_sql)->execute();
        }
    }

    public function addUnionid($date,$id=1,$openid,$unionid){
        $union = new WechatUnionid();
        $union->id = $id;
        $union->open_id = $openid;
        $union->union_id = $unionid;
        $appid = Yii::$app->params['appid'];
        $union->app_id = $appid;
        $union->created_at = $date;
        $union->updated_at = $date;
        $union->save();
    }

    public function addToken($id,$token){
        $model = new MemberToken();
        $model->token = $token;
        $model->member_id = $id;
        $model->save();
    }

    //游戏扣币
    public function reduceCoin($coins,$userId){
        $update_sql = "update t_member set coins =:coins WHERE id=:userId";
        Yii::$app->db->createCommand($update_sql,[
            ':coins'=>$coins,
            ':userId'=>$userId
        ])->execute();
    }

    //订单入库
    public function addOrder($userId,$addressId){
        $order_num = rand(1000000000, 9999999999);
        $date = date("Y-m-d H:i:s",time());
        $order_sql = "insert into t_doll_order(order_number,order_date,order_by,address_id) VALUES (:order_num,:date,:userId,:addressId)";
        Yii::$app->db->createCommand($order_sql,[
            ':order_num'=>$order_num,
            ':date'=>$date,
            ':userId'=>$userId,
            ':addressId'=>$addressId,
        ])->execute();
    }

    //添加地址
    public function addAddress($userId,$addressInfo){
        $model = new MemberAddr();
        $model->member_id = $userId;
        $model->receiver_name = $addressInfo['name'];
        $model->receiver_phone = $addressInfo['phone'];
        $model->province = $addressInfo['province'];
        $model->city = $addressInfo['city'];
        $model->county = $addressInfo['country'];
        $model->street = $addressInfo['street'];
        $model->created_date = $addressInfo['created_date'];
        $model->modified_date = $addressInfo['modified_date'];
        $model->save();
    }

    //修改机器状态
    public function updateStatus($dollId,$status){
        $update_sql = "update t_doll set machine_status =:status WHERE id=:id";
        Yii::$app->db->createCommand($update_sql,[
            ':id'=>$dollId,
            ':status'=>$status,
        ])->execute();
    }

    //保存娃娃信息
    public function addDoll($dollInfo){
        $model = new DollInfo();
        $model->dollName = $dollInfo['dollName'];
        $model->dollTotal = $dollInfo['dollTotal'];
        $model->img_url = $dollInfo['img_url'];
        $model->addTime = $dollInfo['addTime'];
        $model->dollCode = $dollInfo['dollCode'];
        $model->agency = $dollInfo['agency'];
        $model->size = $dollInfo['size'];
        $model->type = $dollInfo['type'];
        $model->note = $dollInfo['note'];
        $model->dollCoins = $dollInfo['dollCoins'];
        $model->deliverCoins = $dollInfo['deliverCoins'];
        $model->save();
    }

    //批量导入物流单号
    public function updateOrder($order_number,$deliver_method,$deliver_number){
        $dollOrder_sql = "update t_doll_order set status = '已发货',deliver_method=:deliver_method,deliver_number=:deliver_number WHERE order_number=:order_number";
        Yii::$app->db->createCommand($dollOrder_sql,[
            ':deliver_method'=>$deliver_method,
            ':deliver_number'=>$deliver_number,
            ':order_number'=>$order_number,
        ])->execute();
        $dollOrder = DollOrder::find()->where(['order_number'=>$order_number])->asArray()->one();
        $order_id = $dollOrder['id'];
        $order_by = $dollOrder['order_by'];
        $itemInfo = DollOrderItem::find()->where(['order_id'=>$order_id])->asArray()->one();
        $item_id = $itemInfo['id'];
        $update_sql = "update doll_order_goods set status = '已发货',deliver_method=:deliver_method,deliver_number=:deliver_number WHERE member_id=:member_id AND (dollitemids like '$item_id%' OR dollitemids like '%,$item_id%')";
        Yii::$app->db->createCommand($update_sql,[
            ':deliver_method'=>$deliver_method,
            ':deliver_number'=>$deliver_number,
            ':member_id'=>$order_by,
        ])->execute();
    }

    public function orderStatus($order_number,$deliver_method,$deliver_number){
        $update_sql = "update doll_order_goods set status = '已发货',deliver_method='$deliver_method',deliver_number='$deliver_number' WHERE order_number='goods_$order_number'";
        Yii::$app->db->createCommand($update_sql)->execute();
        $number = 'goods_'.$order_number;
        $goodsInfo = DollOrderGoods::find()->where(['order_number'=>$number])->asArray()->one();
        $item_id = $goodsInfo['dollitemids'];
        $order_sql = "select * from t_doll_order_item where id IN ('$item_id')";
        $orderInfo = Yii::$app->db->createCommand($order_sql)->queryAll();
        $count = count($orderInfo);
        if($count == 0){
            $orderid = 0;
        }else{
            $orderid = $orderInfo[0]['order_id'];
        }
        $dollOrder_sql = "update t_doll_order set status = '已发货',deliver_method='$deliver_method',deliver_number="."'$deliver_number'"." WHERE id=$orderid";
        Yii::$app->db->createCommand($dollOrder_sql)->execute();
    }

    //合并更新数据库
    public function updateDoll($dollitemids,$dollInfos,$id){
        $update_sql = "update doll_order_goods set dollitemids ="." '$dollitemids',dolls_info ="."'$dollInfos' WHERE id ="." '$id'";
        Yii::$app->db->createCommand($update_sql)->execute();
    }

    public function deleteDoll($id){
        $delete_sql = "delete from doll_order_goods WHERE id =:id";
        Yii::$app->db->createCommand($delete_sql,[
            ':id'=>$id
        ])->execute();
    }

    public function mergeOrder($id,$date,$json) {
        $insert_sql = "insert into doll_order_goods_backup(origin_id,data,created_date) VALUES (:id,:json,:date)";
        Yii::$app->db->createCommand($insert_sql,[
            ':id'=>$id,
            ':json'=>$json,
            ':date'=>$date
        ])->execute();
    }

    public function updateDollInfo($id,$dollName,$dollTotal,$img_url,$addTime,$dollCode,$agency,$size,$type,$note,$dollCoins,$deliverCoins,$redeemCoins){
        $update_sql = "update doll_info set dollName ='$dollName',dollTotal ='$dollTotal',img_url ='$img_url',addTime ='$addTime',dollCode ='$dollCode',agency ='$agency'
        ,size='$size',type='$type',note='$note',dollCoins='$dollCoins',deliverCoins='$deliverCoins',redeemCoins='$redeemCoins' WHERE id ="." '$id'";
        Yii::$app->db->createCommand($update_sql)->execute();
    }

    public function  addOp($openid){
        $sql = "insert into member_add(openid) VALUES ('$openid')";
        Yii::$app->db->createCommand($sql)->execute();
    }

    public function updateUn($openid,$unionid){
        $update_sql = "update member_add set unionid ="." '$unionid' WHERE openid ="." '$openid'";
        Yii::$app->db->createCommand($update_sql)->execute();
    }

    public function addCoins($unionid,$user_id,$coins,$member_id){
        $coins_before = $coins-20;
        $time = date('Y-m-d H:i:s',time());
        $update_sql = "update account set coins =$coins WHERE id =$user_id";
        Yii::$app->db->createCommand($update_sql)->execute();
        $sql = "update member_add set add_flg =1 WHERE unionid ='$unionid'";
        Yii::$app->db->createCommand($sql)->execute();
        $insert_sql = "insert into t_member_charge_history(member_id,prepaid_amt,coins,charge_date,type,charge_method,coins_before,coins_after)
                       VALUES ('$member_id','0','20','$time','income','关注公众号奖励','$coins_before','$coins')";
        Yii::$app->db->createCommand($insert_sql)->execute();
    }

    public function addUp($coins,$member_id){
        $coins_before = $coins-20;
        $time = date('Y-m-d H:i:s',time());
        $insert_sql = "insert into t_member_charge_history(member_id,prepaid_amt,coins,charge_date,type,charge_method,coins_before,coins_after)
                       VALUES ('$member_id','0','20','$time','income','关注公众号奖励','$coins_before','$coins')";
        Yii::$app->db->createCommand($insert_sql)->execute();
    }

    public function addNum($memberId){
        $sql = "insert into invite_num(invite_code,invite_num) VALUES ('$memberId','1')";
        Yii::$app->db->createCommand($sql)->execute();
    }

    public function updateNum($memberId,$num){
        $sql = "update invite_num set invite_num =$num WHERE invite_code =$memberId";
        Yii::$app->db->createCommand($sql)->execute();
    }

    public function updateChannel($user_id,$channel){
        $sql = "update t_member set register_channel ="." '$channel' WHERE id =$user_id";
        Yii::$app->db->createCommand($sql)->execute();
    }

    public function updateCity($city,$code){
        $sql = "update doll_info set agency = '$city' WHERE dollCode ='$code'";
        Yii::$app->db->createCommand($sql)->execute();
    }






















}