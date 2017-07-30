<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 30.07.17
 * Time: 14:02
 */

namespace app\models;

use yii\base\Model;

class Building extends Model
{
    private $floors;
    private $floorHeight;

    /**
     * Building constructor.
     * @param int $floors
     * @param int $floorHeight
     */
    public function __construct(int $floors, int $floorHeight)
    {
        $this->floors = $floors;
        $this->floorHeight = $floorHeight;
    }


    public function getFloorHeight(int $floor){
        if($floor <= $this->floors) {
            return ($floor-1)*$this->floorHeight;
        }
        return false;
    }
}