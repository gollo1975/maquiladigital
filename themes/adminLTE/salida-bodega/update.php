<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SalidaBodega */

$this->title = 'Actualizar orden: ' . $model->orden->numero_orden;
$this->params['breadcrumbs'][] = ['label' => 'Salida Bodegas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_salida_bodega, 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="salida-bodega-update">

  <!--  <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'Consulta' => $Consulta,
    ]) ?>

</div>
