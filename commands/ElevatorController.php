<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Building;
use app\models\Elevator;
use app\models\Tasks;
use Yii;
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
        //fix кириллица в консоле (для Windows)
//        shell_exec('chcp 65001');
        //Бесконечное время выполнения скрипта
        set_time_limit(0);

        echo "Запуск скрипта...".PHP_EOL;
        $building = new Building(Yii::$app->params['building']['floors'], Yii::$app->params['building']['floorHeight']);
        echo "Дом инициализирован...".PHP_EOL;
        $elevator = new Elevator($building, Yii::$app->params['elevator']['startFloor'], Yii::$app->params['elevator']['speed'], Yii::$app->params['elevator']['maxWeight']);
        echo "Лифт инициализирован...".PHP_EOL;
        echo "Загружаю задания...".PHP_EOL;
        echo "--------------------".PHP_EOL;

        do{
            $tasks = Tasks::find()->where(['status_id' => 1])->all();
            echo "Количество людей в ожидании: ".count($tasks).PHP_EOL;
            if(count($tasks)) {
                //get first task
                $firstTask = $tasks[0];

                //update elevator direction
                $elevator->addCall($firstTask->start_floor, $firstTask->direction);

                //move elevator to first task
                if($elevator->moveTo($firstTask->start_floor, $firstTask->vip)) {
                    //on/out persons
                    $elevator->loading();
                }

                while (count($elevator->getStopFloorsList()) || count($elevator->getReverseStopFloorsList())) {
                    if(!count($elevator->getStopFloorsList())) {
                        $elevator->changeDirection();
                    }
                    $list = $elevator->getStopFloorsList();
                    if($elevator->moveTo($list[0]['neededFloor'], $list[0]['vip'])) {
                        //on/out persons
                        $elevator->loading();
                    }
                }

            }
        }
        while(count($tasks));

        //return the elevator to its original state
        $elevator->currentDirection = null;
        $elevator->status_id = 1;
        $elevator->saveProperties();
    }
}
