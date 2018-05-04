<?php

namespace common\services\call;

use Yii;
use common\services\BaseService;
use common\models\call\Caller;

/**
 * Caller排序
 */
class CallerSortService extends BaseService {
    
    public function __construct($config = array()) {
        parent::__construct($config);
    }
    
    /**
     * 获取pass
     * @param integer $offset 偏移值
     * @param integer $limit 数量
     * @return []
     */
    public function getPass($offset, $limit) {
        $db = Caller::getDb();
        $cacheDependency = new \yii\caching\TagDependency(['tags'=>'caller_pass_count']);
        $count = $db->cache(function($db){
            return Caller::find()->where('`status`='.Caller::STATUS_REVIEW_PASS)->count();
        }, 3600, $cacheDependency);
        $callers = Caller::find()->where('`status`='.Caller::STATUS_REVIEW_PASS)
                ->limit($limit)->offset($offset)->all();
        return [
            'count'=>$count,
            'callers'=>$callers
        ];
    }
}