<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * Setting model
 *
 * @property integer $id
 * @property string $key 名称
 * @property string $value 值
 * @property string $description 描述
 * @property integer $created_at 创建时间
 * @property integer $updated_at 更新时间
 */
class Setting extends \nextrip\helpers\ActiveRecord
{
    /**
     * auto cache config
     * @var array
     */
    protected static $autoCacheConfig = [
        'enable' => false,//set to false auto cache will be disabled
        'duration' => 14400,//cache duration(second)
        'useAttribute'=>'key',//support mixed attributes , Eg:['type', 'name']
        'cacheId'=>'cache',//cache component id
    ] ;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%setting}}';
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
            [['description'], 'default', 'value'=>''],
            [['key','value'],'required'],
            [['key','value'],'string'],
            [['key'],'unique'],
        ];
    }
    
    /**
     * 通过KEY获取值 
     * @param string $key 设置KEY
     * @return string
     */
    public static function getValueByKey($key) {
        $model = static::findAcModel($key);
        return $model ? $model->value : null;
    }
}
