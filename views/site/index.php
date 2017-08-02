<?php

use app\models\TaskStatuses;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TasksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model \app\models\Tasks */

$this->title = 'Tasks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tasks-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= $this->render('_form',[
        'model' => $model,
    ]) ?>

    <div class="clearfix"></div>
    <?php Pjax::begin(['id' => 'tasks']) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($model) {
            switch ($model->status_id) {
                case 2:
                    return ['class' => 'warning'];
                case 3:
                    return ['class' => 'success'];
                default:
                    break;
            }
            return [];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'start_floor',
            'end_floor',
            [
                'attribute' => 'direction',
                'content' => function ($data) {
                    return $data->direction === 1 ? 'Down' : 'Up';
                },
                'filter' => [1 => 'Down', 2 => 'Up'],
            ],
            [
                'attribute' => 'status_id',
                'content' => function ($data) {
                    return $data->status->status;
                },
                'filter' => ArrayHelper::map(TaskStatuses::find()->asArray()->all(), 'id', 'status'),
            ],
        ],
    ]); ?>
    <?php Pjax::end() ?>
</div>
