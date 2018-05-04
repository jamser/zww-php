<?php
namespace common\models\gift;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * Gift model
 *
 * @property integer $id
 * @property string $name 名称
 * @property integer $virtual_price 虚拟价格 
 * @property integer $send_count 发送次数
 * @property integer $created_at 创建时间
 * @property integer $updated_at 更新时间
 * @property integer $score 分值 由高到低
 * @property string $url 图片链接
 * @property integer $status 状态
 */
class Gift extends \nextrip\helpers\ActiveRecord
{
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
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%gift}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->db_php;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','virtual_price', 'url'],'required'],
            [['name', 'url'],'string', 'max'=>255, 'encoding'=>'utf-8'],
            [['name'],'unique'],
            [['send_count', 'virtual_price', 'score'],'integer'],
        ];
    }
    
    public function attributeLabels() {
        return [
            'name'=>'名称',
            'virtual_price'=>'虚拟价格',
            'send_count'=>'发送次数',
            'score'=>'分值(由高到低)',
            'url'=>'链接',
            'status'=>'状态',
        ];
    }
    
    /**
     * 获取分值最高的N件礼物
     * @param integer $num 数量
     * @return Gift[]
     */
    public static function getTop($num) {
        $db = static::getDb();
        $gifts = $db->cache(function($db) use ($num) {
            return static::find()->where(' `status`>=0 ')->orderBy('score DESC')->limit($num)->all();
        }, YII_DEBUG ? 60 : 600);
        return $gifts;
    }
}
