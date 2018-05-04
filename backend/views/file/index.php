<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\File;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '文件管理';
?>
<div class="file-index" id="file_index">
    <div class="mb20">
        <a href="/file/create" class="btn btn-default">添加文件</a>
        <a href="#" class="btn btn-default" id="btn_search_toggle">打开搜索</a>
        <?php echo $this->render('_search', ['model' => $searchModel, 'url'=>\yii\helpers\Url::current()]); ?>
    </div>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            [
                'label'=>'type',
                'value'=>function($model) {
                    return File::getTypeName($model->type);
                }
            ],
            'name',
            [
                'label'=>'URL',
                'format'=>'raw',
                'value'=>function($model) {
                    return '<a href="'.\common\helpers\Cdn::getAuthUrl($model->url).'">'.$model->url.'</a>';
                },
            ],
            // 'data',
            [
                'label'=>'MP3文件',
                'format'=>'raw',
                'value'=>function($model) {
                    if(getUrlExt($model->url)==='mp4') {
                        if( ($mp3 = $model->getArrayFormatAttribute('data', 'mp3')) ) {
                            return 'ID：'.Html::a($mp3['id'], '/file/view?id='.$mp3['id'],['target'=>'_blank'])
                                .'&nbsp;&nbsp;URL: '.Html::a($mp3['url'], \common\helpers\Cdn::getAuthUrl($mp3['url']), ['target'=>'_blank'])
                                    .(!$model->getArrayFormatAttribute('data', 'mp3Job') ? '' : Html::button('获取转换结果', ['type'=>'button', 'class'=>'btn btn-default btn-mp3job-query margin5']))
                                    .Html::button('重新提交任务', ['type'=>'button', 'class'=>'btn btn-default submit-again btn-mp3job-submit margin5']);
                        } else if( ($mp3Job = $model->getArrayFormatAttribute('data', 'mp3Job')) ) {
                            return '已提交转换任务 '
                                .Html::button('获取转换结果', ['type'=>'button', 'class'=>'btn btn-default btn-mp3job-query margin5'])
                                .Html::button('重新提交任务', ['type'=>'button', 'class'=>'btn btn-default submit-again btn-mp3job-submit margin5']);
                        } else {
                            return Html::button('提交转换任务', ['type'=>'button', 'class'=>'btn btn-default btn-mp3job-submit margin5']);
                        }
                    } else if(getUrlExt($model->url)==='mp3') {
                        if( ($fromFile = (int)$model->getArrayFormatAttribute('data', 'fromFile')) ) {
                            return '来自文件ID：'.Html::a($fromFile, '/file/view?id='.$fromFile,['target'=>'_blank']);
                        }
                    } else {
                        return '不可生成';
                    }
                }
            ],
            [
                'label'=>'created_at',
                'value'=>function($model) {
                    return date('Y-m-d H:i:s', $model->created_at);
                }
            ],
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view} {update} {snapshot}',
                'buttons'=>[
                    'snapshot'=>function($url, $model, $index) {
                        return getUrlExt($model->url)!=='mp4'? '' : Html::a('截图', '#', ['class'=>'btn btn-default btn-snapshot']);
                    }
                ]
            ],
        ],
    ]); ?>
</div>

<div class="modal fade" id="snapshot_dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">视频截图</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label class="control-label">生成文件名称</label>
                        <input type="text" class="form-control" id="snapshot_name"/>
                    </div>
                    <div class="form-group">
                        <label class="control-label">截取毫秒数</label>
                        <input type="hidden" id="snapshot_file_id"/>
                        <input type="number" class="form-control" id="snapshot_ms"/>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary btn-submit-snapshot">提交</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
<?php 
$this->beginBlock('pageScript');?>
$('#btn_search_toggle').on('click', function(){
    if($('#search_form').hasClass('hidden')) {
        $(this).html('关闭搜索');
    } else {
        $(this).html('打开搜索');
    }
    $('#search_form').toggleClass('hidden');
});
$('#file_index').on('click', '.btn-mp3job-query', function(e){
    var id = $(this).closest('tr').data('key');
    //查询状态
    $.ajax({
        url:'/apiv1/file/mp3-job-query',
        data:{id:id},
        dataType:'json',
        type:'GET',
        error:function() {
            FlashMsg.error('网络错误..');
        },
        success:function(r) {
            if(r.code==='0|OK') {
               FlashMsg.success('生成MP3成功!  文件ID:'+r.result.id+'; MP3 url:'+r.result.url+'; 刷新页面可查看到对应的文件', 5000);
            } else {
                FlashMsg.error(r.msg);
            }
        }
    });
}).on('click', '.btn-mp3job-submit', function(e){
    if($(this).hasClass('submit-again') && !confirm('确定要重新提交任务吗?')) {
        return false;
    }
    var id = $(this).closest('tr').data('key');
    //提交任务
    $.ajax({
        url:'/apiv1/file/mp3-job-submit',
        data:{id:id},
        dataType:'json',
        type:'GET',
        error:function() {
            FlashMsg.error('网络错误..');
        },
        success:function(r) {
            if(r.code==='0|OK') {
                window.location.reload();
            } else {
                FlashMsg.error(r.msg);
            }
        }
    });
}).on('click', '.btn-snapshot', function(e){
    $('#snapshot_dialog').modal('show');
    var id = $(this).closest('tr').data('key');
    $('#snapshot_file_id').val(id);
});

$('#snapshot_dialog').on('click', '.btn-submit-snapshot', function(e){
    e.preventDefault();
    
    var snapshotMs = $('#snapshot_ms').val();
    var snapshotFileId = $('#snapshot_file_id').val();
    var snapshotName = $('#snapshot_name').val();
    if(!snapshotMs || !snapshotFileId || !snapshotName) {
        FlashMsg.error('截图的文件ID和毫秒数不能为空..');
        return false;
    }
    
     $('#snapshot_dialog').modal('hide');
    
    //提交任务
    $.ajax({
        url:'/apiv1/file/snapshot',
        data:{id:snapshotFileId, ms:snapshotMs, name:snapshotName},
        dataType:'json',
        type:'GET',
        error:function() {
            FlashMsg.error('网络错误..');
        },
        success:function(r) {
            if(r.code==='0|OK') {
                FlashMsg.success('提交截图任务成功!  任务ID:'+r.result.id+'; 请前往截图任务查看并获取任务结果', 5000);
            } else {
                FlashMsg.error(r.msg);
            }
        }
    });
});
<?php
$this->endBlock();
$this->registerJs($this->blocks['pageScript']);
?>
</script>