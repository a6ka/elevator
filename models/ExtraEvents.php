<?php

namespace app\models;


/**
 * This is the model class for table "extra_events".
 *
 * @property integer $id
 * @property string $event
 * @property integer $value
 */
class ExtraEvents extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'extra_events';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value'], 'integer'],
            [['event'], 'string', 'max' => 255],
            [['event'], 'unique'],
            [['value'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event' => 'Event',
            'value' => 'Value',
        ];
    }
}
