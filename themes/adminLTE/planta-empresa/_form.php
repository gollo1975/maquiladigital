<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PlantaEmpresa */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="planta-empresa-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nombre_planta')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'direccion_planta')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'telefono_planta')->textInput() ?>

    <?= $form->field($model, 'celular_planta')->textInput() ?>

    <?= $form->field($model, 'usuariosistema')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fecha_registro')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
