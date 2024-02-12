<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\EntradaMateriaPrimaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="entrada-materia-prima-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_entrada') ?>

    <?= $form->field($model, 'idproveedor') ?>

    <?= $form->field($model, 'fecha_proceso') ?>

    <?= $form->field($model, 'fecha_registro') ?>

    <?= $form->field($model, 'numero_soporte') ?>

    <?php // echo $form->field($model, 'subtotal') ?>

    <?php // echo $form->field($model, 'impuesto') ?>

    <?php // echo $form->field($model, 'total_salida') ?>

    <?php // echo $form->field($model, 'autorizado') ?>

    <?php // echo $form->field($model, 'enviar_materia_prima') ?>

    <?php // echo $form->field($model, 'user_name_crear') ?>

    <?php // echo $form->field($model, 'user_name_edit') ?>

    <?php // echo $form->field($model, 'observacion') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
