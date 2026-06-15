<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProyeccionPrestaciones */

$this->title = 'Update Proyeccion Prestaciones: ' . $model->id_proyeccion;
$this->params['breadcrumbs'][] = ['label' => 'Proyeccion Prestaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_proyeccion, 'url' => ['view', 'id' => $model->id_proyeccion]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="proyeccion-prestaciones-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
