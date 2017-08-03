<?php

namespace app\models;


/**
 * This is the model class for table "elevator_property".
 *
 * @property integer $id
 * @property string $elevator_name
 * @property double $currentHeight
 * @property double $speed
 * @property integer $currentDirection
 * @property integer $status_id
 * @property integer $persons_number
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
            [['currentHeight', 'speed', 'status_id', 'elevator_name', 'persons_number'], 'required'],
            [['currentHeight', 'speed'], 'number'],
            [['status_id', 'persons_number', 'currentDirection'], 'integer'],
            [['elevator_name'], 'string'],
            [['elevator_name'], 'unique'],
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
            'elevator_name' => 'Elevator name',
            'currentHeight' => 'Current height',
            'speed' => 'Speed',
            'currentDirection' => 'Current direction',
            'status_id' => 'Status',
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
