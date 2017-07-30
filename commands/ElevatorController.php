<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Building;
use app\models\Elevator;
use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ElevatorController extends Controller
{
    public function actionIndex()
    {
        set_time_limit(0);

        //init building and elevator
        $building = new Building(5,4);
        $elevator = new Elevator($building, 0, 1);

        echo date('H:i:s', time())." - ". $elevator->getCurrentHeight() .PHP_EOL;
        $elevator->moveTo(2);
        echo date('H:i:s', time())." - ". $elevator->getCurrentHeight() .PHP_EOL;
    }
}
