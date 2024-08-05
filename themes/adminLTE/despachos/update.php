<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Despachos */

$this->title = 'Actualizar: ' . $model->codigo_producto;
$this->params['breadcrumbs'][] = ['label' => 'Despachos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_despacho, 'url' => ['update', 'id' => $model->id_despacho]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="despachos-update">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'conSalida' => $conSalida,
    ]) ?>

</div>
