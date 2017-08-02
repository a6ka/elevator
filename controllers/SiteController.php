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
        $tasks = Tasks::find()->where(['status_id' => 1])->all();
        $model = new Tasks();
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
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

    public function actionAddJob($floor, $neededFloor, $direction = false){

    }
}
