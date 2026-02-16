<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\IngresoPersonalContrato */

$this->title = 'Update Ingreso Personal Contrato: ' . $model->id_ingreso;
$this->params['breadcrumbs'][] = ['label' => 'Ingreso Personal Contratos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_ingreso, 'url' => ['view', 'id' => $model->id_ingreso]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ingreso-personal-contrato-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
