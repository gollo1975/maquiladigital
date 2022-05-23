<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CostosGastosEmpresaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="costos-gastos-empresa-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_costo_gasto') ?>

    <?= $form->field($model, 'fecha_inicio') ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'fecha_corte') ?>

    <?= $form->field($model, 'total_costo_gasto') ?>

    <?php // echo $form->field($model, 'fecha_proceso') ?>

    <?php // echo $form->field($model, 'usuariosistema') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
