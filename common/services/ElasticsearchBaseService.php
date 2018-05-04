<?php
namespace common\services;

use yii\base\Object;

class ElasticsearchBaseService extends Object
{
    public $where;

    public static function getHits($query)
    {
        if ($query['timed_out'] === false && $query['hits']['total'] !== 0) {
            return $query['hits']['hits'];
        }else{
            return false;
        }
    }

    public static function getAggs($query)
    {
        if ($query['timed_out'] === false && $query['_shards']['total'] > 0) {
            return $query['aggregations'];
        }else{
            return false;
        }
    }

    public static function toArray($params)
    {
        if (is_array($params)) {
            return $params;
        }else{
            return [(string)$params];
        }
    }

    public static function rooms($query)
    {
        return array_column($query[2]['buckets'], 'key');
    }



}