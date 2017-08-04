<?php

use app\models\TaskStatuses;
use rmrevin\yii\fontawesome\FA;
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

    <h3>Выбор сценария</h3>
    <div class="btn-group">
        <?= Html::a('Базовые условия', ['load-scenario', 'id' => 1], ['class' => 'btn btn-default'])?>
        <?= Html::a('Без направлений', ['load-scenario', 'id' => 2], ['class' => 'btn btn-default'])?>
        <?= Html::a('Спец. кнопки', ['load-scenario', 'id' => 3], ['class' => 'btn btn-default'])?>
        <?= Html::a('Учет веса', ['load-scenario', 'id' => 4], ['class' => 'btn btn-default'])?>
        <?= Html::a('VIP таски', ['load-scenario', 'id' => 5], ['class' => 'btn btn-default'])?>

    </div>


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
            [
                'attribute' => 'end_floor',
                'content' => function ($data) {
                    switch ($data->end_floor) {
                        case -100:
                            return 'Первый этаж';
                        case -110:
                            return 'Последний этаж';
                        default:
                            return $data->end_floor;
                    }
                },
                'filter' => [0 => 'Unknown', 1 => 'Down', 2 => 'Up'],
            ],
            [
                'attribute' => 'direction',
                'content' => function ($data) {
                    return $data->direction === 1 ? 'Down' : ($data->direction === 2 ? 'Up' : 'Unknown');
                },
                'filter' => [0 => 'Unknown', 1 => 'Down', 2 => 'Up'],
            ],
            'weight',
            [
                'attribute' => 'vip',
                'content' => function ($data) {
                    return $data->vip === 1 ? FA::icon('key') : '';
                },
                'contentOptions' => ['class' => 'text-center'],
                'filter' => [0 => 'No', 1 => 'Yes'],
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
