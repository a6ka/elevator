<?php

namespace app\controllers;

use app\models\Building;
use app\models\Elevator;
use yii\web\Controller;

class SiteController extends Controller
{

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $building = new Building(5,4);
        $elevator = new Elevator($building, 0, 1);
        var_dump($elevator->getFloorsArray());die;
        return $this->render('index');
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionAddJob($floor, $neededFloor, $direction = false){

    }
}
