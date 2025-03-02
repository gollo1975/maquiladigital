<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\OrdenProduccionInsumos */

$this->title = 'Create Orden Produccion Insumos';
$this->params['breadcrumbs'][] = ['label' => 'Orden Produccion Insumos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orden-produccion-insumos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
