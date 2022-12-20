<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ComprobanteEgresoTipo */

$this->title = 'Nuevo documento';
$this->params['breadcrumbs'][] = ['label' => 'Tipos de comprobantes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comprobante-egreso-tipo-create">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
