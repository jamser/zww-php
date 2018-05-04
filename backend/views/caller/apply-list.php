<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\call\Caller;
use common\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '申请列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="caller-apply-index">
    
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'avatar'=>[
                'label'=>'头像',
                'value'=>function($model) {
                    return '<img src="'.$model->user->avatar.'" width="50" height="50"/>';
                },
                'format'=>'html'
            ],
            'covers'=>[
                'label'=>'封面相册',
                'value'=>function($model) {
                    $covers = $model->getArrayFormatAttribute('covers');
                    $html = '';
                    foreach($covers as $n=>$row) {
                        $html .= '<img src="'.$row['url'].'" width="50" height="50"/>'.($n%2 ? '<br/>' : '');
                    }
                    return $html;
                },
                'format'=>'html'
            ],
            'id',
            'user_id',
            'user.username',
            'user.about',
            'user.birthday',
            [
                'label'=>'性别',
                'value'=>function($model) {
                    if($model->user->sex==1) {
                        return '男';
                    } else if($model->user->sex==2) {
                        return '女';
                    } else {
                        return '未知';
                    }
                }
            ],
            'phoneAccount.value',
            'service_time',
            [
                'label'=>'状态',
                'value'=>function($model) {
                    return Caller::$status_list[$model->status];
                }
            ],
            [
                'label'=>'申请时间',
                'value'=>function($model) {
                    return date('Y-m-d', $model->created_at);
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{review}',
                'buttons'=> [
                    'review' =>function ($url, $model, $key) {
                        return Html::a('审核', $url);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
