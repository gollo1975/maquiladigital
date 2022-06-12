<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ValorPrendaUnidad */

$this->title = 'Crear pago';
$this->params['breadcrumbs'][] = ['label' => 'Valor Prenda', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="valor-prenda-unidad-create">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'orden' => $orden,
    ]) ?>

</div>
