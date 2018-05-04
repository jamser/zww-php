<?php

namespace nextrip\wechat\helpers;

use Yii;
use yii\base\NotSupportedException;

use nextrip\wechat\models\MpMsg;
use nextrip\wechat\models\Mp;
use nextrip\wechat\models\User as WechatDbUser;
use nextrip\wechat\models\UnionId;

use common\models\User;

use WechatSdk\mp\Message;
use WechatSdk\mp\User as UserApi;
use WechatSdk\models\User as WechatUser;



/**
 * 微信消息处理类
 */
class MpMessageHandler {
    
    /**
     * @var Mp 
     */
    public $mp;
    
    /**
     * @var MpMsg
     */
    public $msg;
    
    /**
     * @var [] 
     */
    public $sourceMessage;
    
    /**
     * @var WechatDbUser 
     */
    public $wechatDbUser;
    
    
    /**
     * @var UnionId
     */
    public $unionIdRecord;
    
    protected $user;
    
    /**
     * @param Mp $wechatMp
     * @param [] $sourceMessage
     */
    public function __construct($wechatMp, $sourceMessage) {
        $this->mp = $wechatMp;
        $this->sourceMessage = $sourceMessage->toArray();
    }
    
    /**
     * 记录消息 插入SQL
     * 普通消息 使用msgid进行重排
     * 事件消息 FromUserName + CreateTime 
     * @return $this
     */
    public function recordMsg() {
        $this->msg = MpMsg::add($this->mp, $this->sourceMessage);
        return $this;
    }
    
    /**
     * 判断是否取消关注事件
     * @return bool 
     */
    public function isUnsubscribeEvent() {
        return ($this->sourceMessage['MsgType']==='event') && ($this->sourceMessage['Event']==='unsubscribe');
    }
    
    /**
     * 判断是否关注事件
     * @return bool 
     */
    public function isSubscribeEvent() {
        return ($this->sourceMessage['MsgType']==='event') && ($this->sourceMessage['Event']==='subscribe');
    }
    
    /**
     * 记录当前的用户
     * @return @this 
     */
    public function recordWechatUser() {
        if($this->isUnsubscribeEvent()) {
            //取消关注事件  获取不到用户资料 假如有记录 设置为已经取消关注状态
            $unionIdRecord = UnionId::findAcModel([$this->sourceMessage['FromUserName'], $this->mp->getConfig('appId')]);
            if($unionIdRecord && $unionIdRecord->status==UnionId::STATUS_SUBSCRIBE) {
                $unionIdRecord->status = UnionId::STATUS_UNSUBSCRIBE;
                $unionIdRecord->updateAttributes(['status']);
            }
        } else {
            $unionIdRecord = UnionId::findAcModel([$this->sourceMessage['FromUserName'], $this->mp->getConfig('appId')]);
            if($unionIdRecord) {
                $this->wechatDbUser = WechatDbUser::findAcModel($unionIdRecord->union_id);
                //由未关注改为关注
                if($unionIdRecord->status!=UnionId::STATUS_SUBSCRIBE) {
                    $unionIdRecord->status = UnionId::STATUS_SUBSCRIBE;
                    $unionIdRecord->updateAttributes(['status']);
                }
            }
            //需要向微信请求用户资料
            if(!$this->wechatDbUser) {
                try {
                    $userApi = new UserApi($this->mp->getConfig('appId'), $this->mp->getConfig('appSecret'));
                    $openUserData = $userApi->get($this->sourceMessage['FromUserName']);
                    $openUser = new WechatUser($this->mp->getConfig('appId'), $openUserData, 
                            !empty($openUserData['nickname']) || !empty($openUserData['headimgurl']) ? 1 : 0);
                    if(!$unionIdRecord) {
                        $unionIdRecord = new UnionId();
                        $unionIdRecord->setAttributes([
                            'union_id'=>$openUser->unionid ? $openUser->unionid : WechatUser::generateUnionId($openUser->openid, $openUser->getAppId()),
                            'open_id'=>$openUser->openid,
                            'app_id'=>$openUser->getAppId(),
                            'status'=>!empty($openUser->subscribe) ? UnionId::STATUS_SUBSCRIBE : UnionId::STATUS_UNSUBSCRIBE
                        ],false);
                        $unionIdRecord->save(false);
                    }
                    $this->wechatDbUser = WechatDbUser::getModelByOpenUser($openUser, $unionIdRecord);
                } catch (\WechatSdk\mp\Exception $ex) {
                    Yii::error($ex);
                }
            }
        }
        $this->unionIdRecord = $unionIdRecord;
        return $this;
    }
    
    /**
     * 获取对应的用户
     * @return User 
     */
    public function getUser() {
        if(!$this->user) {
            throw new \Exception('请完成获取用户流程');
        }
        return $this->user;
    }

    public function parse() {
        $msgType = $this->sourceMessage['MsgType'];
        $method = 'parse'.ucfirst($msgType);
        if(method_exists($this, $method)) {
            return $this->$method();
        } else {
            throw new NotSupportedException('未支持的消息类型 '.$msgType);
        }
    }
    
    /**
     * 分析事件 
     */
    public function parseEvent() {
        $event = $this->sourceMessage['Event'];
        $lowerEvent = strtolower($event);
        $parseEventFunc = 'parseEvent'.  ucfirst(str_replace('_', '', $lowerEvent));
        if(!method_exists($this, $parseEventFunc)) {
            throw new NotSupportedException("未定义事件 {$event} 的处理方式");
        }
        return $this->$parseEventFunc();
    }
    
