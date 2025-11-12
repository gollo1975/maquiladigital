<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Pedidos */

$this->title = 'Actualizar: ' . $model->cliente->nombrecorto;
$this->params['breadcrumbs'][] = ['label' => 'Pedidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_pedido, 'url' => ['update', 'id' => $model->id_pedido]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="pedidos-update">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
