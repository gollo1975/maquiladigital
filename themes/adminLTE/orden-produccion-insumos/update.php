<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\OrdenProduccionInsumos */

$this->title = 'Update Orden Produccion Insumos: ' . $model->id_entrega;
$this->params['breadcrumbs'][] = ['label' => 'Orden Produccion Insumos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_entrega, 'url' => ['view', 'id' => $model->id_entrega]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="orden-produccion-insumos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
