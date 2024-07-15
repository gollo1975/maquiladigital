<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SalidaBodega */

$this->title = 'Nueva salida';
$this->params['breadcrumbs'][] = ['label' => 'Salida Bodegas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salida-bodega-create">

   <!-- <h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'ConReferencia' => $ConReferencia,
    ]) ?>

</div>
