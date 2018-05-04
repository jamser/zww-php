<?php

namespace nextrip\article\models;

use Yii;

/**
 * This is the model class for table "article".
 *
 * @property string $id
 * @property string $user_id
 * @property integer $type
 * @property string $title
 * @property string $content
 * @property string $view_count
 * @property integer $comment_count
 * @property string $created_at
 * @property string $updated_at
 */
class Article extends \nextrip\helpers\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    public function behaviors() {
        return [
            \yii\behaviors\TimestampBehavior::className()
        ];
    }
    
    public function beforeValidate() {
        $this->user_id = (int)Yii::$app->user->id;
        return parent::beforeValidate();
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['user_id', 'type', 'view_count', 'comment_count'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'type' => '类型',
            'title' => '标题',
            'content' => '内容',
            'view_count' => '查看次数',
            'comment_count' => '评论数量',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * 获取文章关联的组件
     */
    public function getComponents() {
        
    }
}
