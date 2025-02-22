<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DocumentoSoporte */

$this->title = 'Actualizar: ' . $model->proveedor->nombrecorto;
$this->params['breadcrumbs'][] = ['label' => 'Documento Soportes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_documento_soporte, 'url' => ['view', 'id' => $model->id_documento_soporte]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="documento-soporte-update">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'sw' => $sw,
        'Token' => $Token,
        'Acceso' => $Acceso,
        'resolucion' => $resolucion,
    ]) ?>

</div>
