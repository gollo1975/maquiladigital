<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CostosGastosEmpresa */

$this->title = 'Editar servicios del ' . $model->fecha_inicio. ' al ' .$model->fecha_corte;
$this->params['breadcrumbs'][] = ['label' => 'Costos y Gastos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_costo_gasto, 'url' => ['view', 'id' => $model->id_costo_gasto]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="costos-gastos-empresa-update">

      <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'grupoPago' => $grupoPago,
        'planta' => $planta,
    ]) ?>

</div>
