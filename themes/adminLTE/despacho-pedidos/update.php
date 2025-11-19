<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DespachoPedidos */

$this->title = 'Update Despacho Pedidos: ' . $model->id_despacho;
$this->params['breadcrumbs'][] = ['label' => 'Despacho Pedidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_despacho, 'url' => ['view', 'id' => $model->id_despacho]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="despacho-pedidos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
