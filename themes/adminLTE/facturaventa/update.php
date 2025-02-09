<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Facturaventa */

$this->title = 'Editar Factura de venta No: ' . $model->idfactura;
$this->params['breadcrumbs'][] = ['label' => 'Facturas de ventas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idfactura, 'url' => ['view', 'id' => $model->idfactura]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="facturaventa-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?php
    if ($model->libre == 1){
        echo $this->render('_formlibre', [
        'model' => $model,
        'clientes' => $clientes,
        'facturastipo' => $facturastipo,
    ]);
    } else {
    echo $this->render('_form', [
        'model' => $model,
        'clientes' => $clientes,
        'ordenesproduccion' => $ordenesproduccion,
        'facturastipo' => $facturastipo,
        'sw' => $sw,
    ]);
    }
    ?>
</div>
