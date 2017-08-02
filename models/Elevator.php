<?php

namespace app\models;

use yii\base\Model;

/**
 * This is the Elevator model class.
 *
 * @property integer $id
 * @property integer $currentHeight
 * @property integer $status_id
 * @property integer $speed
 * @property array $stopFloorsList
 *
 * @property Building $building
 */
class Elevator extends Model implements ElevatorInterface
{
    public $id;
    private $currentHeight = 0;
    private $status_id;
    protected $speed; // m/s
    public $currentDirection;
    private $building;
    private $stopFloorsList;


    public function __construct(Building $building, int $startHeight, int $speed)
    {
        $this->building = $building;
        $this->currentHeight = $startHeight;
        $this->speed = $speed;
        $this->status_id = 1;
        $this->stopFloorsList = [];
    }

    public function addCall($floor, $direction = null)
    {

    }

    public function addJob($neededFloor)
    {
        // TODO: Implement addJob() method.
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
     * @param int $floor
     * @return bool
     */
    public function moveTo(int $floor){
        $startTime = microtime(true);
        $endHeight = $this->building->getFloorHeight($floor);
        echo $endHeight . PHP_EOL;

        if((int) $this->currentHeight !== (int) $endHeight) {
            $startHeight = $this->currentHeight;
            while ($this->currentHeight != $endHeight) {
                $this->currentHeight = $startHeight + floor((microtime(true)-$startTime)*$this->speed);
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public function loading(){
        sleep(5);
        return true;
    }
}