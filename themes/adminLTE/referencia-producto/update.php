<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ReferenciaProducto */

$this->title = 'Actualizar: ' . $model->descripcion_referencia;
$this->params['breadcrumbs'][] = ['label' => 'Referencia Productos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codigo, 'url' => ['update', 'id' => $model->codigo]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="referencia-producto-update">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'sw' => $sw,
    ]) ?>

</div>
