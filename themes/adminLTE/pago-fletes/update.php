<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PagoFletes */

$this->title = 'Actualizar: ' . $model->proveedor->nombrecorto;
$this->params['breadcrumbs'][] = ['label' => 'Pago Fletes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_pago, 'url' => ['update', 'id' => $model->id_pago]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="pago-fletes-update">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
