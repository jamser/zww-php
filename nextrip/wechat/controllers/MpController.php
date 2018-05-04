<?php

namespace nextrip\wechat\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use WechatSdk\mp\Server;
use WechatSdk\mp\Menu;
use WechatSdk\mp\MenuItem;

use common\base\Response;

use nextrip\wechat\models\Mp;

/**
 * MpController implements the CRUD actions for Mp model.
 */
class MpController extends Controller
{
    /**
     * 公众账号消息处理
     * @var mixed
     */
    public $mpMsgHandlerClass = '\nextrip\wechat\helpers\MpMessageHandler';
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access'=> [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions'=>[
                            'auto-reply'
                        ],
                        'allow'=>true,
                    ],
                    [
                        'actions'=>[
                             'index', 'update', 'create', 'view', 'delete', 'set-menu', 'save-menu'
                        ],
                        'allow'=>true,
                        'roles'=>['@']
                     ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    /**
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action) {
        if($action->id==='auto-reply') {
            $this->enableCsrfValidation = false;
            Yii::$app->session->close();
        }
        
        return parent::beforeAction($action);
    }
    
    public function actionAutoReply($key) {
        if(empty(Yii::$app->params['wechatMps'][$key])) {
            throw new \Exception('未定义的微信公众号 '.$key);
        }
        $mpConfig = Yii::$app->params['wechatMps'][$key];
        $mp = Mp::findAcModel($key);
        $mp->msgHandlerClass = $this->mpMsgHandlerClass;
        Yii::info('msg handler class : '.$this->mpMsgHandlerClass);
        $server = new Server($mpConfig['appId'], $mpConfig['autoReplyToken'], $mpConfig['autoReplyEncodingAESKey']);
        return $server->on('message', [$mp, 'handlerMessage'])
                ->on('event', [$mp, 'handlerMessage'])
                ->serve();
    }

    /**
     * Lists all Mp models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Mp::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Mp model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Mp model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if(empty(Yii::$app->params['wechatMps'])) {
            throw new \Exception('请在params-local中定义 wechatMps .. 格式如 : '
                    . '"wechatMps" => [ "k1"=>["appId":"app id", "appSecret"=>"appSecret"...], "k2"=>[...] ]');
        }
        $model = new Mp();
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Mp model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Mp model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    
    public function actionSetMenu($id) {
        $mpModel = $this->findModel($id);
        
        //先获取当前的菜单
        $mpConfig = Yii::$app->params['wechatMps'][$mpModel->key];
        $menuApi = new Menu($mpConfig['appId'], $mpConfig['appSecret']);
        $cache = Yii::$app->cache;
        $cacheKey = 'wechatMpMenu'.$id;
        if( !($menus = $cache->get($cacheKey)) ) {
            try {
                $menus = $menuApi->get();
            } catch (\WechatSdk\mp\Exception $ex) {
                if($ex->getCode()===46003) {
                    $menus = [];
                } else {
                    throw new Exception($ex->getMessage());
                }
            }
            
            $cache->set($cacheKey, $menus, 7200);
        }
        
        return $this->render('setMenu',[
            'menus'=>$menus,
            'id'=>$id
        ]);
    }
    
    /**
     * 保存菜单 
     */
    public function actionSaveMenu($id) {
        $mpModel = $this->findModel($id);
        
        $mpConfig = Yii::$app->params['wechatMps'][$mpModel->key];
        $menuApi = new Menu($mpConfig['appId'], $mpConfig['appSecret']);
        
        $menuData = isset($_POST['menus']) ? $_POST['menus'] : null;
        if(true!==($validateMsg = Menu::validateData($menuData))) {
            $this->error(ErrorCode::E_PARAM_INVALID, $validateMsg);
        }
        $menuItems = Menu::items($menuData);
        //对设置的数据进行验证
        try {
            $menuApi->set($menuItems);
        } catch (Exception $ex) {
            $this->error(ErrorCode::E_PARAM_INVALID, '设置菜单错误 : '.$ex->getMessage());
        }
        $cache = Yii::$app->cache;
        $cacheKey = 'wechatMpMenu'.$id;
        $cache->delete($cacheKey);
        return Response::success();
    }

    /**
     * Finds the Mp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Mp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Mp::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
