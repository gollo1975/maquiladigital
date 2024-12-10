<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DocumentoSoporteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="documento-soporte-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_documento_soporte') ?>

    <?= $form->field($model, 'idproveedor') ?>

    <?= $form->field($model, 'id_compra') ?>

    <?= $form->field($model, 'documento_compra') ?>

    <?= $form->field($model, 'fecha_elaboracion') ?>

    <?php // echo $form->field($model, 'fecha_hora_registro') ?>

    <?php // echo $form->field($model, 'fecha_recepcion_dian') ?>

    <?php // echo $form->field($model, 'fecha_envio_api') ?>

    <?php // echo $form->field($model, 'numero_soporte') ?>

    <?php // echo $form->field($model, 'cuds') ?>

    <?php // echo $form->field($model, 'qrstr') ?>

    <?php // echo $form->field($model, 'id_forma_pago') ?>

    <?php // echo $form->field($model, 'autorizado') ?>

    <?php // echo $form->field($model, 'user_name') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
