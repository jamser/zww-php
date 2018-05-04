<?php

namespace common\models\doll;

use Yii;

/**
 * This is the model class for table "t_doll_catch_history".
 *
 * @property integer $id
 * @property integer $doll_id
 * @property integer $member_id
 * @property string $catch_date
 * @property string $catch_status
 * @property string $video_url
 */
class CatchHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 't_doll_catch_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['doll_id', 'member_id'], 'integer'],
            [['catch_date'], 'safe'],
            [['catch_status'], 'string', 'max' => 5],
            [['video_url'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'doll_id' => 'Doll ID',
            'member_id' => 'Member ID',
            'catch_date' => 'Catch Date',
            'catch_status' => 'Catch Status',
            'video_url' => 'Video Url',
        ];
    }
}
