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
 * @property integer $maxWeight
 * @property integer $currentWeight
 * @property integer $persons_number
 * @property array $stopFloorsList
 * @property array $reverseStopFloorsList
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
    public $maxWeight = 0;
    public $currentWeight = 0;

    private $building;
    private $stopFloorsList = [];
    private $reverseStopFloorsList = [];


    public function __construct(Building $building, int $startFloor, int $speed, int $maxWeight = 0)
    {
        parent::__construct();
        $this->building = $building;
        $this->currentHeight = $building->getFloorHeight($startFloor);
        $this->speed = $speed;
        $this->maxWeight = $maxWeight;
        $this->status_id = 1;
        $this->elevator_name = $this->generateUniqueRandomString('elevator_name');

        $this->saveProperties();
    }

    /**
     * Finds the ElevatorProperty based on its name value.
     * @return null | ElevatorProperty the loaded model
     */
    protected function findProperties()
    {
        return ElevatorProperty::findOne(['elevator_name' => $this->elevator_name]);
    }

    /**
     * Save elevator properties to DB
     * @return bool
     */
    public function saveProperties()
    {
        if(!$model = $this->findProperties()) {
            $model = new ElevatorProperty();
        }
        $model->attributes = $this->attributes;
        return $model->save();
    }

    /**
     * Add new elevator call task
     * @param $floor
     * @param integer $direction
     * @return bool
     */
    public function addCall($floor, $direction = 0)
    {
        echo "Поступил вызов лифта с ".$floor." этажа!".PHP_EOL;
        $taskStop = [
            'neededFloor' => $floor,
            'vip' => 0,
        ];
        $this->stopFloorsList []= $taskStop;
        if($direction) {
            $this->currentDirection = $direction;
        }
        $this->saveProperties();
        return true;
    }

    /**
     * @param int $button
     * @param int $vip
     * @return bool
     */
    public function pressButton(int $button, int $vip = 0)
    {
        echo "В кабине нажата кнопка: ".$button.PHP_EOL;
        if($vip) {
            echo "Барина везем! Пристегните ремни!".PHP_EOL;
        }
        //if press service button
        switch ($button) {
            case -100:
                $button = 1;
                break;
            case -110:
                $button = $this->building->floors;
                break;
            default:
                break;
        }

        //validate floor number (between 1 and max floor). If not - ignore the signal
        if($button <= $this->building->floors && $button > 0) {
            $neededDirection = 1; //Down
            if($button > $this->getElevatorFloor()) {
                $neededDirection = 2; //Up
            }
            $taskStop = [
                'neededFloor' => $button,
                'vip' => $vip,
            ];
            if($this->currentDirection === $neededDirection) {
                $this->addToStopFloorsList($taskStop);
            } else {
                $this->addToReverseStopFloorsList($taskStop);
            }

        }
        return true;
    }

    /**
     * @param int $floor
     * @param int $vip
     * @return bool
     */
    public function moveTo(int $floor, int $vip = 0){
        echo "Принял команду перемещения на ".$floor." этаж".PHP_EOL;
        $startTime = microtime(true);
        $endHeight = $this->building->getFloorHeight($floor);

        //update elevator status
        if($this->currentHeight < $endHeight) {
            $this->currentDirection = 2;
            $this->status_id = 2;
        } elseif ($this->currentHeight > $endHeight) {
            $this->currentDirection = 1;
            $this->status_id = 3;
        }
        $this->saveProperties();

        if((int) $this->currentHeight !== (int) $endHeight) {
            $startHeight = $this->currentHeight;
            //save current elevator status
            $currentStatus = $this->status_id;
            //move up
            if($this->status_id === 2) {
                while ((int)$this->currentHeight !== (int)$endHeight) {
                    //STOP button
                    if(ExtraEvents::findOne(['event' => 'stop_button'])->value) {
                        echo "СТОП!".PHP_EOL;
                        $this->status_id = 5;
                        $this->saveProperties();
                    }
                    while (ExtraEvents::findOne(['event' => 'stop_button'])->value) {
                        sleep(.3);
                        $startTime = microtime(true);
                    }
                    if($this->status_id === 5) {
                        echo "ПОЕХАЛИ!".PHP_EOL;
                        $this->status_id = $currentStatus;
                        $this->saveProperties();
                    }

                    $prevHeight = (int) $this->currentHeight;
                    $this->currentHeight = $startHeight + floor((microtime(true)-$startTime)*$this->speed);
                    if( $prevHeight !== (int) $this->currentHeight) {
                        if($currentFloor = $this->getElevatorFloor()) {
                            echo "Проезжаю ".$currentFloor." этаж".PHP_EOL;
                            if(!$vip) {
                                $newTasks = Tasks::find()->where([
                                    'status_id' => 1,
                                    'start_floor' => $currentFloor,
                                    'direction' => [0, $this->currentDirection],
                                ])->all();
                                $currentOutTasks = Tasks::find()->where([
                                    'status_id' => 2,
                                    'end_floor' => $currentFloor,
                                ])->all();
                                if(count($newTasks) && ($this->currentWeight < $this->maxWeight || count($currentOutTasks))) {
                                    echo "На этаже обнаружена жизнь! Подбираю!".PHP_EOL;
                                    break;
                                }
                            }
                        }
                        $this->saveProperties();
                    }
                }
            //move down
            } else {
                while ((int)$this->currentHeight !== (int)$endHeight) {
                    //STOP button
                    if(ExtraEvents::findOne(['event' => 'stop_button'])->value) {
                        echo "СТОП!".PHP_EOL;
                        $this->status_id = 5;
                        $this->saveProperties();
                    }
                    while (ExtraEvents::findOne(['event' => 'stop_button'])->value) {
                        sleep(.3);
                        $startTime = microtime(true);
                    }
                    if($this->status_id === 5) {
                        echo "ПОЕХАЛИ!".PHP_EOL;
                        $this->status_id = $currentStatus;
                        $this->saveProperties();
                    }

                    $prevHeight = $this->currentHeight;
                    $this->currentHeight = $startHeight - floor((microtime(true)-$startTime)*$this->speed);
                    if((int) $prevHeight !== (int) $this->currentHeight) {
                        if($currentFloor = $this->getElevatorFloor()) {
                            echo "Проезжаю ".$currentFloor." этаж".PHP_EOL;
                            if(!$vip) {
                                $newTasks = Tasks::find()->where([
                                    'status_id' => 1,
                                    'start_floor' => $currentFloor,
                                    'direction' => [0, $this->currentDirection],
                                ])->all();
                                $currentOutTasks = Tasks::find()->where([
                                    'status_id' => 2,
                                    'end_floor' => $currentFloor,
                                ])->all();
                                if(count($newTasks) && ($this->currentWeight < $this->maxWeight || count($currentOutTasks))) {
                                    echo "На этаже обнаружена жизнь! Подбираю!".PHP_EOL;
                                    break;
                                }
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

        //persons OUT
        $personsOut = Tasks::find()->where(['status_id' => 2, 'end_floor' => $this->getElevatorFloor()])->all();
        foreach ($personsOut as $personOut) {
            $personOut->status_id = 3;
            $personOut->save();
            $this->persons_number--;
            $this->currentWeight -= $personOut->weight;
            echo "Высаживаю пассажира. Количество людей в лифте: ".$this->persons_number.PHP_EOL;
        }

        //persons IN
        $personsIn = Tasks::find()->where(['status_id' => 1,'start_floor' => $this->getElevatorFloor()])->all();
        foreach ($personsIn as $personIn) {
            if(($this->currentWeight + $personIn->weight) <= $this->maxWeight){
                $personIn->status_id = 2;
                switch ($personIn->end_floor) {
                    case -100:
                        $personIn->end_floor = 1;
                        break;
                    case -110:
                        $personIn->end_floor = $this->building->floors;
                        break;
                    default:
                        break;
                }
                $personIn->save();
                $this->persons_number++;
                $this->currentWeight += $personIn->weight;
                echo "Новый пассажир. Количество людей в лифте: ".$this->persons_number.PHP_EOL;
                //add unique person destination to stopFloorsList
                $this->pressButton($personIn->end_floor, $personIn->vip);
            } else {
                echo "Пассажир слишком пухлый. Подождет следующего раза }:-D".PHP_EOL;
            }

        }
        //sort stop floors
        $this->sortStopFloorsList();
        $this->saveProperties();
        sleep(3);
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
        $neededKey = -1;
        foreach ($this->stopFloorsList as $key => $item) {
            if($item['neededFloor'] === $floor) {
                $neededKey = $key;
                break;
            }
        }
        if($neededKey !== -1) {
            unset($this->stopFloorsList[$neededKey]);
            return true;
        }
        return false;
    }

    /**
     * @param array $taskStop
     * @return bool
     */
    private function addToStopFloorsList(array $taskStop)
    {
        if(!ArrayHelper::isIn($taskStop, $this->stopFloorsList)) {
            array_push($this->stopFloorsList, $taskStop);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    private function sortStopFloorsList(){
        if($this->currentDirection === 1) {
            ArrayHelper::multisort($this->stopFloorsList, ['vip', 'neededFloor'], [SORT_DESC, SORT_DESC]);
        } else {
            ArrayHelper::multisort($this->stopFloorsList, ['vip', 'neededFloor'], [SORT_DESC, SORT_ASC]);
        }
        return true;
    }

    /**
     * @param array $taskStop
     * @return bool
     */
    private function addToReverseStopFloorsList(array $taskStop)
    {
        if(!ArrayHelper::isIn($taskStop, $this->reverseStopFloorsList)) {
            array_push($this->reverseStopFloorsList, $taskStop);
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getReverseStopFloorsList()
    {
        return $this->reverseStopFloorsList;
    }

    /**
     * @return bool
     */
    public function changeDirection()
    {
        $this->stopFloorsList = $this->reverseStopFloorsList;
        $this->reverseStopFloorsList = [];
        if($this->currentDirection === 1) {
            $this->currentDirection = 2;
        } else {
            $this->currentDirection = 1;
        }
        $this->saveProperties();
        $this->sortStopFloorsList();
        return true;
    }
}