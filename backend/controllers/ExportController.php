<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\models\PublishAssetForm;

class ExportController extends Controller
{
    public $enableCsrfValidation = false;
    public $layout = false;

//    展示出所有订单数据
    public function actionShow()
    {
        $page = isset($_GET['page'])?$_GET['page']:1;
        $size=10;
        $data = Yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
    select e.allDoll from (
        select order_id,group_concat((c.dollNames)) allDoll from (
        select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id ")->queryAll();
        $count = count($data);
//        print_r($count);die;
        $total_page=ceil($count/$size);
//        echo $total_page;die;
        $last_page=$page-1<1?1:$page-1;

        $next_page=$page+1>$total_page?$total_page:$page+1;

        $offset=($page-1)*$size;
//        echo $offset;die;


        $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id limit $offset,$size ";
                $datas = Yii::$app->db->createCommand($sql)->queryAll();
//                print_r($datas);die;
                 return $this->render('orderShow', ['data' => $datas,'page'=>$page,'last_page'=>$last_page,'next_page'=>$next_page,'total_page'=>$total_page]);
    }


//    导出订单
    public function actionExport()
    {
//        echo 1111;die;
        $startTime = $_REQUEST['startTime'];
        $endTime = $_REQUEST['endTime'];
//        echo $dateTime;die;
        if($startTime=='' || $endTime==''){
            //        查询表中的数据
            $list = yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id")->queryAll();
            //        获取时间
            $filename = time("Y-m-d") . "t_doll_order";
            header("Content-type:applicationndnd/vnd.ms-excel");
            //        数据格式
            header("Content-Disposition:filename=" . $filename . ".xls");
//        导出的数据
            $strexport = "订单号\t发件人姓名\t发件人手机\t发件人详细地址\t自定义区域1*（商品编码、娃娃名称、数量）\t收件人姓名\t收件人手机\t收件人详细地址\t自定义区域\t日期\r";
            //        循环出来
            foreach ($list as $row) {
                $strexport .= $row['order_number'] . "\t";
                $strexport .= $row['addrPerson'] . "\t";
                $strexport .= $row['phone'] . "\t";
                $strexport .= $row['faddress'] . "\t";
                $strexport .= $row['dollinfos'] . "\t";
                $strexport .= $row['receiver_name'] . "\t";
                $strexport .= $row['receiver_phone'] . "\t";
                $strexport .= $row['taddress'] . "\t";
                $strexport .= '' . "\t";
                $strexport .= $row['order_date'] . "\r";
            }
            //        转码
            $strexport = iconv('UTF-8', "GB2312//IGNORE", $strexport);
            $this->redirect(["export/show"]);
            exit($strexport);
            //        渲染页面
        }else{
            //        查询表中的数据
            $list = yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id    where order_date between '$startTime' and '$endTime'")->queryAll();
            //        获取时间
            $filename = time("Y-m-d") . "t_doll_order";
            header("Content-type:applicationndnd/vnd.ms-excel");
            //        数据格式
            header("Content-Disposition:filename=" . $filename . ".xls");
//        导出的数据
            $strexport = "订单号\t发件人姓名\t发件人手机\t发件人详细地址\t自定义区域1*（商品编码、娃娃名称、数量）\t收件人姓名\t收件人手机\t收件人详细地址\t自定义区域\t日期\r";
            //        循环出来
            foreach ($list as $row) {
                $strexport .= $row['order_number'] . "\t";
                $strexport .= $row['addrPerson'] . "\t";
                $strexport .= $row['phone'] . "\t";
                $strexport .= $row['faddress'] . "\t";
                $strexport .= $row['dollinfos'] . "\t";
                $strexport .= $row['receiver_name'] . "\t";
                $strexport .= $row['receiver_phone'] . "\t";
                $strexport .= $row['taddress'] . "\t";
                $strexport .= '' . "\t";
                $strexport .= $row['order_date'] . "\r";
            }
            //        转码
            $strexport = iconv('UTF-8', "GB2312//IGNORE", $strexport);
            $this->redirect(["export/show"]);
            exit($strexport);
            //        渲染页面
        }

  }

//  搜索
    public function actionSearch(){
        $startTime = $_REQUEST['startTime'];
        $endTime = $_REQUEST['endTime'];
        $name = $_REQUEST['name'];
//          echo $name;die;
        if($startTime=='' || $endTime==''){
////              echo '<script>alert("值不能为空");location.href="?r=export/show"</script>';
//              echo 1;
            $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id  where receiver_phone='$name' or receiver_name like '%$name%' or order_number='$name'";
            $data = Yii::$app->db->createCommand($sql)->queryAll();
            return json_encode($data);
        }else{
            $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id  where  order_date between '$startTime' and '$endTime'";
            $data = Yii::$app->db->createCommand($sql)->queryAll();
//          print_r($data);die;
            return json_encode($data);
        }
    }

//  展示已发货的列表

      public function actionDelivery(){

          $page = isset($_GET['page'])?$_GET['page']:1;
          $size=10;
          $data = Yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
        select e.allDoll from (
        select order_id,group_concat((c.dollNames)) allDoll from (
        select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id where status='已发货'")->queryAll();
          $count = count($data);
//        print_r($count);die;
          $total_page=ceil($count/$size);
//        echo $total_page;die;
          $last_page=$page-1<1?1:$page-1;

          $next_page=$page+1>$total_page?$total_page:$page+1;

          $offset=($page-1)*$size;
//        echo $offset;die;


          $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id where status='已发货' limit $offset,$size";
          $datas = Yii::$app->db->createCommand($sql)->queryAll();
//                print_r($datas);die;
          return $this->render('deliveryShow', ['data' => $datas,'page'=>$page,'last_page'=>$last_page,'next_page'=>$next_page,'total_page'=>$total_page]);
      }


//      导出已发货的订单
        public function actionExportDelivery(){

            $startTime = $_REQUEST['startTime'];
            $endTime = $_REQUEST['endTime'];
//        echo $dateTime;die;
            if($startTime=='' || $endTime==''){
                //        查询表中的数据
                $list = yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id where status='已发货'")->queryAll();
                //        获取时间
                $filename = time("Y-m-d") . "t_doll_order";
                header("Content-type:applicationndnd/vnd.ms-excel");
                //        数据格式
                header("Content-Disposition:filename=" . $filename . ".xls");
//        导出的数据
                $strexport = "订单号\t发件人姓名\t发件人手机\t发件人详细地址\t自定义区域1*（商品编码、娃娃名称、数量）\t收件人姓名\t收件人手机\t收件人详细地址\t自定义区域\t日期\r";
                //        循环出来
                foreach ($list as $row) {
                    $strexport .= $row['order_number'] . "\t";
                    $strexport .= $row['addrPerson'] . "\t";
                    $strexport .= $row['phone'] . "\t";
                    $strexport .= $row['faddress'] . "\t";
                    $strexport .= $row['dollinfos'] . "\t";
                    $strexport .= $row['receiver_name'] . "\t";
                    $strexport .= $row['receiver_phone'] . "\t";
                    $strexport .= $row['taddress'] . "\t";
                    $strexport .= '' . "\t";
                    $strexport .= $row['order_date'] . "\r";
                }
                //        转码
                $strexport = iconv('UTF-8', "GB2312//IGNORE", $strexport);
                $this->redirect(["export/show"]);
                exit($strexport);
                //        渲染页面
            }else{
                //        查询表中的数据
                $list = yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id    where status='已发货'and order_date between '$startTime' and '$endTime'")->queryAll();
                //        获取时间
                $filename = time("Y-m-d") . "t_doll_order";
                header("Content-type:applicationndnd/vnd.ms-excel");
                //        数据格式
                header("Content-Disposition:filename=" . $filename . ".xls");
//        导出的数据
                $strexport = "订单号\t发件人姓名\t发件人手机\t发件人详细地址\t自定义区域1*（商品编码、娃娃名称、数量）\t收件人姓名\t收件人手机\t收件人详细地址\t自定义区域\t日期\r";
                //        循环出来
                foreach ($list as $row) {
                    $strexport .= $row['order_number'] . "\t";
                    $strexport .= $row['addrPerson'] . "\t";
                    $strexport .= $row['phone'] . "\t";
                    $strexport .= $row['faddress'] . "\t";
                    $strexport .= $row['dollinfos'] . "\t";
                    $strexport .= $row['receiver_name'] . "\t";
                    $strexport .= $row['receiver_phone'] . "\t";
                    $strexport .= $row['taddress'] . "\t";
                    $strexport .= '' . "\t";
                    $strexport .= $row['order_date'] . "\r";
                }
                //        转码
                $strexport = iconv('UTF-8', "GB2312//IGNORE", $strexport);
                $this->redirect(["export/show"]);
                exit($strexport);
                //        渲染页面
            }
        }

//  搜索以发货
    public function actionSearch1(){
        $startTime = $_REQUEST['startTime'];
        $endTime = $_REQUEST['endTime'];
        $name = $_REQUEST['name'];
//          echo $name;die;
        if($startTime=='' || $endTime==''){
////              echo '<script>alert("值不能为空");location.href="?r=export/show"</script>';
//              echo 1;
            $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id  where status='已发货' and receiver_phone='$name' or receiver_name like '%$name%' or order_number='$name'";
            $data = Yii::$app->db->createCommand($sql)->queryAll();
            return json_encode($data);
        }else{
            $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id  where status='已发货' and order_date between '$startTime' and '$endTime'";
            $data = Yii::$app->db->createCommand($sql)->queryAll();
//          print_r($data);die;
            return json_encode($data);
        }
    }




//    未发货的列表
    public function actionUnshipped(){

        $page = isset($_GET['page'])?$_GET['page']:1;
        $size=10;
        $data = Yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
    select e.allDoll from (
        select order_id,group_concat((c.dollNames)) allDoll from (
        select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id where status='已兑换' or status='寄存中' or status='申请发货'")->queryAll();
        $count = count($data);
//        print_r($count);die;
        $total_page=ceil($count/$size);
//        echo $total_page;die;
        $last_page=$page-1<1?1:$page-1;

        $next_page=$page+1>$total_page?$total_page:$page+1;

        $offset=($page-1)*$size;
//        echo $offset;die;


        $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id where status='已兑换' or status='寄存中' or status='申请发货' limit $offset,$size";
        $datas = Yii::$app->db->createCommand($sql)->queryAll();
//                print_r($datas);die;
        return $this->render('unshippedShow', ['data' => $datas,'page'=>$page,'last_page'=>$last_page,'next_page'=>$next_page,'total_page'=>$total_page]);
    }


//    导出未发货的订单
       public function actionExportUnshipped(){
           $startTime = $_REQUEST['startTime'];
           $endTime = $_REQUEST['endTime'];
           $name = $_REQUEST['name'];
//        echo $dateTime;die;
           if($startTime=='' || $endTime==''){
               //        查询表中的数据
               $list = yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id where status='已兑换' or status='寄存中' or status='申请发货'")->queryAll();
               //        获取时间
               $filename = time("Y-m-d") . "t_doll_order";
               header("Content-type:applicationndnd/vnd.ms-excel");
               //        数据格式
               header("Content-Disposition:filename=" . $filename . ".xls");
//        导出的数据
               $strexport = "订单号\t发件人姓名\t发件人手机\t发件人详细地址\t自定义区域1*（商品编码、娃娃名称、数量）\t收件人姓名\t收件人手机\t收件人详细地址\t自定义区域\t日期\r";
               //        循环出来
               foreach ($list as $row) {
                   $strexport .= $row['order_number'] . "\t";
                   $strexport .= $row['addrPerson'] . "\t";
                   $strexport .= $row['phone'] . "\t";
                   $strexport .= $row['faddress'] . "\t";
                   $strexport .= $row['dollinfos'] . "\t";
                   $strexport .= $row['receiver_name'] . "\t";
                   $strexport .= $row['receiver_phone'] . "\t";
                   $strexport .= $row['taddress'] . "\t";
                   $strexport .= '' . "\t";
                   $strexport .= $row['order_date'] . "\r";
               }
               //        转码
               $strexport = iconv('UTF-8', "GB2312//IGNORE", $strexport);
               $this->redirect(["export/show"]);
               exit($strexport);
               //        渲染页面
           }else{
               //        查询表中的数据
               $list = yii::$app->db->createCommand("select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id   where  (status='已兑换' or status='寄存中' or status='申请发货') AND order_date between '$startTime' and '$endTime'")->queryAll();
               //        获取时间
               $filename = time("Y-m-d") . "t_doll_order";
               header("Content-type:applicationndnd/vnd.ms-excel");
               //        数据格式
               header("Content-Disposition:filename=" . $filename . ".xls");
//        导出的数据
               $strexport = "订单号\t发件人姓名\t发件人手机\t发件人详细地址\t自定义区域1*（商品编码、娃娃名称、数量）\t收件人姓名\t收件人手机\t收件人详细地址\t自定义区域\t日期\r";
               //        循环出来
               foreach ($list as $row) {
                   $strexport .= $row['order_number'] . "\t";
                   $strexport .= $row['addrPerson'] . "\t";
                   $strexport .= $row['phone'] . "\t";
                   $strexport .= $row['faddress'] . "\t";
                   $strexport .= $row['dollinfos'] . "\t";
                   $strexport .= $row['receiver_name'] . "\t";
                   $strexport .= $row['receiver_phone'] . "\t";
                   $strexport .= $row['taddress'] . "\t";
                   $strexport .= '' . "\t";
                   $strexport .= $row['order_date'] . "\r";
               }
               //        转码
               $strexport = iconv('UTF-8', "GB2312//IGNORE", $strexport);
               $this->redirect(["export/show"]);
               exit($strexport);
               //        渲染页面
           }
       }
//     搜索未发货
      public function actionSearch2(){
          $startTime = $_REQUEST['startTime'];
          $endTime = $_REQUEST['endTime'];
          $name = $_REQUEST['name'];
//          echo $name;die;
          if($startTime=='' || $endTime==''){
////              echo '<script>alert("值不能为空");location.href="?r=export/show"</script>';
//              echo 1;
              $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id  where (status='已兑换' or status='寄存中' or status='申请发货') and receiver_phone='$name' or receiver_name like '%$name%' or order_number='$name'";
              $data = Yii::$app->db->createCommand($sql)->queryAll();
              return json_encode($data);
          }else{
              $sql = "select d.id,order_number,'365抓娃娃' addrPerson,'17601323004' phone,'上海市金山区365抓娃娃' faddress,(
                select e.allDoll from (
                select order_id,group_concat((c.dollNames)) allDoll from (
                select order_id,CONCAT((select dollName from doll_info b where b.dollCode = a.doll_code),'*',a.quantity,';') dollNames from t_doll_order_item a
                ) c GROUP BY c.order_id) e where e.order_id = d.id ) as dollinfos,g.receiver_name,g.receiver_phone,CONCAT(g.province,g.city,g.county,g.street) taddress,d             .`status`,d.order_date
                from t_doll_order d left join (select * from t_member_addr r where r.default_flg=1) g on d.order_by=g.member_id  where (status='已兑换' or status='寄存中' or status='申请发货') and order_date between '$startTime' and '$endTime'";
              $data = Yii::$app->db->createCommand($sql)->queryAll();
//          print_r($data);die;
              return json_encode($data);
          }
      }
}
//(addDate = @addDate or @addDate is null) and (name = @name or @name = '')