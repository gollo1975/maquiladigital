<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PuntoVenta */

$this->title = 'Actualizar: ' . $model->nombre_punto;
$this->params['breadcrumbs'][] = ['label' => 'Punto Ventas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_punto, 'url' => ['view', 'id' => $model->id_punto]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="punto-venta-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'sw' => $sw,
    ]) ?>

</div>
