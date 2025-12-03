<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PackingPedido */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="packing-pedido-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_pedido')->textInput() ?>

    <?= $form->field($model, 'id_despacho')->textInput() ?>

    <?= $form->field($model, 'id_transportadora')->textInput() ?>

    <?= $form->field($model, 'fecha_proceso')->textInput() ?>

    <?= $form->field($model, 'fecha_hora_registro')->textInput() ?>

    <?= $form->field($model, 'cantidad_despachadas')->textInput() ?>

    <?= $form->field($model, 'user_name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
