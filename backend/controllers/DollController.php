<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\models\PublishAssetForm;

class DollController extends Controller
{
    public function actionIndex(){
        echo 11111;die;
    }
    public $enableCsrfValidation = false;
    public $layout = false;
//    展示出所有订单数据
    public function actionAdd()
    {
        return $this->render('dollAdd');
    }
    public function actionAdd_do(){
//        添加娃娃信息
        $data=Yii::$app->request->post();
//      print_r($data);die;
//        print_r($_FILES);die;
        if($_FILES["song_img"]["error"]) {
            echo $_FILES["song_img"]["error"];
        } else {
            //控制上传文件类型
            if(($_FILES["song_img"]["type"]=="image/jpg" || $_FILES["song_img"]["type"]=="img/png" || $_FILES["song_img"]["type"]=="image/jpeg") && $_FILES["song_img"]["size"]<5000000)
            {
                //找到文件存放的位置
                $filename = "../../common/upload/".$_FILES["song_img"]["name"];  //加 .  拼接
                //转换编码格式
                $filename = iconv("UTF-8","gb2312",$filename);
                //判断文件是否存在
                if(file_exists($filename)) {
                    echo "该文件已存在！";die;
                } else {
                    //保存文件
                    move_uploaded_file($_FILES["song_img"]["tmp_name"],$filename);                   //移动上传文件
                }
            } else
            {
                echo "文件类型不正确";die;
            }
        }
//        print_r($_FILES);die;
        $dollName=$data['dollName'];
        $dollTotal=$data['dollTotal'];
        $song_img=$_FILES['song_img']['name'];
        $dollNumber=$data['dollNumber'];
        $dollCode=$data['dollCode'];
        $time=time();
        $sql="insert into doll_info(dollName,dollTotal,img,dollNumber,addTime,dollCode) values('$dollName','$dollTotal','$song_img','$dollNumber','$time','$dollCode')";
//        echo $sql;die;
        $result=Yii::$app->db->createCommand($sql)->execute();
        if($result){
            return $this->redirect(['doll/show']);
        }else{
            return $this->redirect(['doll/add']);
        }
    }
//    展示娃娃信息
        public function actionShow(){
            $sql = "select id,dollName,dollTotal,img,dollNumber,addTime,dollCode from doll_info";
            $data = Yii::$app->db->createCommand($sql)->queryAll();
//            print_r($data);die;
            return $this->render('dollShow',['data'=>$data]);
        }
//    删除娃娃信息
        public function actionDel(){
            $id=Yii::$app->request->get('id');
            $sql = "delete from doll_info where id = '$id'";
            $data = Yii::$app->db->createCommand($sql)->execute();
            if($data){
                return $this->redirect(['doll/show']);
            }else{
                return $this->redirect(['doll/show']);
            }
        }
//    修改娃娃订单
        public function actionSave(){
            $id=Yii::$app->request->get('id');
//            echo $id;die;
            $sql = "select * from doll_info where id = '$id'";
            $data = Yii::$app->db->createCommand($sql)->queryOne();
//            print_r($data);die;
            return $this->render('dollSave',['data'=>$data]);
        }
//       修改娃娃订单
        public function actionSave_do(){
            $data = Yii::$app->request->post();
//            echo $data['song_img'];die;
            if($_FILES["song_img"]["error"]) {
                echo $_FILES["song_img"]["error"];
            } else {
                //控制上传文件类型
                if(($_FILES["song_img"]["type"]=="image/jpg" || $_FILES["song_img"]["type"]=="img/png" || $_FILES["song_img"]["type"]=="image/jpeg") && $_FILES["song_img"]["size"]<5000000)
                {
                    //找到文件存放的位置
                    $filename = "../../common/upload/".$_FILES["song_img"]["name"];  //加 .  拼接
                    //转换编码格式
                    $filename = iconv("UTF-8","gb2312",$filename);
                    //判断文件是否存在
//                    if(file_exists($filename)) {
//                        echo "该文件已存在！";die;
//                    } else {
//                        //保存文件
//                        move_uploaded_file($_FILES["song_img"]["tmp_name"],$filename);                   //移动上传文件
//                    }
                } else
                {
                    echo "文件类型不正确";die;
                }
            }
            $filename=$_FILES['song_img']['name'];
//            print_r($filename);die;
            $img = $data['song_img'];
//            echo $img;die;
            $stu_id = $data['stu_id'];
            $dollName=$data['dollName'];
            $dollTotal=$data['dollTotal'];
            $dollNumber=$data['dollNumber'];
            $dollCode=$data['dollCode'];
            $time=time();
            if($filename){
                if(!$img){
                    echo "错误信息";
                }else{
                    $sql="update doll_info set img='$filename',dollName='$dollName',dollTotal='$dollTotal',dollNumber='$dollNumber',dollCode='$dollCode',addTime='$time' where id='$stu_id'";
                    $res = Yii::$app->db->createCommand($sql)->execute();
                    if($res){
                        echo '<script>alert("修改成功");location.href="index.php?r=doll/show"</script>';
                    }else{
                        echo '<script>alert("修改失败");location.href="index.php?r=doll/show"</script>';
                    }
                }
            }else{
                $sql="update doll_info set dollName='$dollName',dollTotal='$dollTotal',dollNumber='$dollNumber',dollCode='$dollCode',addTime='$time' where id='$stu_id'";
                $res = Yii::$app->db->createCommand($sql)->execute();
                if($res){
                    echo '<script>alert("修改成功");location.href="index.php?r=doll/show"</script>';
                }else{
                    echo '<script>alert("修改失败");location.href="index.php?r=doll/show"</script>';
                }
            }
        }
}