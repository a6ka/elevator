<?php

namespace app\models;


/**
 * This is the model class for table "elevator_statuses".
 *
 * @property integer $id
 * @property string $status
 *
 * @property ElevatorProperty[] $elevatorProperties
 */
class ElevatorStatuses extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'elevator_statuses';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'required'],
            [['status'], 'string', 'max' => 50],
            [['status'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getElevatorProperties()
    {
        return $this->hasMany(ElevatorProperty::className(), ['status_id' => 'id']);
    }
}
