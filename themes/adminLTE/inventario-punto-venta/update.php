<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\InventarioPuntoVenta */

$this->title = 'Actualizar: ' . $model->nombre_producto;
$this->params['breadcrumbs'][] = ['label' => 'Inventario Punto Ventas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_inventario, 'url' => ['update', 'id' => $model->id_inventario]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="inventario-punto-venta-update">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'sw' => 1,
    ]) ?>

</div>
