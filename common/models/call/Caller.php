<?php

namespace common\models\call;

use Yii;
use common\models\User;
use common\models\user\Account;
//use yii\web\UploadedFile;
//use common\extensions\cloudStorage\CloudStorage;
//use common\eventHandlers\PcallCallerEh;

/**
 * This is the model class for table "prodcall_caller".
 *
 * @property string $id
 * @property string $user_id 用户ID
 * @property string $covers 封面相册
 * @property string $description
 * @property integer $status 
 * @property string $review_logs
 * @property string $service_time 服务时间段
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property integer $apply_time 申请时间
 * @property float $price 价格
 * @property integer $score 分数 用于排序
 * 
 * @property PcallUser $callUser 用户资料
 * @property User $user 用户资料
 */
class Caller extends \nextrip\helpers\ActiveRecord
{
    const STATUS_WAITING_FOR_REVIEW = 0;//等待审核
    const STATUS_REVIEW_PASS = 1;//通过
    const STATUS_REVIEW_REJECTED = 2;//拒绝
    
    const EVENT_AFTER_CHANGE_STATUS = 'afterChangeStatus';//在改变状态时
    
    const DEFAULT_PRICE = 6.00;//默认价格
    
    /**
     * auto cache config
     * @var array
     */
    protected static $autoCacheConfig = [
        'enable' => false,//set to false auto cache will be disabled
        'duration' => 14400,//cache duration(second)
        'useAttribute'=>'user_id',//support mixed attributes , Eg:['type', 'name']
        'cacheId'=>'cache',//cache component id
    ] ;
    
    /**
     * 封面照片ID数组
     * @var integer[] 
     */
    public $cover_ids;

    /**
     * 已选择的服务时间
     * @var array 
     */
    public $select_service_time;
    
    public static $status_list = [
        self::STATUS_WAITING_FOR_REVIEW=>'等待审核',
        self::STATUS_REVIEW_PASS=>'审核通过',
        self::STATUS_REVIEW_REJECTED=>'审核拒绝',
    ];

