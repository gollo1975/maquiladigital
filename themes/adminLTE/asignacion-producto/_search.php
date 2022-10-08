<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AsignacionProductoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="asignacion-producto-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_asignacion') ?>

    <?= $form->field($model, 'idcliente') ?>

    <?= $form->field($model, 'documento') ?>

    <?= $form->field($model, 'razonzocial') ?>

    <?= $form->field($model, 'fecha_asignacion') ?>

    <?php // echo $form->field($model, 'fecha_registro') ?>

    <?php // echo $form->field($model, 'unidades') ?>

    <?php // echo $form->field($model, 'idtipo') ?>

    <?php // echo $form->field($model, 'orden_produccion') ?>

    <?php // echo $form->field($model, 'autorizado') ?>

    <?php // echo $form->field($model, 'usuario') ?>

    <?php // echo $form->field($model, 'total_orden') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
