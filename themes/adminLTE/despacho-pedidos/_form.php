<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DespachoPedidos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="despacho-pedidos-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_pedido')->textInput() ?>

    <?= $form->field($model, 'idcliente')->textInput() ?>

    <?= $form->field($model, 'fecha_despacho')->textInput() ?>

    <?= $form->field($model, 'cantidad_despachada')->textInput() ?>

    <?= $form->field($model, 'fecha_hora_registro')->textInput() ?>

    <?= $form->field($model, 'user_name')->textInput() ?>

    <?= $form->field($model, 'subtotal')->textInput() ?>

    <?= $form->field($model, 'impuesto')->textInput() ?>

    <?= $form->field($model, 'total_despacho')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
