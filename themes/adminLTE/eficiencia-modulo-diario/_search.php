<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\EficienciaModuloDiarioSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="eficiencia-modulo-diario-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_eficiencia') ?>

    <?= $form->field($model, 'id_planta') ?>

    <?= $form->field($model, 'fecha_actual') ?>

    <?= $form->field($model, 'fecha_proceso') ?>

    <?= $form->field($model, 'total_eficiencia_planta') ?>

    <?php // echo $form->field($model, 'usuario_creador') ?>

    <?php // echo $form->field($model, 'usuario_editor') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
