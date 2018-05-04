<?php
namespace common\services\doll;

use Yii;

class ElasticsearchService extends \common\services\ElasticsearchBaseService{
    public static function all(){
        $host=['127.0.0.1:9200'];
        $client = \Elasticsearch\ClientBuilder::create()->setHosts($host)->build();
        $query =
            [
                'query' =>
                    [
                        'range' =>
                            [
                                'date' =>
                                    [
                                        'gte' => '2018-03-01'
                                    ]
                            ]
                    ],
            ];
        $params =[
            'index' => 'game-logs',
            'body' => $query
        ];
        $res = $client->search($params);
        return $res;
    }

    public static function allSearch($start,$end,$size,$from){
        $host=['127.0.0.1:9200'];
        $client = \Elasticsearch\ClientBuilder::create()->setHosts($host)->build();
        $query =
            [
                'query' =>
                    [
                        'range' =>
                            [
                                'date' =>
                                    [
                                        'gte' => $start,
                                        'lte' => $end
                                    ]
                            ]
                    ],
                'sort' =>
                    [
                        'date' => [
                            'order' => 'desc'
                        ]
                    ]
            ];
        $params =[
            'index' => 'game-logs',
            'size'=>$size,
            'from'=>$from,
            'body' => $query
        ];
        $res = $client->search($params);
        return $res;
    }

    public static function typeSearch($type,$size,$from){
        $host=['127.0.0.1:9200'];
        $client = \Elasticsearch\ClientBuilder::create()->setHosts($host)->build();
        $query =
            [
                'query' =>
                    [
                        'bool' =>
                            [
                                'must' =>
                                    [
                                        'match' =>
                                            [
                                                'log_type' => $type,
                                            ]
                                    ]
                            ]
                    ],
                'sort' =>
                    [
                        'date' => [
                            'order' => 'desc'
                        ]
                    ]
            ];
        $params =[
            'index' => 'game-logs',
            'size'=>$size,
            'from'=>$from,
            'body' => $query
        ];
        $res = $client->search($params);
        return $res;
    }

    public static function dateSearch($date,$size,$from){
        $host=['127.0.0.1:9200'];
        $client = \Elasticsearch\ClientBuilder::create()->setHosts($host)->build();
        $query =
            [
                'query' =>
                    [
                        'bool' =>
                            [
                                'must' =>
                                    [
                                        'match' =>
                                            [
                                                'date' => $date,
                                            ]
                                    ]
                            ]
                    ],
                'sort' =>
                    [
                        'date' => [
                            'order' => 'desc'
                        ]
                    ]
            ];
        $params =[
            'index' => 'game-logs',
            'size'=>$size,
            'from'=>$from,
            'body' => $query
        ];
        $res = $client->search($params);
        return $res;
    }


    public static function insert(){
        $host=['127.0.0.1:9200'];
        $client = \Elasticsearch\ClientBuilder::create()->setHosts($host)->build();
        $body =
            [
                'title'=>'测试日志23',
                'name'=>'日志名称',
                'user_name'=>'用户名称',
                'class_name'=>'类名',
                'function_name'=>'方法名',
                'log_type'=>'正常日志',
                'content'=>'日志内容内容内容',
                'date'=>'2018-03-23'
            ];
        $params =[
            'index' => 'game-logs',
            'type'=>'2018-03',
            'id'=>'23',
            'body' => $body
        ];
        $res = $client->create($params);
        return $res;
    }



    //分页查询
    public static function pageSearch($pageSize,$page){
        $host=['127.0.0.1:9200'];
        $client = \Elasticsearch\ClientBuilder::create()->setHosts($host)->build();
        $query =
            [
                'size'=>$pageSize,
                'query' =>
                    [
                        'bool' =>
                            [
                                'must' =>
                                    [
                                        'match' =>
                                            [
                                                'date' => '',
                                            ]
                                    ]
                            ]
                    ],
                'sort' =>
                [
                    'date' => [
                        'order' => 'desc'
                    ]
                ]
            ];
        $params =[
            'index' => 'game-logs',
            'body' => $query
        ];
        $res = $client->search($params);
        return $res;
    }
}