<?php

namespace console\controllers;

use Yii;

use common\models\gift\Gift;
use common\models\gift\SendRecord;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\helpers\MyFunction;
use backend\modules\erp\models\DollOrderGoods;
use yii\web\UploadedFile;
require_once("../Classes/PHPExcel.php");
/**
 * ErpController implements the CRUD actions for Gift model.
 */
class ErpController extends \yii\console\Controller
{

    public function actionUpdateOldOrderStatus() {
        $file = Yii::getAlias('@console').'/runtime/';

        //读取内容
        $model = new DollOrderGoods();
        if ($model->load(Yii::$app->request->post())) {
            $model->deliver_number = UploadedFile::getInstance($model, 'deliver_number');
            $excelInfo = $this->object2array($model->deliver_number);
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
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            for($i=2;$i<=$highestRow;$i++)
            {
                $order_number = $sheet->getCell("B".$i)->getValue();
                $deliver_number= $sheet->getCell("D".$i)->getValue();
                try{
                    $myfunction =new MyFunction();
                    $myfunction->updateOrder($order_number,$deliver_number);
                }catch(\Exception $e){

                }
            }
            return $this->redirect('/erp/order/index');
        } else {
            return $this->render('upload',[
                'model' => $model,
            ]);
        }
        //进行处理 并输出是否成功
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
