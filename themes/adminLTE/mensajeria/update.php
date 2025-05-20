<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Mensajeria */

$this->title = 'Actualizar: ' . $model->proveedor->nombrecorto;
$this->params['breadcrumbs'][] = ['label' => 'Mensajerias', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_codigo, 'url' => ['update', 'id' => $model->id_codigo]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="mensajeria-update">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
