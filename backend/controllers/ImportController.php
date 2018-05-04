<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\models\PublishAssetForm;
include_once('../Classes/PHPExcel.php');
include_once('../Classes/PHPExcel/IOFactory.php');//静态类
class ImportController extends Controller
{
    public $enableCsrfValidation = false;
    public $layout = false;

    public function actionImport(){
        return $this->render('import');
    }
    public function actionImport_do()
    {
/**  导入 Excel  **/
            $tmp = $_FILES['file_excel']['tmp_name'];    //  文件 类型
            $tmp_name = $_FILES['file_excel']['name']; // 文件名称
//print_r($tmp_name);die;
            $file_type = strtolower(substr($tmp_name,strrpos($tmp_name,'.')+1));//得到文件类型，并且都转化成小写
            $exceldata      = $this->readExcel($tmp,$file_type);
            unset($exceldata[0]);
//            print_r($exceldata);die;
            foreach($exceldata as $key => $link)
           {
                $sql = "insert into customer_order(customer_order,customer_name,waybill_number,order_status,order_type,status_introduce,hair_company,send_company,hair_address_person,send_address_person,goods_info,count_weight,goods_price,freight,hair_phone,send_phone,order_date,gathering_place,large,smarty) values('$link[1]','$link[2]','$link[3]','$link[4]','$link[5]','$link[6]','$link[7]','$link[8]','$link[9]','$link[10]','$link[11]','$link[12]','$link[13]','$link[14]','$link[15]','$link[16]','$link[17]','$link[18]','$link[19]','$link[20]')";
//               echo $sql;die;
               $data = Yii::$app->db->createCommand($sql)->execute();
//               print_r($data);die;
           }
               if($data){
                   echo "<script>alert('导入数据成功');location.href='?r=import/show'</script>";
               }else{
                   echo "<script>alert('导入数据成功');location.href='?r=import/show'</script>";
               }

    }
/*  引用类文件  识别Excel 文件内容 */
   public function readExcel($path,$file_type)
   {
    //引用PHPexcel 类
//    $type = 'Excel2007';//设置为Excel5代表支持2003或以下版本，Excel2007代表2007版
    switch ($file_type) {
        case 'xls':
            $type = 'Excel5';
            break;
        case 'xlsx':
            $type = 'Excel2007';
            break;
    }
    $xlsReader = \PHPExcel_IOFactory::createReader($type);
    $xlsReader->setReadDataOnly(true);
    $xlsReader->setLoadSheetsOnly(true);
    $Sheets = $xlsReader->load($path);
    //开始读取上传到服务器中的Excel文件，返回一个二维数组
    $dataArray = $Sheets->getSheet(0)->toArray();
//    print_r($dataArray);die;
    return $dataArray;
    }
//    展示导入的数据
    public function actionShow(){
       $sql = "select * from customer_order";
       $data = Yii::$app->db->createCommand($sql)->queryAll();
        return $this->render('importShow',['data'=>$data]);
    }
}