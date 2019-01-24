<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\OrdenProduccion */

$this->title = 'Editar Orden de Producción: ' . $model->idordenproduccion;
$this->params['breadcrumbs'][] = ['label' => 'Ordenes de Producción', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idordenproduccion, 'url' => ['view', 'id' => $model->idordenproduccion]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="banco-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
		'clientes' => $clientes,
        'ordenproducciontipos' => $ordenproducciontipos,
        'codigos' => $codigos,
    ]) ?>

</div>
