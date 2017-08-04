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
 * @property integer $persons_number
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

    /**
     * Save elevator properties to DB
     * @return bool
     */
    public function saveProperties()
    {
        if(!$model = ElevatorProperty::findOne(['elevator_name' => $this->elevator_name])) {
            $model = new ElevatorProperty();
        }
        $model->attributes = $this->attributes;
        return $model->save();
    }

    /**
     * Add new elevator call task
     * @param $floor
     * @param null $direction
     * @return bool
     */
    public function addCall($floor, $direction = 0)
    {
        echo "Поступил вызов лифта с ".$floor." этажа!".PHP_EOL;
        $this->stopFloorsList []= $floor;
        $this->currentDirection = $direction;
        $this->saveProperties();
        return true;
    }

    /**
     * @param $button
     * @return bool
     */
    public function pressButton($button)
    {
        echo "В кабине нажата кнопка: ".$button.PHP_EOL;
        //if press floor number
        if(is_int($button)) {
            //validate floor number (between 1 and max floor). If not - ignore the signal
            if($button <= $this->building->floors && $button > 0) {
                $this->addToStopFloorsList($button);
            }
        }
        return true;
    }

    /**
     * @param int $floor
     * @return bool
     */
    public function moveTo(int $floor){
        echo "Принял команду перемещения на ".$floor." этаж".PHP_EOL;
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
            //move up
            if($this->status_id === 2) {
                while ((int)$this->currentHeight !== (int)$endHeight) {
                    $prevHeight = (int) $this->currentHeight;
                    $this->currentHeight = $startHeight + floor((microtime(true)-$startTime)*$this->speed);
                    if( $prevHeight !== (int) $this->currentHeight) {
                        if($currentFloor = $this->getElevatorFloor()) {
                            echo "Проезжаю ".$currentFloor." этаж".PHP_EOL;
                            $tasks = Tasks::find()->where(['status_id' => 1, 'start_floor' => $currentFloor])->all();
                            if(count($tasks)) {
                                echo "На этаже обнаружена жизнь! Подбираю!".PHP_EOL;
                                break;
                            }
                        }
                        $this->saveProperties();
                    }
                }
            //move down
            } else {
                while ((int)$this->currentHeight !== (int)$endHeight) {
                    $prevHeight = $this->currentHeight;
                    $this->currentHeight = $startHeight - floor((microtime(true)-$startTime)*$this->speed);
                    if((int) $prevHeight !== (int) $this->currentHeight) {
                        if($currentFloor = $this->getElevatorFloor()) {
                            echo "Проезжаю ".$currentFloor." этаж".PHP_EOL;
                            $tasks = Tasks::find()->where(['status_id' => 1, 'start_floor' => $currentFloor])->all();
                            if(count($tasks)) {
                                echo "На этаже обнаружена жизнь! Подбираю!".PHP_EOL;
                                break;
                            }
                        }
                        $this->saveProperties();
                    }
                }
            }
        }
        if((int)$this->currentHeight === (int)$this->building->getFloorHeight($floor)){
            echo "Прибыл на ".$floor." этаж".PHP_EOL;
            $this->deleteFromStopFloorsList($floor);
        }

        return true;
    }

    /**
     * @return bool
     */
    public function loading()
    {
        echo "Открыл двери".PHP_EOL;
        //update elevator status
        $this->status_id = 4;

        //person out action
        $personsOut = Tasks::find()->where(['status_id' => 2, 'end_floor' => $this->getElevatorFloor()])->all();
        foreach ($personsOut as $personOut) {
            $personOut->status_id = 3;
            $personOut->save();
            $this->persons_number--;
            echo "Высаживаю пассажира. Количество людей в лифте: ".$this->persons_number.PHP_EOL;
        }

        //person in action
        $personsIn = Tasks::find()->where(['status_id' => 1, 'direction' => $this->currentDirection,'start_floor' => $this->getElevatorFloor()])->all();
        foreach ($personsIn as $personIn) {
            $personIn->status_id = 2;
            $personIn->save();
            $this->persons_number++;
            echo "Новый пассажир. Количество людей в лифте: ".$this->persons_number.PHP_EOL;
            //add unique person destination to stopFloorsList
            $this->pressButton($personIn->end_floor);
        }
        //sort stop floors
        $this->sortStopFloorsList();
        $this->saveProperties();
//        sleep(3);
        echo "Закрыл двери".PHP_EOL;
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
        if(!($floor - floor($floor))) {
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

    /**
     * @return bool
     */
    private function sortStopFloorsList(){
        if($this->currentDirection === 1) {
            rsort($this->stopFloorsList,SORT_NUMERIC);
        } else {
            sort($this->stopFloorsList,SORT_NUMERIC);
        }
        return true;
    }
}