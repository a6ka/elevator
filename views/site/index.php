<?php

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
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'start_floor',
            'end_floor',
            'direction',
            'status_id',
        ],
    ]); ?>
    <?php Pjax::end() ?>
</div>
