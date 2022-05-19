<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PlantaEmpresaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="planta-empresa-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_planta') ?>

    <?= $form->field($model, 'nombre_planta') ?>

    <?= $form->field($model, 'direccion_planta') ?>

    <?= $form->field($model, 'telefono_planta') ?>

    <?= $form->field($model, 'celular_planta') ?>

    <?php // echo $form->field($model, 'usuariosistema') ?>

    <?php // echo $form->field($model, 'fecha_registro') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
