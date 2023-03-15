<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PagoBancoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pago-banco-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_pago_banco') ?>

    <?= $form->field($model, 'nit_empresa') ?>

    <?= $form->field($model, 'id_banco') ?>

    <?= $form->field($model, 'tipo_pago') ?>

    <?= $form->field($model, 'aplicacion') ?>

    <?php // echo $form->field($model, 'secuencia') ?>

    <?php // echo $form->field($model, 'fecha_creacion') ?>

    <?php // echo $form->field($model, 'fecha_aplicacion') ?>

    <?php // echo $form->field($model, 'descripcion') ?>

    <?php // echo $form->field($model, 'usuario') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
