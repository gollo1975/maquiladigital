<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrdenProduccionInsumos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="orden-produccion-insumos-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'idordenproduccion')->textInput() ?>

    <?= $form->field($model, 'idtipo')->textInput() ?>

    <?= $form->field($model, 'fecha_hora_generada')->textInput() ?>

    <?= $form->field($model, 'codigo_producto')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'orden_produccion_cliente')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'total_insumos')->textInput() ?>

    <?= $form->field($model, 'total_costo')->textInput() ?>

    <?= $form->field($model, 'user_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fecha_creada')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
