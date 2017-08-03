<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * This is the Elevator model class.
 *
 * @property string $elevator_name
 * @property integer $currentHeight
 * @property integer $currentDirection
 * @property integer $status_id
 * @property integer $speed
 * @property array $stopFloorsList
 *
 * @property Building $building
 */
class Elevator extends Model implements ElevatorInterface
{
    public $elevator_name;
    public $currentHeight = 0;
    public $status_id;
    public $speed; // m/s
    public $currentDirection;
    public $persons_number = 0;

    private $building;
    private $stopFloorsList;


    public function __construct(Building $building, int $startFloor, int $speed)
    {
        parent::__construct();
        $this->building = $building;
        $this->currentHeight = $building->getFloorHeight($startFloor);
        $this->speed = $speed;
        $this->status_id = 1;
        $this->stopFloorsList = [];
        $this->elevator_name = $this->generateUniqueRandomString('elevator_name');

        $this->saveProperties();
    }

    public function saveProperties()
    {
        if(!$model = ElevatorProperty::findOne(['elevator_name' => $this->elevator_name])) {
            $model = new ElevatorProperty();
        }
        $model->attributes = $this->attributes;
        return $model->save();
    }

    public function addCall($floor, $direction = null)
    {
        $this->stopFloorsList []= $floor;
        $this->currentDirection = $direction;
        $this->saveProperties();
    }

    public function pressButton($button)
    {
        // TODO: Implement pressButton() method.
    }

    /**
     * @param int $floor
     * @return bool
     */
    public function moveTo(int $floor){
        $startTime = microtime(true);
        $endHeight = $this->building->getFloorHeight($floor);

        //update elevator status
        if($this->currentHeight < $endHeight) {
            $this->status_id = 2;
        } elseif ($this->currentHeight > $endHeight) {
            $this->status_id = 3;
        }
        $this->saveProperties();

        if((int) $this->currentHeight !== (int) $endHeight) {
            $startHeight = $this->currentHeight;
            while ($this->currentHeight != $endHeight) {
                $prevHeight = $this->currentHeight;
                $this->currentHeight = $startHeight + floor((microtime(true)-$startTime)*$this->speed);
                if($prevHeight !== $this->currentHeight) {
                    $this->saveProperties();
                }
            }
        }
        $this->deleteFromStopFloorsList($floor);
        return true;
    }

    /**
     * @return bool
     */
    public function loading()
    {
        //update elevator status
        $this->status_id = 4;

        //person out action
        $personsOut = Tasks::find()->where(['status_id' => 2, 'end_floor' => $this->getElevatorFloor()])->all();
        foreach ($personsOut as $person) {
            $person->status_id = 3;
            $person->save();
            $this->persons_number--;
        }

        //person in action
        $personsOut = Tasks::find()->where(['status_id' => 1, 'start_floor' => $this->getElevatorFloor()])->all();
        foreach ($personsOut as $person) {
            $person->status_id = 2;
            $person->save();
            $this->persons_number++;
            //add unique person destination to stopFloorsList
            $this->addToStopFloorsList($person->end_floor);
        }

        $this->saveProperties();
//        sleep(3);
        return true;
    }

    /**
     * @return int
     */
    public function getCurrentHeight(){
        return $this->currentHeight;
    }

    /**
     * @return string
     */
    public function getStatus(){
        return $this->status;
    }

    /**
     * @param $attribute
     * @param int $length
     * @return string
     */
    protected function generateUniqueRandomString($attribute, $length = 32) {
        $randomString = Yii::$app->getSecurity()->generateRandomString($length);

        if(!ElevatorProperty::findOne([$attribute => $randomString]))
            return $randomString;
        else
            return $this->generateUniqueRandomString($attribute, $length);
    }

    /**
     * @return array
     */
    public function getStopFloorsList()
    {
        return $this->stopFloorsList;
    }

    /**
     * @return int|null
     */
    public function getElevatorFloor()
    {
        $floor = $this->currentHeight/$this->building->floorHeight + 1;
        if(is_int($floor)) {
            return (int) $floor;
        }
        return null;
    }

    /**
     * @param int $floor
     * @return bool
     */
    private function deleteFromStopFloorsList(int $floor)
    {
        if(($key = array_search($floor, $this->stopFloorsList)) !== false) {
            unset($this->stopFloorsList[$key]);
            return true;
        }
        return false;
    }

    /**
     * @param int $floor
     * @return bool
     */
    private function addToStopFloorsList(int $floor)
    {
        if(!ArrayHelper::isIn($floor, $this->stopFloorsList)) {
            array_push($this->stopFloorsList, $floor);
            return true;
        }
        return false;
    }
}