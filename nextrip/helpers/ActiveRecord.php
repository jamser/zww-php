<?php

namespace nextrip\helpers;

use Yii;
use Exception;

class ActiveRecord extends \yii\db\ActiveRecord {
    
    /**
     * 自定义数据
     * @var array 
     */
    protected $customData = [];


    /**
     * auto cache config
     * @var array
     */
    protected static $autoCacheConfig = [
        'enable' => false,//set to false auto cache will be disabled
        'duration' => 14400,//cache duration(second)
        'useAttribute'=>'id',//support mixed attributes , Eg:['type', 'name']
        'cacheId'=>'cache',//cache component id
    ] ;

    public function updateAttributes($attributes) {
        $ret = parent::updateAttributes($attributes);
        $this->autoCacheCurrentModel();
        return $ret;
    }
    
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        $this->autoCacheCurrentModel();
        
    }

    public function afterDelete() {
        Yii::$app->{static::$autoCacheConfig['cacheId']}->delete($this->getCurrentAcCacheKey());
        return parent::afterDelete();
    }
   
    //***************************************************cache function start***********************************************

    /**
     * @param string $key enable|useAttributes|duration
     * @return mixed
     */
    public static function getAutoCacheConfig($key=null) {
        return $key===null ? static::$autoCacheConfig : static::$autoCacheConfig[$key];
    }
    
    /**
     * get auto cache key prefix
     * @return string
     */
    public static function getAcCacheKeyPrefix() {
        return '_mc:' . static::className();
    }
    
    /**
     * get auto cache key 
     * @param string|array $useAttributeValue 
     * @return string
     */
    public static function getAcCacheKey($useAttributeValue) {
        return static::getAcCacheKeyPrefix() .':'. (is_array($useAttributeValue) ? implode('_', $useAttributeValue) : $useAttributeValue);
    }

    /**
     * multi get auto cache keys 
     * @param array $useAttributeValues
     * @return array list of values corresponding to the specified keys. The array
     * is returned in terms of (key, value) pairs.
     */
    public static function getAcCacheKeys($useAttributeValues) {
        $keys = [];
        foreach ($useAttributeValues as $key => $useAttributeValue) {
            $keys[$key] = static::getAcCacheKey($useAttributeValue);
        }
        return $keys;
    }
    
    /**
     * get current model auto cache key
     * @return string
     */
    public function getCurrentAcCacheKey() {
        if (is_array(static::$autoCacheConfig['useAttribute'])) {
            $attributes = [];
            foreach (static::$autoCacheConfig['useAttribute'] as $field) {
                $attributes[] = $this->$field;
            }
            $key = static::getAcCacheKey($attributes);
        } else {
            $field = static::$autoCacheConfig['useAttribute'];
            $key = static::getAcCacheKey($this->$field);
        }
        return $key;
    }
    
    /**
     * get auto cache model
     * @param string|array $useAttributeValue see static::$autoCacheConfig['useAttribute']
     * @return static
     */
    public static function findAcModel($useAttributeValue) {
        if (static::$autoCacheConfig['enable']) {
            $attributes = Yii::$app->{static::$autoCacheConfig['cacheId']}->get(static::getAcCacheKey($useAttributeValue));
            if (!$attributes) {
                if (($record = static::findAcModelFromDb($useAttributeValue))) {
                    $record->autoCacheCurrentModel();
                } else {
                    static::autoCacheByAttributeData($useAttributeValue, []);
                }
            } else {
                if ($attributes) {
                    $record = static::instantiate($attributes);
                    static::populateRecord($record, $attributes);
                } else {
                    $record = null;
                }
            }
        } else {
            $record = static::findAcModelFromDb($useAttributeValue);
        }
        return $record;
    }
    
    /**
     * find model from db by auto cache attributes
     * @param string|array $useAttributeValue see static::$autoCacheConfig['useAttribute']
     * @return static
     */
    public static function findAcModelFromDb($useAttributeValue) {
        if (is_array($useAttributeValue)) {
            if (!is_array(static::$autoCacheConfig['useAttribute'])) {
                throw new Exception('AutoCache Error : The argument should be an array');
            }
                
            if (count($useAttributeValue) != count(static::$autoCacheConfig['useAttribute'])) {
                throw new Exception('AutoCache Error : Invalid argument $useAttributeValue.. see '.static::className().'::$autoCacheConfig');
            }
                
            $attributes = [];
            foreach (static::$autoCacheConfig['useAttribute'] as $k => $field) {
                $attributes[$field] = $useAttributeValue[$k];
            }
            $record = static::findOne($attributes);
        } else {
            if (is_array(static::$autoCacheConfig['useAttribute'])) {
                throw new Exception('AutoCache Error : The argument should be a string or an integer');
            }
                
            $record = static::findOne([static::$autoCacheConfig['useAttribute'] => $useAttributeValue]);
        }
        return $record;
    }
    
    /**
     * find all models by auto cache attributes
     * @param array $useAttributeValues  see static::$autoCacheConfig['useAttribute']
     * @reutrn static[]
     */
    public static function findAllAcModels($useAttributeValues) {
        if (static::$autoCacheConfig['enable']) {
            $cacheKeys = static::getAcCacheKeys($useAttributeValues);
            $cacheData = Yii::$app->{static::$autoCacheConfig['cacheId']}->mget($cacheKeys);
            $notExistsIds = $records = array();
            foreach ($useAttributeValues as $key => $id) {
                $cacheKey = $cacheKeys[$key];
                if (!$cacheData[$cacheKey]) {
                    $notExistsIds[$key] = $id;
                    $records[$key] = null;
                } else {
                    $model = static::instantiate($cacheData[$cacheKey]);
                    static::populateRecord($model, $cacheData[$cacheKey]);
                    $records[$key] = $model;
                }
            }

            if ($notExistsIds) {
                $notExistsRecords = static::findAllAcModelsFromDb($notExistsIds);
                foreach ($notExistsRecords as $key => $record) {
                    if ($record) {
                        $record->autoCacheCurrentModel();
                    } else {
                        static::autoCacheByAttributeData($useAttributeValues[$key], []);
                    }
                    $records[$key] = $record;
                }
            }
        } else {
            $records = static::findAllAcModelsFromDb($useAttributeValues);
        }
        return $records;
    }
    
    /**
     * find all models from db by auto cache attributes 
     * @param array $useAttributeValues 
     * @param bool $saveCache 
     * @return static[]
     */
    public static function findAllAcModelsFromDb($useAttributeValues, $saveCache=1) {
        $idsMap = $sortIds = [];
        foreach ($useAttributeValues as $key => $id) {
            $mapKey = is_array($id) ? serialize($id) : $id;
            $idsMap[$mapKey] = $key;
            $sortIds[] = $id;
        }
        if (is_array(static::$autoCacheConfig['useAttribute'])) {
            $conditions = $params = array();
            foreach ($sortIds as $key => $id) {
                if (!is_array($id)) {
                    throw new Exception('AutoCache Error : The argument should be an array');
                }
                if (count($id) != count(static::$autoCacheConfig['useAttribute'])) {
                    throw new Exception('AutoCache Error : Invalid argument $useAttributeValues.. see '.static::className().'::$autoCacheConfig');
                }
                $conditionStr = '';
                foreach (static::$autoCacheConfig['useAttribute'] as $k => $field) {
                    $params[':p_' . $key . '_' . $k] = $id[$k];
                    $conditionStr .= ($conditionStr ? ' AND ' : '') . "  `$field`=:p_{$key}_{$k}";
                }
                $conditions[] = '(' . $conditionStr . ')';
            }
            $recordArr = static::findBySql('SELECT * FROM '.static::tableName().' WHERE '.implode(' OR ', $conditions), $params)->all();
            $records = array();
            foreach ($recordArr as $record) {
                $fields = array();
                foreach (static::$autoCacheConfig['useAttribute'] as $k => $field) {
                    $fields[] = $record->$field;
                }
                $fieldsValue = serialize($fields);
                $key = $idsMap[$fieldsValue];
                $records[$key] = $record;
                if($saveCache) {
                    $record->autoCacheCurrentModel();
                }
            }
        } else {//string format 
            $recordArr = static::findAll([static::$autoCacheConfig['useAttribute'] => $sortIds]);
            $records = $return = [];
            foreach ($recordArr as $record) {
                $field = static::$autoCacheConfig['useAttribute'];
                $fieldValue = $record->$field;
                $key = $idsMap[$fieldValue];
                $records[$key] = $record;
                if($saveCache) {
                    $record->autoCacheCurrentModel();
                }
            }
            foreach ($useAttributeValues as $key => $id) {
                $return[$key] = isset($records[$key]) ? $records[$key] : null;
            }
            $records = $return;
        }
        return $records;
    }

    /**
     * 保存当前的数据到缓存当中
     */
    public function autoCacheCurrentModel() {
        if (static::$autoCacheConfig['enable']) {
            return Yii::$app->{static::$autoCacheConfig['cacheId']}->set(
                    $this->getCurrentAcCacheKey(), $this->attributes, static::$autoCacheConfig['duration']
                );
        }
        return false;
    }

    /**
     * save auto cache  by attributes array
     * @param string|array $useAttributeValue
     * @param mixed $attributes
     * @return bool
     */
    public static function autoCacheByAttributeData($useAttributeValue, $attributes) {
        if (static::$autoCacheConfig['enable']) {
            return Yii::$app->{static::$autoCacheConfig['cacheId']}->set(static::getAcCacheKey($useAttributeValue), $attributes, static::$autoCacheConfig['duration']);
        }
    }
    
    /**
     * 删除自动缓存
     * @param array $useAttributeValue
     */
    public static function delAutoCache($useAttributeValue) {
        Yii::$app->{static::$autoCacheConfig['cacheId']}->delete(static::getAcCacheKey($useAttributeValue));
    }

    //***************************************************cache function end***********************************************

    /**
     * fields 处理整型数据方法 将一些数据由字符串转换为整型
     * 在fields中调用 $this->intFields(['id', 'createAt'=>'create_at'...])
     * @param array $intAttributes
     * @return array 返回一个数组 将该数组和其他非整型数据相加即可构成fields的返回数据
     */
    public function intFields($intAttributes) {
        $fields = [];
        foreach($intAttributes as $key=>$attribute) {
            $fields[is_numeric($key) ? $attribute : $key] = function($model) use($attribute)  {return (int)$model->$attribute;};
        }
        return $fields;
    }
    
    /**
     * 获取关联的模型
     * @param string $attribute 当前的标签
     * @param string $relationName 关系名称
     * @param string $class 关联的类
     * @param array $modelMaps 如果传入该数组 会先从这个数组查找对应的值
     * @return static
     */
    public function getRelatedModel($attribute, $relationName, $class, $modelMaps=[]) {
        if(!$this->isRelationPopulated($relationName)) {
            $model = $this->{$attribute} 
                    ? (isset($modelMaps[$this->{$attribute}]) 
                        ? $modelMaps[$this->{$attribute}] 
                        : $class::findAcModel((int)$this->{$attribute}))
                    : null; 
            $this->populateRelation($relationName, $model);
            return $model;
        }
        return $this->__get($relationName);
    }
    
    /**
     * 获取数组格式的数据
     * @param string $attribute 属性
     * @param string $key 从数组中获取的值
     * @param mixed  $defaultValue 如果数组后的属性值里没有获取到$key 将返回该默认值
     * @param string $format 格式 默认为serialize  可选json
     * @return array
     */
    public function getArrayFormatAttribute($attribute, $key=null, $defaultValue=[], $format='serialize') {
        $relationName = ucfirst($attribute);
        if(!$this->isRelationPopulated($relationName)) {
            $formatData = $format==='json' ?  Format::jsonToArr($this->$attribute, true) : Format::toArr($this->$attribute);
            $this->populateRelation($relationName, $formatData);
        } else {
            $formatData = $this->__get($relationName);
        }
        return $key ? \yii\helpers\ArrayHelper::getValue($formatData, $key, $defaultValue) : ($formatData ? $formatData : $defaultValue);
    }
    
    /**
     * 获取数组格式的数据
     * @param string $attribute 属性
     * @param string $key 从数组中获取的值
     * @param mixed  $defaultValue 如果数组后的属性值里没有获取到$key 将返回该默认值
     * @param string $format 格式 默认为serialize  可选json
     * @return array
     */
    public function getJsonArrFormatAttribute($attribute, $key=null, $defaultValue=null) {
        return $this->getArrayFormatAttribute($attribute, $key, $defaultValue, 'json');
    }
    
    //处理自定义数据
    
    /**
     * 获取自定义的数据
     * @param string $key 获取的key值
     * @param mixed $defaultValue 没有设置的返回的默认值
     * @return mixed
     * @see \yii\helpers\ArrayHelper::getValue()
     */
    public function getCustomData($key, $defaultValue=null) {
        return \yii\helpers\ArrayHelper::getValue($this->customData, $key, $defaultValue);
    }
    
    /**
     * 设置自定义数据
     * @param string $key
     * @param mixed $value
     */
    public function setCustomData($key, $value) {
        $this->customData[$key] = $value;
    }
}