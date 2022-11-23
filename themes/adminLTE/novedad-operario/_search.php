<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\NovedadOperarioSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="novedad-operario-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_novedad') ?>

    <?= $form->field($model, 'id_tipo_novedad') ?>

    <?= $form->field($model, 'id_operario') ?>

    <?= $form->field($model, 'documento') ?>

    <?= $form->field($model, 'fecha_inicio_permiso') ?>

    <?php // echo $form->field($model, 'fecha_final_permiso') ?>

    <?php // echo $form->field($model, 'hora_inicio_permiso') ?>

    <?php // echo $form->field($model, 'hora_final_permiso') ?>

    <?php // echo $form->field($model, 'fecha_registro') ?>

    <?php // echo $form->field($model, 'observacion') ?>

    <?php // echo $form->field($model, 'usuario') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
