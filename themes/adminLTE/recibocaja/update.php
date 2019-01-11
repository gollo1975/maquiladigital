<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Recibocaja */

$this->title = 'Editar Recibo caja: ' . $model->idrecibo;
$this->params['breadcrumbs'][] = ['label' => 'Recibos de Cajas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idrecibo, 'url' => ['view', 'id' => $model->idrecibo]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="recibocaja-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'municipios' => $municipios,
        'tiporecibos' => $tiporecibos,
        'clientes' => $clientes,
    ]) ?>

</div>
