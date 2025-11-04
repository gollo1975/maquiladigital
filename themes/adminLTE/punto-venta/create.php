<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PuntoVenta */

$this->title = 'Nuevo punto';
$this->params['breadcrumbs'][] = ['label' => 'Punto Ventas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="punto-venta-create">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'sw' => $sw,
    ]) ?>

</div>