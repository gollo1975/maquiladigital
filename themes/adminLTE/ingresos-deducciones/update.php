<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\IngresosDeducciones */

$this->title = 'Actualizar';
$this->params['breadcrumbs'][] = ['label' => 'Ingresos Deducciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_ingreso, 'url' => ['view', 'id' => $model->id_ingreso]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="ingresos-deducciones-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
