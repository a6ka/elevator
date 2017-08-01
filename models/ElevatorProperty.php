<?php

namespace app\models;


/**
 * This is the model class for table "elevator_property".
 *
 * @property integer $id
 * @property double $currentHeight
 * @property double $speed
 * @property string $currentDirection
 * @property integer $status_id
 *
 * @property ElevatorStatuses $status
 */
class ElevatorProperty extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'elevator_property';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['currentHeight', 'speed', 'status_id'], 'required'],
            [['currentHeight', 'speed'], 'number'],
            [['status_id'], 'integer'],
            [['currentDirection'], 'string', 'max' => 4],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => ElevatorStatuses::className(), 'targetAttribute' => ['status_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'currentHeight' => 'Current Height',
            'speed' => 'Speed',
            'currentDirection' => 'Current Direction',
            'status_id' => 'Status ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(ElevatorStatuses::className(), ['id' => 'status_id']);
    }
}
