<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProyeccionPrestacionesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="proyeccion-prestaciones-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_proyeccion') ?>

    <?= $form->field($model, 'fecha_inicio') ?>

    <?= $form->field($model, 'fecha_corte') ?>

    <?= $form->field($model, 'total_primas') ?>

    <?= $form->field($model, 'total_cesantias') ?>

    <?php // echo $form->field($model, 'total_intereses') ?>

    <?php // echo $form->field($model, 'total_vacaciones') ?>

    <?php // echo $form->field($model, 'user_name') ?>

    <?php // echo $form->field($model, 'fecha_hora_registro') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
