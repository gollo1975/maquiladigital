<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PackingPedido */

$this->title = 'Update Packing Pedido: ' . $model->id_packing;
$this->params['breadcrumbs'][] = ['label' => 'Packing Pedidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_packing, 'url' => ['view', 'id' => $model->id_packing]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="packing-pedido-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
