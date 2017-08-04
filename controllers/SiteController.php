<?php

namespace app\controllers;

use app\models\ExtraEvents;
use app\models\Tasks;
use app\models\TasksSearch;
use Yii;
use yii\helpers\ArrayHelper;
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
            $model->save();
            $model = new Tasks();
        }
        $searchModel = new TasksSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $stopButton = ExtraEvents::findOne(['event' => 'stop_button']);
        if(array_key_exists('stop_button', Yii::$app->request->queryParams)) {
            $stopButton->value = ArrayHelper::getValue(Yii::$app->request->queryParams, 'stop_button');
            $stopButton->save();
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'stop_button' => $stopButton,
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

    /**
     * Reload Tasks table.
     * @param $id
     */
    public function actionLoadScenario($id)
    {
        Tasks::deleteAll();
        switch ($id) {
            case 1:
                $taskList = [
                    [1, 4, 2, 1, 0, 0],
                    [3, 2, 1, 1, 0, 0],
                    [4, 1, 1, 1, 0, 0],
                ];
                break;
            case 2:
                $taskList = [
                    [1, 4, 0, 1, 0, 0],
                    [3, 2, 0, 1, 0, 0],
                    [4, 1, 0, 1, 0, 0],
                ];
                break;
            case 3:
                $taskList = [
                    [1, -110, 0, 1, 0, 0],
                    [3, 2, 0, 1, 0, 0],
                    [4, -100, 0, 1, 0, 0],
                ];
                break;
            case 4:
                $taskList = [
                    [1, 4, 0, 1, 70, 0],
                    [1, 2, 0, 1, 80, 0],
                    [2, 4, 0, 1, 110, 0],
                    [3, 1, 0, 1, 70, 0],
                    [4, 3, 0, 1, 65, 0],
                    [2, 3, 0, 1, 54, 0],
                    [1, 3, 0, 1, 92, 0],
                    [1, 4, 0, 1, 122, 0],
                    [4, 1, 0, 1, 89, 0],
                    [3, 1, 0, 1, 56, 0],
                    [2, 1, 0, 1, 70, 0],
                    [2, 4, 0, 1, 90, 0],
                ];
                break;
            case 5:
                $taskList = [
                    [1, 4, 0, 1, 0, 1],
                    [4, 1, 0, 1, 0, 1],
                    [3, 1, 0, 1, 0, 0],
                ];
                break;
            case 6:
                $taskList = [
                    [1, 4, 0, 1, 70, 0],
                    [1, 2, 0, 1, 80, 0],
                    [2, 10, 0, 1, 110, 0],
                    [3, 8, 0, 1, 70, 0],
                    [8, 1, 0, 1, 65, 0],
                    [10, 3, 0, 1, 54, 0],
                    [10, 1, 0, 1, 92, 0],
                    [10, 1, 0, 1, 122, 0],
                    [6, 3, 0, 1, 89, 0],
                    [5, 7, 0, 1, 56, 0],
                    [7, 5, 0, 1, 70, 0],
                    [1, 3, 0, 1, 90, 0],
                    [4, 10, 0, 1, 88, 0],
                    [4, 3, 0, 1, 60, 0],
                    [7, 8, 0, 1, 70, 0],
                    [9, 10, 0, 1, 55, 0],
                    [9, 3, 0, 1, 45, 0],
                    [9, 4, 0, 1, 32, 0],
                    [10, 1, 0, 1, 77, 0],
                    [2, 5, 0, 1, 69, 0],
                    [2, 3, 0, 1, 55, 0],
                    [2, 1, 0, 1, 64, 0],
                    [2, 1, 0, 1, 72, 0],
                    [2, 1, 0, 1, 71, 0],
                ];
                break;
            default:
                $taskList = [];
                break;
        }
        Yii::$app->db->createCommand()->batchInsert('tasks', ['start_floor', 'end_floor', 'direction', 'status_id', 'weight', 'vip'], $taskList)->execute();

        $this->redirect('index');
    }
}
