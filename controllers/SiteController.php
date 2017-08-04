<?php

namespace app\controllers;

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
            $model = new Tasks();
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
                    [1, 4, 0, 1, 0, 0],
                    [3, 2, 0, 1, 0, 0],
                    [4, 1, 0, 1, 0, 1],
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
