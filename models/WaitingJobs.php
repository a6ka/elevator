<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "waiting_jobs".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $floor
 * @property integer $direction
 */
class WaitingJobs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'waiting_jobs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'floor'], 'required'],
            [['created_at', 'updated_at', 'floor', 'direction'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'floor' => 'Floor',
            'direction' => 'Direction',
        ];
    }
}