    public function parseEventTemplatesendjobfinish() {
        
    }
    
    /**
     * 群发消息任务完成 
     * Status:为“send success”或“send fail”或“err(num)”。但send success时，也有可能因用户拒收公众号的消息、系统错误等原因造成少量用户接收失败。err(num)是审核失败的具体原因，可能的情况如下：
     * err(10001), //涉嫌广告 err(20001), //涉嫌政治 err(20004), //涉嫌社会 err(20002), //涉嫌色情 err(20006), //涉嫌违法犯罪 err(20008), //涉嫌欺诈 err(20013), //涉嫌版权 err(22000), //涉嫌互推(互相宣传) err(21000), //涉嫌其他
     */
    public function parseEventMasssendjobfinish() {
        // MASSSENDJOBFINISH
//        $massMsgId = $this->sourceMessage['MsgID'];//群发ID
//        $status = $this->sourceMessage['Status'];//状态
//        $totalCount = $this->sourceMessage['TotalCount'];//发送总数
//        $filterCount = $this->sourceMessage['FilterCount'];//过滤后的总数 准备发送的粉丝数
//        $sentCount = $this->sourceMessage['SentCount'];//发送成功的粉丝数
//        $errorCount = $this->sourceMessage['ErrorCount'];//发送失败的粉丝数
//        $record = BatchNotifyRecord::find()->where(['wechatMsgId'=>(string)$massMsgId])->one();
//        if($record) {
//            $pushResult = $record->pushResult;
//            $pushResult['status'] = $status;
//            $record->updateAttributes([
//                'pushResult'=>$pushResult,
//                'totalCount'=>$totalCount,
//                'filterCount'=>$filterCount,
//                'sentCount'=>$sentCount,
//                'errorCount'=>$errorCount
//            ]);
//            //更新发送成功的数量
//            $task = BatchNotifyTask::findOne($record->taskId);
//            $task->filterCount = BatchNotifyRecord::find()->where('taskId='.$record->taskId)->sum('filterCount');
//            $task->successCount = BatchNotifyRecord::find()->where('taskId='.$record->taskId)->sum('sentCount');
//            $task->errorCount = BatchNotifyRecord::find()->where('taskId='.$record->taskId)->sum('errorCount');
//            $task->updateAttributes(['filterCount','successCount', 'errorCount']);
//            
//            BatchNotifyLog::add($record->taskId, "微信反馈任务 {$massMsgId} 的状态为 {$status}");
//        } else {
//            Yii::error("找不到群发结果对应的记录. 收到的结果为 : ".  var_export($this->sourceMessage,1));
//        }
        
    }
    
    /**
     * 解析查看事件 
     */
    public function parseEventView() {
        
    }
    
    /**
     * 解析点击事件 
     */
    public function parseEventClick() {
        switch ($this->sourceMessage['EventKey']) {
            default:
                $response = 'success';
                break;
        }
        return $response;
    }


    /**
     * 解析关注事件 
     */
    public function parseEventSubscribe() {
        $eventKey = isset($this->sourceMessage['EventKey']) ? $this->sourceMessage['EventKey'] : null;
        switch ($eventKey) {
           default:
                $msg = $this->mp->default_welcome;
                break;
        }
        return Message::make(Message::TEXT)->setAttribute('content', $msg);
    }
    
    /**
     * 解析取消关注事件 
     */
    public function parseEventUnsubscribe() {
        return 'success';
    }
    
    /**
     * 解析扫描事件 
     */
    public function parseEventScan() {
        switch ($this->sourceMessage['EventKey']) {
            default:
                $msg = $this->mp->default_welcome;
                break;
        }
        return Message::make(Message::TEXT)->setAttribute('content', $msg);
    }
    
    /**
     * 解析上传地理位置事件 
     */
    public function parseEventLocation() {
    }
    
    /**
     * 解析扫描二维码推送事件 
     */
    public function parseEventScancodepush() {
        
    }
    
    /**
     * 解析扫描二维码等待消息事件 
     */
    public function parseEventScancodewaitmsg() {
    }
    
    /**
     * 解析弹出系统拍照事件
     */
    public function parseEventPicsysphoto() {
        
    }
    
    /**
     * 解析拍照或上传照片事件 
     */
    public function parseEventPicphotooralbum() {//pic_photo_or_album 
        
    }
    
    /**
     * 解析打开微信相册事件 
     */
    public function parseEventPicweixin() {
        
    }
    
    /**
     * 解析选择地理位置事件 
     */
    public function parseEventLocationselect() {
        
    }

    /**
     * 分析文本 
     */
    public function parseText() {
        return 'success';
    }
    
    /**
     * 分析图片 
     */
    public function parseImage() {
        return 'success';
    }
    
    /**
     * 分析视频 
     */
    public function parseVideo() {
        return 'success';
    }
    
    /**
     * 分析短视频
     */
    public function parseShortvideo() {
        return 'success';
    }
    
    /**
     * 分析地理位置
     */
    public function parseLocation() {
        return 'success';
    }
    
    /**
     * 分析链接
     */
    public function parseLink() {
        return 'success';
    }
    
    /**
     * 分析声音 
     */
    public function parseVoice() {
        return 'success';
    }
    
}

