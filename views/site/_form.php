<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Tasks */
/* @var $form yii\widgets\ActiveForm */

$startFloorList = [];
for ($i = 1; $i <= Yii::$app->params['building']['floors'] ; $i++) {
    $startFloorList[$i] = "$i этаж";
}
$endFloorList = ArrayHelper::merge($startFloorList, ['-100' => 'Первых этаж', '-110' => 'Последний этаж']);

?>

<div class="tasks-form">

    <?php Pjax::begin(['id' => 'new_task']) ?>
    <h3>Добавить задание</h3>
    <?php $form = ActiveForm::begin([
        'options' => ['data-pjax' => true],
        'id' => 'task-form',
        'fieldConfig' => [
            'options' => [
                'tag' => false,
            ],
        ],
    ]); ?>

    <div class="form-group col-md-2 col-sm-6 col-xs-12">
        <?= $form->field($model, 'start_floor')->dropDownList($startFloorList, ['prompt'=>'Select floor...']) ?>
    </div>

    <div class="form-group col-md-2 col-sm-6 col-xs-12">
        <?= $form->field($model, 'end_floor')->dropDownList($endFloorList, ['prompt'=>'Select floor...']) ?>
    </div>

    <div class="form-group col-md-2 col-sm-6 col-xs-12">
        <?= $form->field($model, 'direction')->dropDownList(['1' => 'Down', '2' => 'Up'], ['prompt'=>'Select direction...']) ?>
    </div>

    <div class="form-group col-md-2 col-sm-6 col-xs-12">
        <?= $form->field($model, 'weight')->textInput(['value' => 70]) ?>
    </div>

    <div class="form-group col-md-2 col-sm-6 col-xs-12 tasks__form-group">
        <?= $form->field($model, 'vip')->checkbox(['value' => 1]) ?>
    </div>

    <?= $form->field($model, 'status_id')->hiddenInput(['value' => 1])->label(false) ?>

    <div class="form-group col-md-2 col-sm-6 col-xs-12 tasks__button-group">
        <?= Html::submitButton('Add task', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
