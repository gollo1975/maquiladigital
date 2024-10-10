<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Resolucion */

$this->title = 'Editar Resolución: ' . $model->nroresolucion;
$this->params['breadcrumbs'][] = ['label' => 'Resoluciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idresolucion, 'url' => ['view', 'id' => $model->idresolucion]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="resolucion-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
