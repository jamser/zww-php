<?php
namespace backend\modules\doll\controllers;

use common\services\doll\RtmpService;
use frontend\models\Doll;
use Yii;
use yii\web\Controller;

class RtmpController extends Controller{
    public $enableCsrfValidation = false;
    
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index','rtmp-index','rtmp-status','get-push-url','test'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    public function actionIndex(){
        return $this->render('index');
    }

    /**
     * 获取推流地址
     * 如果不传key和过期时间，将返回不含防盗链的url
     * @param bizId 您在腾讯云分配到的bizid
     *        streamId 您用来区别不同推流地址的唯一id
     *        key 安全密钥
     *        time 过期时间 sample 2016-11-12 12:00:00
     * @return String url */
    public function actionGetPushUrl(){
        $bizId='16787';
        $streamId = $_GET['streamId'];
        $key ="d3a006c2b8fe676c5337c3b70f894c8e";
        $time = date('Y-m-d 23:59:59',strtotime("+10 year")-24*60*60);
        if($key && $time){
            $txTime = strtoupper(base_convert(strtotime($time),10,16));
            $livecode = $bizId."_".$streamId; //直播码
            $txSecret = md5($key.$livecode.$txTime);
            $ext_str = "?".http_build_query(array(
                    "bizid"=> $bizId,
                    "txSecret"=> $txSecret,
                    "txTime"=> $txTime
                ));
        }
        $url1 = "rtmp://".$bizId.".livepush.myqcloud.com/live/".$livecode.(isset($ext_str) ? $ext_str : "");
        $url2 = "rtmp://".$bizId.".liveplay.myqcloud.com/live/".$livecode;
        $url3 =  "http://".$bizId.".liveplay.myqcloud.com/live/".$livecode.".flv";
        $url4 = "http://".$bizId.".liveplay.myqcloud.com/live/".$livecode.".m3u8";
        $urls = json_encode(array('url1'=>$url1,'url2'=>$url2,'url3'=>$url3,'url4'=>$url4));
        return $urls;
//        print_r("推流地址: ".$url1);
//        echo "<br/>";
//        print_r("播放地址（RTMP）: ".$url2);
//        echo "<br/>";
//        print_r("播放地址（FLV）: ".$url3);
//        echo "<br/>";
//        print_r("播放地址(HLS) : ".$url4);
    }

    /**
     * 获取播放地址
     * @param bizId 您在腾讯云分配到的bizid
     *        streamId 您用来区别不同推流地址的唯一id
     * @return String url */
    function getPlayUrl($streamId){
        $bizId='16787';
        $livecode = $bizId."_".$streamId; //直播码
        return array(
            "rtmp://".$bizId.".liveplay.myqcloud.com/live/".$livecode,
            "http://".$bizId.".liveplay.myqcloud.com/live/".$livecode.".flv",
            "http://".$bizId.".liveplay.myqcloud.com/live/".$livecode.".m3u8"
        );
    }

    public function actionRtmpStatus(){
        $service = new \common\services\doll\RtmpService();
        $service->machineRtmp();
    }

    public function actionRtmpIndex(){
        $db = Yii::$app->db_php;
        $machine_code = Yii::$app->getRequest()->get('machine_code',null);
        $rtmp_status = Yii::$app->getRequest()->get('rtmp_status',null);
        $conditions = $params = [];
        if($machine_code) {
            $conditions[] = '`machine_code`=:machine_code';
            $params[':machine_code'] = trim($machine_code);
        }
        if($machine_code){
            $service = new RtmpService();
            $service->oneRtmp($machine_code);
        }
        if($rtmp_status && $rtmp_status!='全部') {
            if($rtmp_status == '直播中'){
                $conditions[] = '`rtmp_status`=:rtmp_status';
                $params[':rtmp_status'] = '开启';
            }else{
                $conditions[] = '`rtmp_status`=:rtmp_status';
                $params[':rtmp_status'] = '断流';
            }
        }

        $sql = 'SELECT COUNT(*) FROM doll_rtmp'.($conditions ? ' WHERE'.implode(' AND ', $conditions) : '');
        $count = $db->createCommand($sql, $params)->queryScalar();

        $pages = new \yii\data\Pagination([
            'totalCount'=>$count
        ]);

        $offset = $pages->getOffset();
        $size = $pages->getLimit();
        $sql = "select * from doll_rtmp"
            .($conditions ? ' WHERE'.implode(' AND ', $conditions) : '')
            . "  ORDER BY create_date DESC limit $offset,$size";
        $rows = $db->createCommand($sql, $params)->queryAll();
        return $this->render('rtmp',[
            'data'=>$rows,
            'pages' => $pages,
        ]);
    }

    public function actionTest(){
        $service = new \common\services\doll\RtmpService();
        $ats = $service->rtmpStatus('devicea_1016');
        print_r($ats);
    }


}