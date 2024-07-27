<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PedidoCliente */

$this->title = 'Actualizar: ' . $model->cliente->nombrecorto;
$this->params['breadcrumbs'][] = ['label' => 'Pedido Clientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_pedido, 'url' => ['update', 'id' => $model->id_pedido]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="pedido-cliente-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
