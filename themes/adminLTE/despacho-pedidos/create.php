<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DespachoPedidos */

$this->title = 'Create Despacho Pedidos';
$this->params['breadcrumbs'][] = ['label' => 'Despacho Pedidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="despacho-pedidos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
