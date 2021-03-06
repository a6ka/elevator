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
/* @var $stop_button \app\models\ExtraEvents */

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
        <?= Html::button('10 этажей', ['class' => 'btn btn-default', 'data-toggle' => 'modal', 'data-target' => '#myModal'])?>
    </div>


    <?= $this->render('_form',[
        'model' => $model,
    ]) ?>

    <div class="clearfix"></div>

    <h3>Экстра кнопки</h3>
    <div class="btn-group mb-30">
        <?= Html::a($stop_button->value ? 'CANCEL STOP' : 'STOP!', ['index', 'stop_button' => ($stop_button->value ? 0 : 1)], ['class' => $stop_button->value ? 'btn btn-success' : 'btn btn-danger'])?>
    </div>

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

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Внимание!</h4>
            </div>
            <div class="modal-body">
                <p>Прежде чем загрузить данный сценарий отредактируйте параметры здания в файле <code>config/params.php</code></p>
            </div>
            <div class="modal-footer">
                <p>Вы уже изменили параметры?</p>
                <button type="button" class="btn btn-link" data-dismiss="modal">Нет</button>
                <?= Html::a('Да', ['load-scenario', 'id' => 6], ['class' => 'btn btn-success'])?>
            </div>
        </div>

    </div>
</div>