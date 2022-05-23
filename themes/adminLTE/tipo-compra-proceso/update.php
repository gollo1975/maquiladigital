<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TipoCompraProceso */

$this->title = 'Update Tipo Compra Proceso: ' . $model->id_tipo_compra;
$this->params['breadcrumbs'][] = ['label' => 'Tipo Compra Procesos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_tipo_compra, 'url' => ['view', 'id' => $model->id_tipo_compra]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tipo-compra-proceso-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