    public function init() {
        parent::init();
        $this->on(static::EVENT_AFTER_CHANGE_STATUS, ['\common\eventHandlers\PcallEh', 'afterChangeCallerStatus']);
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%prodcall_caller}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->db_php;
    }
    
    public function behaviors() {
        return [
            \yii\behaviors\TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['voice'], 'default', 'value'=>''],
            [['cover_ids', 'description', 'service_time'], 'required'],
            [['description', 'service_time'], 'string'],
            [['cover_ids'], 'checkCoverIds'],
        ];
    }
    
    public function afterFind() {
        parent::afterFind();
        //$this->select_service_time = json_decode($this->service_time);
    }
    
    public function checkCoverIds($attribute, $params) {
        if(!$this->cover_ids || !is_array($this->cover_ids)) {
            $this->addError($attribute, '封面ID不能为空');
            return false;
        }
        $coverIds = [];
        foreach($this->cover_ids as $coverId) {
            if($coverId>0 && !in_array((int)$coverId, $coverIds)) {
                $coverIds[] = (int)$coverId;
            }
        }
        if(!$coverIds) {
            $this->addError($attribute, '上传的照片无效');
            return false;
        }
        $images = UploadImage::find()->where(['id'=>$coverIds])->all();
        $formatCovers = [];
        foreach($images as $image) {
            if($image->userId = Yii::$app->user->id) {
                $formatCovers[] = [
                    'id'=>$image->id,
                    'url'=>$image->url,
                    'width'=>$image->width,
                    'height'=>$image->height
                ];
            }
        }
        if(!$formatCovers) {
            $this->addError($attribute, '上传的照片无效');
            return false;
        }
        $this->covers = json_encode($formatCovers);
    }

    public function upload()
    {
        if ($this->validate()) {
            $covers = [];
            foreach ($this->cover_files as $file) {
                $covers[] = $this->uploadImage($file->tempName);
                //$file->saveAs('uploads/' . $file->baseName . '.' . $file->extension);
                //上传照片到阿里云
            }
            $this->covers = json_encode($covers);
            return true;
        } else {
            return false;
        }
    }
    
    public function checkServiceTime($attribute, $params) {
        if(!$this->select_service_time) {
            $this->addError($attribute,'至少选择一个服务时间段');
            return false;
        }
        $items = PcallCaller::getAllServiceTimeItems();
        $ranges = array_keys($items);
        foreach($this->select_service_time as $time) {
            if(!in_array($time, $ranges)) {
                $this->addError ($attribute, '不在服务范围内');
                return false;
            }
        }
        $this->service_time = json_encode($this->select_service_time);
    }
    
    public function saveFile($file) {
        $path = Yii::getAlias('@frontend/web/assets/uploads');
        if(!is_dir($path)) {
            mkdir($path);
        }
        $filename = uniqid() . '.' . $file->extension;
        $file->saveAs($path.'/'.$filename);
        $imgSize = getimagesize($path.'/'.$filename);
        return [
            'url'=>'//yii2-test.com/uploads/'.$filename,
            'width'=>$imgSize[0],
            'height'=>$imgSize[1]
        ];
    }

    
    public function uploadImage($tempImageFile) {
        $imageSize = getimagesize($tempImageFile);
        if(!$imageSize) {
            $this->addError('cover_files', '获取不到图片的尺寸');
            return false;
        } else if($imageSize[0]<200) {
            $this->addError('cover_files', '图片宽度不能少于200像素');
            return false;
        } else if($imageSize[1]<200) {
            $this->addError('cover_files', '图片高度不能少于200像素');
            return false;
        }
        //保存图片到云存储
        $tempImageFileContent = file_get_contents($tempImageFile);
        $path = \common\modules\feed\models\Feed::getPictureStorePath(Yii::$app->user->id, 'jpg', md5($tempImageFileContent), randStr(16));
        $fileInfo = CloudStorage::uploadUserPicture($tempImageFile, $path);
        if($fileInfo) {
            return [
                'url'=>$fileInfo['url'],
                'width'=>$imageSize[0],
                'height'=>$imageSize[1]
            ];
        } else {
            $this->addError('cover_files', '上传图片到云服务器失败 : '.CloudStorage::getError());
            return false;
        }
        
    }
    
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'covers' => '封面图',
            'voice' => '声音',
            'description' => '描述',
            'status' => '状态',
            'review_logs' => '审核日志',
            'cover_files' => '封面图片',
            'service_time' => '服务时间段',
            'select_service_time' => '服务时间段',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    public function changeStatus($status) {
        $oldStatus = $this->status;
        $this->status = (int)$status;
        $ret = $this->updateAttributes([$status]);
        //触发事件
        if($ret) {
            $this->trigger(static::EVENT_AFTER_CHANGE_STATUS, new \nextrip\helpers\Event(['customData'=>['oldStatus'=>$oldStatus]]));
        }
    }
    
    /**
     * 获取所有服务时间时间段
     * @return array
     */
    public static function getAllServiceTimeItems() {
        $startTime = 6*3600;
        $endTime = 9*3600;
        $ranges = [];
        $current = $startTime;
        $todayTime = strtotime('today');
        while($current<$endTime) {
            $key = $current.'_'.($current+600);
            $ranges[$key] = date('H:i', $todayTime + $current).' - '.date('H:i', $todayTime + $current+600);
            $current+=600;
        }
        return $ranges;
    }
    
    public function attributeHints() {
        return [
            'service_time'=>'格式: 周一到周五 7:00-9:00 , 节假日休息'
        ];
    }
    
    public function getUser() {
        return $this->hasOne(User::className(), ['id'=>'user_id']);
    }
    
    public function getPhoneAccount() {
        $query = Account::find();
        $query->primaryModel = $this;
        $query->link = ['user_id'=>'user_id'];
        $query->multiple = false;
        $query->where('type='.Account::TYPE_PHONE);
        return $query;
    }
    
    /**
     * 获取可以预约的日期时间
     * @return array
     */
    public static function getCanBookDayTimes() {
        $todayTime = strtotime('today');
        $startDayTime = date('H')>=22 ? $todayTime + 86400 : $todayTime;
        $canSelectDays = [];
        $weekarray=array("日","一","二","三","四","五","六");
        for($i=0; $i<7; $i++) {
            $startDayTime+= 86400;
            //$dayTimes[] = $startDayTime;
            $canSelectDays[$startDayTime] = date('Y-m-d', $startDayTime)." 星期".$weekarray[date("w", $startDayTime)];
        }
        return $canSelectDays;
    }
    
    /**
     * 获取可以预约的开始时间范围
     * @return []
     */
    public static function getCanBookStartTimes() {
        $time = 3600*6;
        $times = [];
        while($time<3600*9) {
            $times[] = $time;
            $time += 600;
        }
        return $times;
    }
    
    /**
     * 获取可以预约的结束时间范围
     * @return []
     */
    public static function getCanBookEndTimes() {
        $time = 3600*6+600;
        $times = [];
        while($time<(3600*9+600)) {
            $times[] = $time;
            $time += 600;
        }
        return $times;
    }
    
    /**
     * 获取价格
     * @return float
     */
    public function getPrice() {
        return $this->price ? $this->price : self::DEFAULT_PRICE;
    }
}
