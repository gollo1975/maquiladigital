<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProyeccionPrestaciones */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="proyeccion-prestaciones-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fecha_inicio')->textInput() ?>

    <?= $form->field($model, 'fecha_corte')->textInput() ?>

    <?= $form->field($model, 'total_primas')->textInput() ?>

    <?= $form->field($model, 'total_cesantias')->textInput() ?>

    <?= $form->field($model, 'total_intereses')->textInput() ?>

    <?= $form->field($model, 'total_vacaciones')->textInput() ?>

    <?= $form->field($model, 'user_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fecha_hora_registro')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
