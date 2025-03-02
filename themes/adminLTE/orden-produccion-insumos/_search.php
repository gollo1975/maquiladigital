<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrdenProduccionInsumosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="orden-produccion-insumos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_entrega') ?>

    <?= $form->field($model, 'idordenproduccion') ?>

    <?= $form->field($model, 'idtipo') ?>

    <?= $form->field($model, 'fecha_hora_generada') ?>

    <?= $form->field($model, 'codigo_producto') ?>

    <?php // echo $form->field($model, 'orden_produccion_cliente') ?>

    <?php // echo $form->field($model, 'total_insumos') ?>

    <?php // echo $form->field($model, 'total_costo') ?>

    <?php // echo $form->field($model, 'user_name') ?>

    <?php // echo $form->field($model, 'fecha_creada') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
