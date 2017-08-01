<?php

namespace app\models;


/**
 * This is the model class for table "tasks".
 *
 * @property integer $id
 * @property integer $start_floor
 * @property integer $end_floor
 * @property integer $direction
 * @property integer $status_id
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
        return [
            [['start_floor', 'end_floor', 'status_id'], 'required'],
            [['start_floor', 'end_floor', 'direction', 'status_id'], 'integer'],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskStatuses::className(), 'targetAttribute' => ['status_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'start_floor' => 'Start Floor',
            'end_floor' => 'End Floor',
            'direction' => 'Direction',
            'status_id' => 'Status ID',
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