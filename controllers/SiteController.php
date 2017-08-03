<?php

namespace app\controllers;

use app\models\Building;
use app\models\Elevator;
use app\models\Tasks;
use app\models\TasksSearch;
use Yii;
use yii\web\Controller;

class SiteController extends Controller
{

    /**
     * Lists all Tasks models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Tasks();
        if ($model->load(Yii::$app->request->post()))
        {
            if(!$model->direction) {
                $model->direction = 0;
            }
            $model->save();
        }
        $searchModel = new TasksSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
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

    public function actionTest()
    {
        set_time_limit(0);

        $building = new Building(5,4);
        $elevator = new Elevator($building, 1, 1);

        $tasks = Tasks::find()->where(['status_id' => 1])->all();
        while(count($tasks))
        {
            //get first task
            $firstTask = $tasks[0];

            //update elevator direction
            $elevator->addCall($firstTask->start_floor, $firstTask->direction);

            //move elevator to first task
            $elevator->moveTo($firstTask->start_floor);
            $elevator->loading();

            var_dump($elevator->attributes);die;
        }
        //Возвоащаем лифт в исходное состояние
        $elevator->currentDirection = null;
        $elevator->status_id = 1;
        $elevator->saveProperties();
    }

    public function actionLoadScenario($id)
    {
        Tasks::deleteAll();
        switch ($id) {
            case 1:
                $taskList = [
                    [1, 4, 2, 1],
                    [3, 2, 1, 1],
                    [4, 1, 1, 1],
                ];
                break;
            case 2:
                $taskList = [
                    [1, 4, 0, 1],
                    [3, 2, 0, 1],
                    [4, 1, 0, 1],
                ];
                break;
            default:
                $taskList = [];
                break;
        }
        Yii::$app->db->createCommand()->batchInsert('tasks', ['start_floor', 'end_floor', 'direction', 'status_id'], $taskList)->execute();

        $this->redirect('index');
    }
}
