<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tasks".
 *
 * @property integer $id
 * @property integer $start_floor
 * @property integer $end_floor
 * @property integer $direction
 * @property integer $status_id
 * @property integer $weight
 * @property integer $vip
 *
 * @property TaskStatuses $status
 */
class Tasks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $floors = [];
        for ($i = 1; $i <= Yii::$app->params['building']['floors'] ; $i++) {
            $floors[] = $i;
        }
        $floors []= -100; //First floor
        $floors []= -110; //Last floor

        return [
            [['start_floor', 'end_floor', 'status_id', 'weight'], 'required'],
            [['direction', 'status_id', 'weight', 'vip'], 'integer'],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskStatuses::className(), 'targetAttribute' => ['status_id' => 'id']],
            [['start_floor', 'end_floor'], 'in', 'range' => $floors],
            [['vip', 'direction'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'start_floor' => 'Start floor',
            'end_floor' => 'End floor',
            'direction' => 'Direction',
            'status_id' => 'Status',
            'weight' => 'Weight',
            'vip' => 'VIP',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(TaskStatuses::className(), ['id' => 'status_id']);
    }
}
