<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Tasks */
/* @var $form yii\widgets\ActiveForm */
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
        <?= $form->field($model, 'start_floor')->textInput() ?>
    </div>

    <div class="form-group col-md-2 col-sm-6 col-xs-12">
        <?= $form->field($model, 'end_floor')->textInput() ?>
    </div>

    <div class="form-group col-md-2 col-sm-6 col-xs-12">
        <?= $form->field($model, 'direction')->dropDownList(['1' => 'Down', '2' => 'Up'], ['prompt'=>'Select direction...']) ?>
    </div>

    <?= $form->field($model, 'status_id')->hiddenInput(['value' => 1])->label(false) ?>

    <div class="form-group col-md-2 col-sm-6 col-xs-12">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
