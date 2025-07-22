<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PagoAdicionalPermanente */

$this->title = 'Adicional x fecha';
$this->params['breadcrumbs'][] = ['label' => 'Adicional x fecha', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pago-adicional-fecha-create">
 <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?= $this->render('_formadicion', [
        'model' => $model,
        'fecha_corte' => $fecha_corte,
        'fecha_inicio' => $fecha_inicio,
    ]) ?>

</div>
