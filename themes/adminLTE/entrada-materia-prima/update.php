<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\EntradaMateriaPrima */

$this->title = 'Update Entrada Materia Prima: ' . $model->id_entrada;
$this->params['breadcrumbs'][] = ['label' => 'Entrada Materia Primas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_entrada, 'url' => ['view', 'id' => $model->id_entrada]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="entrada-materia-prima-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
